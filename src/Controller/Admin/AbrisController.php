<?php

namespace App\Controller\Admin;

use App\Entity\Abris;
use App\Entity\UploadedDocument;
use App\Entity\User;
use App\Form\AbrisFormType;
use App\FormFilter\AbrisFilterType;
use App\Repository\AbrisRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;

use function Safe\unlink;

/**
 * @Route("/admin/abris")
 */
class AbrisController extends Controller
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/", name="admin_abris_index", methods="GET|POST")
     */
    public function indexAction(AbrisRepository $abrisRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $session = $request->getSession();
        $filterData = [];
        $filterForm = $this->createForm(AbrisFilterType::class);

        if ('reset' == $request->get('filter_action')) { // Reset filter
            $session->remove('abrisFilter');

            return $this->redirect($this->generateUrl('admin_abris_index'));
        }

        if ('filter' == $request->get('filter_action')) { // Filter action
            $filterForm->handleRequest($request); // Bind values from the request
            if ($filterForm->isSubmitted() && $filterForm->isValid()) {
                if ($filterForm->has('name')) {
                    $filterData['name'] = $filterForm->get('name')->getData();
                }
                if ($filterForm->has('type')) {
                    $filterData['type'] = $filterForm->get('type')->getData();
                }

                $session->set('abrisFilter', $filterData); // Save filter to session
            }
        } elseif ($session->has('abrisFilter')) {
            $filterData = $session->get('abrisFilter');
            if (array_key_exists('name', $filterData)) {
                $filterForm->get('name')->setData($filterData['name']);
            }
            if (array_key_exists('type', $filterData) && '' != $filterData['type']) {
                $type = $filterData['type'];
                $type = $this->getDoctrine()->getManager()->merge($type);
                $filterForm->get('type')->setData($type);
            }
        }

        // envoi du user logué si son rôle est autre que admin
        if (!$this->getUser()->hasRole('ROLE_ADMIN')) {
            $filterData['userID'] = $this->getUser()->getId();
        }
        $per_page = $this->getParameter('app.per_page_global');
        $query = $abrisRepository->search($filterData);
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1)/* page number */,
            $request->query->getInt('per_page', $per_page), /* limit per page */
            ['defaultSortFieldName' => 'a.slug', 'defaultSortDirection' => 'asc']
        );

        return $this->render('admin/abris/index.html.twig', ['pagination' => $pagination, 'search_form' => $filterForm->createView()]);
    }

    /**
     * @Route("/new", name="admin_abris_new", methods="GET|POST")
     */
    public function newAction(Request $request, TranslatorInterface $translator): Response
    {
        $em = $this->getDoctrine()->getManager();
        $abris = new Abris();
        //        $placeDeFeuInt = new AbrisPlaceDeFeuInterieur();
        $em->persist($abris);
        //        $abris->addAbrisPlaceDeFeuInterieur($placeDeFeuInt);
        $form = $this->createForm(AbrisFormType::class, $abris, ['translator' => $translator, 'attr' => ['id' => 'abris-form']]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($abris);
            $em->flush();

            $this->addFlash('success', 'Generics.flash.addSuccess');
            if (!$request->request->has('createAndContinue')) {
                return $this->redirectToRoute('admin_abris_index');
            } else {
                $abris = new Abris();
                $form = $this->createForm(AbrisFormType::class, $abris, ['translator' => $translator]);
            }
        }

        return $this->render('admin/abris/new.html.twig', [
                    'abris' => $abris,
                    'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_abris_edit", methods="GET|POST")
     */
    public function editAction(Request $request, Abris $abris, TranslatorInterface $translator, FileUploader $fileUploader, \Swift_Mailer $mailer): Response
    {
        $this->checkAccess($abris, $translator);
        $form = $this->createForm(AbrisFormType::class, $abris, ['translator' => $translator]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->has('files')) {
                $uploadedFiles = $form['files']->getData();
                foreach ($uploadedFiles as $file) {
                    $uploadedDoc = $fileUploader->upload($file, $abris);
                }
            }
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Generics.flash.editSuccess');
            // faire en sorte que les admin soient avertis par mail lors des mises à jour sur les abris par les gestionnaires/propriétaires
            if ($this->isGranted('ROLE_OWNER') || $this->isGranted('ROLE_MANAGER')) {
                $this->sendEditNotificationEmail($abris, $translator, $mailer);
            }

            return $this->redirectToRoute('admin_abris_index');
        }

        return $this->render('admin/abris/edit.html.twig', [
                    'abris' => $abris,
                    'form' => $form->createView(),
        ]);
    }

    /**
     * Delete a abris entity.
     *
     * @Route("/{id}/delete", name="admin_abris_delete",  methods="GET")
     *
     * @return Response
     */
    public function deleteAction(Abris $abris, TranslatorInterface $translator, Request $request)
    {
        $this->checkAccess($abris, $translator);
        if ($this->isCsrfTokenValid('delete'.$abris->getId(), $request->query->get('_token'))) {
            $this->getDoctrine()->getManager()->remove($abris);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Suppression effectuée avec succès');
        } else {
            $this->addFlash('error', 'CSRF Token Invalid');
        }

        return $this->redirectToRoute('admin_abris_index');
    }

    /**
     * Delete a photo entity.
     *
     * @Route("/photo/{id}/delete", name="admin_abris_delete_photo",  methods="GET")
     *
     * @return Response
     */
    public function deletePhotoAction(UploadedDocument $photo, Request $request)
    {
        $abrisId = $photo->getAbris()->getId();
        if ($this->isCsrfTokenValid('delete_photo'.$photo->getId(), $request->query->get('_token'))) {
            $this->getDoctrine()->getManager()->remove($photo);
            $this->getDoctrine()->getManager()->flush();
            $filename = $this->getParameter('picture_directory').'/'.$abrisId.'/'.$photo->getFileName();

            if (file_exists($filename)) {
                unlink($filename);
            } else {
                $this->addFlash('warning', 'Impossible de supprimer le fichier');
            }
            $this->addFlash('success', 'Suppression effectuée avec succès');
        } else {
            $this->addFlash('error', 'CSRF Token Invalid');
        }

        return $this->redirectToRoute('admin_abris_edit', ['id' => $abrisId]);
    }

    /**
     * Batch action for BusinessState entity.
     *
     * @Route("/batch", name="admin_abris_batch",  methods="POST")
     *
     * @return Response
     */
    public function batchAction(Request $request, AbrisRepository $abrisRepository)
    {
        if ('batchDelete' === $request->request->get('batch_action')) {
            $ids = $request->request->get('ids');
            if ($ids) {
                $abrisRepository->batchDelete($ids);
                $this->addFlash('success', 'Les éléments selectionnés ont été supprimés');
            }
        }

        return $this->redirectToRoute('admin_abris_index');
    }

    private function checkAccess($abris, $translator)
    {
        if (!$this->isGranted('ROLE_ADMIN') && !$this->getUser()->getGestionnaireAbris()->contains($abris) && !$this->getUser()->getProprietaireAbris()->contains($abris)) {
            throw new AccessDeniedException($translator->trans('Security.messages.accessDeniedException'));
        }
    }

    private function sendEditNotificationEmail($abris, $translator, $mailer)
    {
        // recherche des admins
        $filter = ['role' => 'ROLE_ADMIN'];
        $admins = $this->em->getRepository(User::class)->search($filter);

        $destsMail = [];
        foreach ($admins as $admin) {
            $destsMail[] = $admin->getEmail();
        }

        $currentUser = $this->getUser();
        $baseUrl = 'https://abris.parc-du-vercors.fr';
        $url = $baseUrl.$this->generateUrl('admin_abris_edit', [
            'id' => $abris->getId(),
        ]);

        $subject = str_replace(['%id%', '%abris%'], [$abris, $currentUser], $translator->trans('Emails.Abris.updateNotification.subject'));
        $body = str_replace(['%id%', '%abris%', '%url%', '%currentUser%'], [$abris->getId(), $abris, $url, $currentUser], $translator->trans('Emails.Abris.updateNotification.body'));

        try {
            $message = (new \Swift_Message($subject))
                ->setFrom($this->getParameter('app.genericMail'))
                ->setTo($destsMail)
                ->setBody(
                    $this->renderView(
                        'emails/generics.html.twig',
                        ['subject' => $subject, 'body' => $body]
                    ),
                    'text/html'
                );
            $mailer->send($message);
        } catch (\Exception $exc) {
            echo "Imposssible d'envoyer le mail aux destinataires".$exc->getTraceAsString();
        }
    }
}
