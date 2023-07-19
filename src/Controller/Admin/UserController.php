<?php

namespace App\Controller\Admin;

use App\Entity\UploadedDocument;
use App\Entity\User;
use App\Form\UserFormType as UserFormType;
use App\FormFilter\UserFilterType;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use App\Service\FileUploader;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/admin/users")
 */
class UserController extends Controller
{
   private $translator;
   private $em;

  
    public function __construct(TranslatorInterface $translator, EntityManagerInterface $em)
    {
        $this->translator = $translator;
        $this->em = $em;

    }
  
    /**
     * My account page
     *
     *
     * @Route("/myaccount", name="user_myaccount",  methods="GET")
     *
     *
     * @return Response
     */
    public function myAccount(MessageRepository $messageRepository)
    {
        $user = $this->getUser();
        $userMessages = $messageRepository->getUserMessages($user->getId());
        $userType = strtolower(str_replace('ROLE_', '', $this->getUser()->getRoles()[0]));
        //dump($userType);die;
        $form = $this->createForm(UserFormType::class, $user, ['userType' => $userType, 'isNew' => false]);
        return $this->render('admin/user/show.html.twig', [
            'user' => $user,
            'userType' => $userType,
            'form' =>  $form->createView(),
            'userMessages' => $userMessages
        ]);
    }
    
    /**
     * @Route("/{userType}", name="user_index", methods="GET|POST", requirements={"userType": "waiting_validation|user|owner|manager|admin"})
     *
     */
    public function indexAction(UserRepository $userRepository, $userType, Request $request, PaginatorInterface $paginator): Response
    {
        $session = $request->getSession();
        $filterData = [];
        $filterForm = $this->createForm(UserFilterType::class);

        if ('reset' == $request->get('filter_action')) { // Reset filter
            $session->remove('userFilter');

            return $this->redirect($this->generateUrl('user_index', ['userType' => $userType]));
        }


        if ('filter' == $request->get('filter_action')) { // Filter action
            $filterForm->handleRequest($request); // Bind values from the request
            if ($filterForm->isSubmitted()) {
                if ($filterForm->has('slug')) {
                    $filterData['slug'] = $filterForm->get('slug')->getData();
                }

                $session->set('userFilter', $filterData); // Save filter to session
            }
        } elseif ($session->has('userFilter')) {
            $filterData = $session->get('userFilter');
            $filterForm = $this->createForm(UserFilterType::class, $filterData, ['data_class' => null]);
            if (array_key_exists('slug', $filterData)) {
                $filterForm->get('slug')->setData($filterData['slug']);
            }
        }

        //prise en compte type utiliateur
        $filterData['role'] = $role = 'ROLE_'.strtoupper($userType);

        $per_page = $this->getParameter('app.per_page_global');

        $query = $userRepository->search($filterData);
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1)/* page number */,
            $request->query->getInt('per_page', $per_page), /* limit per user */
            ['defaultSortFieldName' => 'u.slug', 'defaultSortDirection' => 'asc']
        );

        return $this->render('admin/user/index.html.twig', [
          'pagination' => $pagination,
          'search_form' => $filterForm->createView(),
          'role' => $role,
          'userType' => $userType,
          ]);
    }

    /**
     * @Route("/new/{userType}", name="user_new", methods="GET|POST")
     */
    public function newAction(Request $request, $userType): Response
    {
        $user = new User();
        $role = 'ROLE_'.strtoupper($userType);
        $user->addRole($role);

        $form = $this->createForm(UserFormType::class, $user, ['userType' => $userType, 'isNew' => true]);
        // copy the value of email in field login
        if ($request->request->has('user_form')) {
            $userForm = $request->get('user_form');
            $userForm['login'] = $userForm['email'];
            $request->request->set('user_form', $userForm);
        }
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->updateUserSlug($user);
            $this->addFlash('success', 'Generics.flash.addSuccess');
            if (!$request->request->has('createAndContinue')) {
                return $this->redirectToRoute('user_index', ['userType' => $userType, 'isNew' => true]);
            } else {
                $user = new User();
                $form = $this->createForm(UserFormType::class, $user, ['userType' => $userType, 'isNew' => true]);
            }
        }
        
        return $this->render('admin/user/new.html.twig', [
                'user' => $user,
                'form' => $form->createView(),
                'userType' => $userType,
        ]);
    }
    
    /**
     * Finds and displays a User entity.
     *
     *
     * @param User $user
     * @Route("/{id}/validate", name="user_validate",  methods="GET")
     *
     *
     * @return Response
     */
    public function validateAction(User $user, TranslatorInterface $translator, \Swift_Mailer $mailer)
    {
        $em = $this->getDoctrine()->getManager();
        $user->addRole('ROLE_USER');
        $user->removeRole('ROLE_WAITING_VALIDATION');
        $em->persist($user);
        $em->flush();
        $this->updateUserSlug($user);
        $this->addFlash('success', 'Entities.User.flashes.userValidationSuccess');
        // send mail to user to inform that his account is valide
        $message = (new \Swift_Message($translator->trans('Security.messages.yourAccountisValidate')))
            ->setFrom($this->getParameter('app.genericMail'))
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    'emails/validationAccount.html.twig',
                    compact('user')
                ),
                'text/html'
            )
        ;
        $mailer->send($message);
        
        return $this->redirectToRoute('user_index', ['userType' => 'waiting_validation']);
    }

    /**
     * @Route("/{userType}/{id}/edit", name="user_edit", methods="GET|POST")
     */
    public function editAction(Request $request, $userType, User $user, FileUploader $fileUploader): Response
    {
        $form = $this->createForm(UserFormType::class, $user, ['userType' => $userType, 'isNew' => false]);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->has('picture') && $form['picture']->getData()) {
                $uploadedFile = $form['picture']->getData();
                $fileUploader->setTargetDirectory('users');
                $uploadedDoc = $fileUploader->upload($uploadedFile, $user);
            }
            $user->setSlug(''); // to force slug refresh
            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->flush();
            
            $this->updateUserSlug($user);
            
            $this->addFlash('success', 'Generics.flash.editSuccess');

            return $this->redirectToRoute('user_show', ['userType' => $userType, 'id' => $user->getId()]);
        }

        return $this->render('admin/user/edit.html.twig', [
                'object' => $user,
                'form' => $form->createView(),
                'userType' => $userType,
        ]);
    }
    
    /**
     * Change type and role of on user
     *
     *
     * @param User $user
     * @Route("/{id}/change-role", name="user_changeRole",  methods="POST")
     *
     *
     * @return Response
     */
    public function changeRoleAction(Request $request, User $user)
    {
        // gestion des anciens rôles
        $user->resetRoles();
        
        // gestion du nouveau rôle
        $new_role = $request->request->get('newRole');
        $user->addRole($new_role);
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        
        $this->addFlash('success', 'Entities.User.flashes.changeRoleSuccess');

        $userType = strtolower(str_replace('ROLE_', '', $new_role));
        
        return $this->redirectToRoute('user_show', ['userType' => $userType, 'id' => $user-> getId()]);
    }
    
    /**
     * Delete a user entity.
     *
     * @param int $id
     * @Route("/{userType}/{id}/delete", name="user_delete",  methods="GET")
     *
     * @return Response
     */
    public function deleteAction($userType, $id, UserRepository $userRepository)
    {
        $ids = [$id];
        $userRepository->batchDelete($ids);
        $this->addFlash('success', 'Suppression effectuée avec succès');

        return $this->redirectToRoute('user_index', ['userType' => $userType]);
    }
    
    /**
     * Finds and displays a User entity.
     *
     * @param String   $userType
     * @param User $user
     * @Route("/{userType}/{id}", name="user_show",  methods="GET")
     *
     *
     * @return Response
     */
    public function showAction($userType, User $user, MessageRepository $messageRepository)
    {
        $userMessages = $messageRepository->getUserMessages($user->getId());
        $form = $this->createForm(UserFormType::class, $user, ['userType' => $userType, 'isNew' => false]);
        return $this->render('admin/user/show.html.twig', [
            'user' => $user,
            'userType' => $userType,
            'form' =>  $form->createView(),
            'userMessages' => $userMessages
        ]);
    }
    
    /**
     * Batch action for BusinessState entity.
     *
     * @param Request $request
     * @Route("/batch", name="user_batch",  methods="POST")
     *
     * @return Response
     */
    public function batchAction(Request $request, UserRepository $userRepository)
    {
        $ids = $request->request->get('ids');
        if ($ids) {
          if ('batchDelete' === $request->request->get('batch_action')) {
                  $userRepository->batchDelete($ids);
                  $this->addFlash('success', 'Les éléments selectionnés ont été supprimés');
              }

          if ('batchExport' === $request->request->get('batch_action')) {
              return $this->exportUsers($userRepository, $ids);
          }
        }
        $userType = $request->query->get('userType');

        return $this->redirectToRoute('user_index', ['userType' => $userType]);
    }
    
    /**
     * Delete a photo entity.
     *
     * @param int $id
     * @Route("/photo/{id}/delete", name="admin_user_delete_photo",  methods="GET")
     *
     * @return Response
     */
    public function deletePhotoAction(UploadedDocument $photo, Request $request)
    {
        $userId = $photo->getUser()->getId();
        if ($this->isCsrfTokenValid('delete_photo' . $photo->getId(), $request->query->get('_token'))) {
            $this->getDoctrine()->getManager()->remove($photo);
            $this->getDoctrine()->getManager()->flush();
            $filename = $this->getParameter('picture_directory') . '/users/' . $userId . '/' . $photo->getFileName();

            if (file_exists($filename)) {
                unlink($filename);
            } else {
                $this->addFlash('warning', 'Impossible de supprimer le fichier');
            }
            $this->addFlash('success', 'Suppression effectuée avec succès');
        } else {
            $this->addFlash('error', 'CSRF Token Invalid');
        }



        return $this->redirectToRoute('admin_user_edit', ['id' => $userId]);
    }
    
    private function exportUsers(UserRepository $userRepository,$usersIds): Response
    {
        
        $users = $userRepository->findBy(['id'=>$usersIds]);
        $now = new \DateTime;
        $rows = ['Nom;Prénom;Rôles;Structure;Courriel;Tél fixe; Tél portable;Adresse;'];
        /** @var Registration $registration */
        foreach($users as $user) {
            $roleString = '';
            $separator = '';
            foreach($user->getRoles() as $role){
              $roleString .= $separator.$this->translator->trans('Entities.User.roles.'.$role);
              $separator = '-';
            }
            $data = 
                [
                  $user->getLastName(),
                  $user->getFirstName(),
                  $roleString,
                  $user->getStructureName(),
                  $user->getEmail(),
                  $user->getCoordinate()?$user->getCoordinate()->getPhone():'',
                  $user->getCoordinate()?$user->getCoordinate()->getMobilePhone():'',
                  $user->getCoordinate()?$user->getCoordinate()->getFormatedAddress():'',

                ];
            $rows[] = implode(';', $data);
        }
        
        $content = implode("\n", $rows);
        
        $response = new Response($content);
        //$response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Type', 'application/force-download');
        $response->headers->set('Cache-Control', 'no-cache');
        $slugify = new Slugify();
        $filename = $slugify->slugify('export utilisateurs '.$now->format('d-m-Y H:i:s'));
        //$response->headers->set('Content-disposition', 'attachment; filename='.$filename.'.csv');
        $response->headers->set('Content-Disposition', $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT, "$filename.csv"
        ));

        return $response;
        
    }
    
    private function updateUserSlug(User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $slugify = new Slugify();
        $slug = $slugify->slugify($user->__toString());
        $user->setSlug($slug);
        $em->persist($user);
        $em->flush();
    }
}
