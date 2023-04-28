<?php

namespace App\Controller\Admin;

use App\Entity\Discussion;
use App\Entity\Message;
use App\FormFilter\DiscussionFilterType;
use App\FormFilter\MessageFilterType;
use App\Repository\DiscussionRepository;
use App\Repository\MessageRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/admin/discussion")
 */
class DiscussionController extends AbstractController
{

    
    /**
     * @Route("/", name="discussion_index", methods={"GET|POST"})
     */
    public function index(DiscussionRepository $discussionRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $session = $request->getSession();
        $filterData = [];
        $filterForm = $this->createForm(DiscussionFilterType::class);

        if ('reset' == $request->get('filter_action')) { // Reset filter
            $session->remove('discussionFilter');

            return $this->redirect($this->generateUrl('discussion_index'));
        }


        if ('filter' == $request->get('filter_action')) { // Filter action
            $filterForm->handleRequest($request); // Bind values from the request
            if ($filterForm->isSubmitted() && $filterForm->isValid()) {
                if ($filterForm->has('abris')) {
                    $filterData['abris'] = $filterForm->get('abris')->getData();
                }

                $session->set('discussionFilter', $filterData); // Save filter to session
            }
        } elseif ($session->has('discussionFilter')) {
            $filterData = $session->get('discussionFilter');
            $filterForm = $this->createForm(DiscussionFilterType::class, $filterData, ['data_class' => null]);
            if (array_key_exists('abris', $filterData)) {
                $filterForm->get('abris')->setData($filterData['abris']);
            }
        }
        
        // envoi du user logué si son rôle est autre que admin
        if (!$this->getUser()->hasRole('ROLE_ADMIN')) {
            $filterData['userID'] = $this->getUser()->getId();
        }

        $per_page = $this->getParameter('app.per_page_global');
        $query = $discussionRepository->search($filterData);
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1)/* user number */,
            $request->query->getInt('per_page', $per_page), /* limit per user */
                []
        );


        return $this->render('admin/discussion/index.html.twig', ['pagination' => $pagination, 'search_form' => $filterForm->createView()]);
    }
    
    

    /**
     * @Route("/{id}/newMessage", name="message_new", methods={"POST"})
     */
    public function newMessage(Discussion $discussion, Request $request, TranslatorInterface $translator, \Swift_Mailer $mailer): Response
    {
        if ($request->request->has('message')) {
            $message = new Message();
            $message->setMessage($request->request->get('message'));
            $message->setSubject('');
            $message->setDiscussion($discussion);
            $discussion->setUpdated(new \DateTime());
            $this->getDoctrine()->getManager()->persist($message);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Entities.Message.flashes.addSuccess');
            $this->sendMailNotification($message, $mailer, $translator);
        }
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
        //$this->redirect($referer);
        // return $this->render('admin/dysfonctionnement/new.html.twig', [
        //             'dysfonctionnement' => $dysfonctionnement,
        //             'form' => $form->createView(),
        // ]);
    }
    
    
    
    
    /**
     * Finds and displays a Discussion entity.
     *
     * @param String   $userType
     * @param User $user
     * @Route("/{id}", name="admin_discussion_show",  methods="GET")
     *
     *
     * @return Response
     */
    public function showAction(Discussion $discussion, TranslatorInterface $translator)
    {
        //$this->checkAccess($discussion, $translator);
        return $this->render('admin/discussion/show.html.twig', [
            'discussion' => $discussion,
            
        ]);
    }
    
    /**
     * send email notification
     * @param type $message
     */
    private function sendMailNotification(Message $message, $mailer, TranslatorInterface $translator)
    {
        // lorsqu'un nouveau message est ajouté concernant un abris, une notification est envoyée
        $dysfonctionnement = $message->getDiscussion();
        $abris = $dysfonctionnement->getAbris();
        $currentUser =  $this->getUser();
        $destMail = [];
        $users = $abris->getGestionnaires() ?: $abris->getProprietaires();
        foreach ($users as $user) {
            if ($message->getCreatedBy()->getId() !== $currentUser->getId() && filter_var($user->getEmail(), FILTER_VALIDATE_EMAIL)) {
                $destMail[] = $user->getEmail();
            }
        }
        // followers
        foreach ($abris->getFollowers() as $user) {
            if ($message->getCreatedBy()->getId() !== $user->getId() && filter_var($user->getEmail(), FILTER_VALIDATE_EMAIL)) {
                $destMail[] = $user->getEmail();
            }
        }
           
        $body = str_replace(['%id%','%abris%'], [$message->getId(), $abris], $translator->trans('Emails.Message.newMessageAboutAbris.body'));
        $subject = str_replace('%id%', $message->getId(), $translator->trans('Emails.Message.newMessageAboutAbris.subject'));
        try {
            $email = (new \Swift_Message($subject))
                ->setFrom($this->getParameter('app.genericMail'))
                ->setTo($destMail)
                ->setBody(
                    $this->renderView(
                        'emails/generics.html.twig',
                        ['subject'=>$subject, 'body' => $body]
                    ),
                    'text/html'
                ) ;
            $mailer->send($email);
        } catch (Exception $exc) {
            echo "Imposssible d'envoyer le mail aux destinataires" . $exc->getTraceAsString();
        }
    }

    /**
     * @Route("/{id}/delete", name="discussion_delete", methods={"GET"})
     */
    public function delete(Request $request, Discussion $discussion, TranslatorInterface $translator): Response
    {
        $this->checkAccess($discussion, $translator);
        if ($this->isCsrfTokenValid('delete_discussion' . $discussion->getId(), $request->query->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($discussion);
            $entityManager->flush();
            $return = [
                'status' => 'success',
                'message' => $translator->trans('Generics.flash.deleteSuccess'),
            ];
        } else {
            $return = [
                'status' => 'error',
                'message' => 'CSRF Token Invalid',
            ];
        }

        return new Response(json_encode($return, JSON_THROW_ON_ERROR), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }
    
    /**
     * Batch action for discussion entity.
     *
     * @param Request $request
     * @Route("/batch", name="discussion_batch",  methods="POST")
     *
     * @return Response
     */
    public function batchAction(Request $request, DiscussionRepository $discussionRepository)
    {
        if ('batchDelete' === $request->request->get('batch_action')) {
            $ids = $request->request->get('ids');
            if ($ids) {
                $discussionRepository->batchDelete($ids);
                $this->addFlash('success', 'Les éléments selectionnés ont été supprimés');
            }
        }

        return $this->redirectToRoute('discussion_index');
    }
    
    private function checkAccess(Discussion $discussion, $translator)
    {
        $abris = $discussion->getAbris();
        if (!$this->isGranted('ROLE_ADMIN') && !($this->getUser()->getGestionnaireAbris())->contains($abris) && !($this->getUser()->getProprietaireAbris())->contains($abris)) {
            throw new AccessDeniedException($translator->trans('Security.messages.accessDeniedException'));
        }
    }
}
