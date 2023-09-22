<?php

namespace App\Controller\Admin;

use App\Entity\Dysfonctionnement;
use App\Entity\Discussion;
use App\Entity\UploadedDocument;
use App\Form\DysfonctionnementType;
use App\FormFilter\DysfonctionnementFilterType;
use App\Repository\DysfonctionnementRepository;
use App\Repository\ListingValueRepository;
use App\Service\FileUploader;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/admin/dysfonctionnement")
 */
class DysfonctionnementController extends AbstractController
{
    public const newStatusID = 47;
    public const inprogressStatusID = 48;
    public const resolveStatusID = 49;

    /**
     * @Route("/", name="dysfonctionnement_index", methods={"GET|POST"})
     */
    public function index(DysfonctionnementRepository $dysfonctionnementRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $session = $request->getSession();
        $filterData = [];
        $filterForm = $this->createForm(DysfonctionnementFilterType::class);

        if ('reset' == $request->get('filter_action')) { // Reset filter
            $session->remove('dysfonctionnementFilter');

            return $this->redirect($this->generateUrl('dysfonctionnement_index'));
        }

        if ($request->query->has('abrisName')) {
            $filterData = $session->get('dysfonctionnementFilter');
            $filterData['abris'] = $request->query->get('abrisName');
            $session->set('dysfonctionnementFilter', $filterData); // Save filter to session
        }
        if ('filter' == $request->get('filter_action')) { // Filter action
            $filterForm->handleRequest($request); // Bind values from the request
            if ($filterForm->isSubmitted() && $filterForm->isValid()) {
                if ($filterForm->has('abris')) {
                    $filterData['abris'] = $filterForm->get('abris')->getData();
                }
                if ($filterForm->has('natureDys')) {
                    $filterData['natureDys'] = $filterForm->get('natureDys')->getData();
                }

                $session->set('dysfonctionnementFilter', $filterData); // Save filter to session
            }
        } elseif ($session->has('dysfonctionnementFilter')) {
            $filterData = $session->get('dysfonctionnementFilter');
            $filterForm = $this->createForm(DysfonctionnementFilterType::class, $filterData, ['data_class' => null]);
            if (array_key_exists('abris', $filterData)) {
                $filterForm->get('abris')->setData($filterData['abris']);
            }
            if (array_key_exists('natureDys', $filterData)) {
                $filterForm->get('natureDys')->setData($filterData['natureDys']);
            }
        }

        // envoi du user logué si son rôle est autre que admin
        if (!$this->getUser()->hasRole('ROLE_ADMIN')) {
            $filterData['userID'] = $this->getUser()->getId();
        }

        $per_page = $this->getParameter('app.per_page_global');
        $query = $dysfonctionnementRepository->search($filterData);
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1)/* user number */,
            $request->query->getInt('per_page', $per_page), /* limit per user */
            []
        );


        return $this->render('admin/dysfonctionnement/index.html.twig', ['pagination' => $pagination, 'search_form' => $filterForm->createView()]);
    }

    /**
     * @Route("/new", name="dysfonctionnement_new", methods={"GET","POST"})
     */
    public function new(Request $request, ListingValueRepository $listingValueRepository, TranslatorInterface $translator, \Swift_Mailer $mailer): Response
    {
        $dysfonctionnement = new Dysfonctionnement();
        $newStatus = $listingValueRepository->find(self::newStatusID);
        $dysfonctionnement->setStatusDys($newStatus);
        $form = $this->createForm(DysfonctionnementType::class, $dysfonctionnement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dysfonctionnement->setStatusDys($newStatus);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($dysfonctionnement);
            $entityManager->flush();

            $this->sendCreateEmail($dysfonctionnement, $translator, $mailer);

            return $this->redirectToRoute('dysfonctionnement_index');
        }

        return $this->render('admin/dysfonctionnement/new.html.twig', [
                    'dysfonctionnement' => $dysfonctionnement,
                    'form' => $form->createView(),
        ]);
    }

    private function sendCreateEmail($dysfonctionnement, $translator, $mailer)
    {
        $abris = $dysfonctionnement->getAbris();
        $destMail = $dysfonctionnement->getCreatedBy()->getEmail();
        $url = "https://abris.parc-du-vercors.fr/";

        // ####  envoi du mail à la personne ayant ajouté le dysfonctionnement    
        $body = str_replace(['%id%','%abris%','%url%'], [$dysfonctionnement->getId(), $abris, $url], $translator->trans('Emails.Dysfonctionnement.newDysfonctionnement.body'));
        $subject = str_replace(['%id%','%abris%'], [$dysfonctionnement->getId(), $abris], $translator->trans('Emails.Dysfonctionnement.newDysfonctionnement.subject'));
        try {
            $message = (new \Swift_Message($subject))
                ->setFrom($this->getParameter('app.genericMail'))
                ->setTo($destMail)
                ->setBody(
                    $this->renderView(
                        'emails/generics.html.twig',
                        ['subject'=>$subject, 'body' => $body]
                    ),
                    'text/html'
                ) ;
            $mailer->send($message);
        } catch (Exception $exc) {
            echo "Imposssible d'envoyer le mail aux destinataires" . $exc->getTraceAsString();
        }
        
        // ####  mail aux proprietaires / gestionnaires
        $destMails = [];
        /** @var  User $user */
        foreach ($abris->getGestionnaires() as $user) {
            if (filter_var($user->getEmail(), FILTER_VALIDATE_EMAIL)) {
                $destMails[] = $user->getEmail();
            }
        }
        foreach ($abris->getProprietaires() as $user) {
            if (filter_var($user->getEmail(), FILTER_VALIDATE_EMAIL)) {
                $destMails[] = $user->getEmail();
            }
        }

        if (!empty($destMails)) {
            try {
                $message = (new \Swift_Message($abris->getName() . ' ' . $translator->trans('Security.messages.newDysfunctionReported')))
                    ->setFrom($this->getParameter('app.genericMail'))        
                    ->setBody(
                    $this->renderView(
                            'emails/newDysfunction.html.twig',
                            ['dysfonctionnement' => $dysfonctionnement]
                        ),
                        'text/html'
                    );
                foreach($destMails as $email){
                   $message->addTo($email); 
                }
                $mailer->send($message);
            } catch (Exception $exc) {
                echo "Imposssible d'envoyer le mail aux destinataires" . $exc->getTraceAsString();
            }
        }
    }

    /**
     * @Route("/{id}/edit", name="dysfonctionnement_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Dysfonctionnement $dysfonctionnement, FileUploader $fileUploader, TranslatorInterface $translator, \Swift_Mailer $mailer): Response
    {
        $this->checkAccess($dysfonctionnement, $translator);
        $oldStatus = $dysfonctionnement->getStatusDys();
        $user = $this->getUser();
        $form = $this->createForm(DysfonctionnementType::class, $dysfonctionnement, ['user' => $user]);
        $form->handleRequest($request);

        if (!$dysfonctionnement->getDiscussion()) {
            $discussion = new Discussion();
            $discussion->setAbris($dysfonctionnement->getAbris());
            $discussion->setName($dysfonctionnement->getAbris()." ".$dysfonctionnement->getNatureDys()." ".$dysfonctionnement->getCreated()->format('d/m/Y'));
            $this->getDoctrine()->getManager()->persist($discussion);
            $dysfonctionnement->setDiscussion($discussion);
            $this->getDoctrine()->getManager()->flush();
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFiles = $form['files']->getData();
            foreach ($uploadedFiles as $file) {
                $uploadedDoc = $fileUploader->upload($file, $dysfonctionnement);
            }
            $this->getDoctrine()->getManager()->flush();

            // test si passage a resolus
            $this->sendChangeStatusEmail($dysfonctionnement, $oldStatus, $translator, $mailer);


            return $this->redirectToRoute('dysfonctionnement_index');
        }

        return $this->render('admin/dysfonctionnement/edit.html.twig', [
                    'dysfonctionnement' => $dysfonctionnement,
                    'form' => $form->createView(),
        ]);
    }

    private function sendChangeStatusEmail($dysfonctionnement, $oldStatus, $translator, $mailer)
    {
        $newStatus = $dysfonctionnement->getStatusDys();
        $abris = $dysfonctionnement->getAbris();
        $destMail = $dysfonctionnement->getCreatedBy()->getEmail();

        if ((!$oldStatus || $newStatus->getId() !== $oldStatus->getId()) && $newStatus->getId() == $this::resolveStatusID) {
            // resolus : envoi d'un mail à l'auteur du dysfcontionnement
            $body = str_replace(['%id%','%abris%'], [$dysfonctionnement->getId(), $abris], $translator->trans('Emails.Dysfonctionnement.dysfonctionnementResolved.body'));
            $subject = str_replace(['%id%','%abris%'], [$dysfonctionnement->getId(), $abris], $translator->trans('Emails.Dysfonctionnement.dysfonctionnementResolved.subject'));
            try {
                $message = (new \Swift_Message($subject))
                ->setFrom($this->getParameter('app.genericMail'))
                ->setTo($destMail)
                ->setBody(
                    $this->renderView(
                        'emails/generics.html.twig',
                        ['subject'=>$subject, 'body' => $body]
                    ),
                    'text/html'
                ) ;
                $mailer->send($message);
            } catch (Exception $exc) {
                echo "Imposssible d'envoyer le mail aux destinataires" . $exc->getTraceAsString();
            }
        }
    }

    /**
     * @Route("/{id}/delete", name="dysfonctionnement_delete", methods={"GET"})
     */
    public function delete(Request $request, Dysfonctionnement $dysfonctionnement, TranslatorInterface $translator): Response
    {
        $this->checkAccess($dysfonctionnement, $translator);
        if ($this->isCsrfTokenValid('delete_dysfonctionnement' . $dysfonctionnement->getId(), $request->query->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($dysfonctionnement);
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
     * Delete a photo entity.
     *
     * @param int $id
     * @Route("/photo/{id}/delete", name="admin_dysfonctionnement_delete_photo",  methods="GET")
     *
     * @return Response
     */
    public function deletePhotoAction(UploadedDocument $photo, Request $request)
    {
        $abrisId = $photo->getDysfonctionnement()->getAbris()->getId();
        if ($this->isCsrfTokenValid('delete_photo' . $photo->getId(), $request->query->get('_token'))) {
            $this->getDoctrine()->getManager()->remove($photo);
            $this->getDoctrine()->getManager()->flush();
            $filename = $this->getParameter('picture_directory') . '/' . $abrisId . '/' . $photo->getFileName();

            if (file_exists($filename)) {
                unlink($filename);
            } else {
                $this->addFlash('warning', 'Impossible de supprimer le fichier');
            }
            $this->addFlash('success', 'Suppression effectuée avec succès');
        } else {
            $this->addFlash('error', 'CSRF Token Invalid');
        }



        return $this->redirectToRoute('dysfonctionnement_edit', ['id' => $photo->getDysfonctionnement()->getId()]);
    }

    /**
     * Batch action for Dysfonctionnement entity.
     *
     * @param Request $request
     * @Route("/batch", name="dysfonctionnement_batch",  methods="POST")
     *
     * @return Response
     */
    public function batchAction(Request $request, DysfonctionnementRepository $dysfonctionnementRepository)
    {
        if ('batchDelete' === $request->request->get('batch_action')) {
            $ids = $request->request->get('ids');
            if ($ids) {
                $dysfonctionnementRepository->batchDelete($ids);
                $this->addFlash('success', 'Les éléments selectionnés ont été supprimés');
            }
        }

        return $this->redirectToRoute('dysfonctionnement_index');
    }

    /**
     * @Route("/{id}/newDiscussion", name="admin_dysfonctionnement_newDiscussion", methods={"POST"})
     */
    //    public function newFromDysfonctionnementAction(Dysfonctionnement $dysfonctionnement, Request $request): Response {
    //
    //        if ($request->request->has('name')){
    //            $discussion = new Discussion();
    //            $discussion->setDysfonctionnement($request->request->get('message'));
    //            $message->setSubject('');
    //            $message->setDiscussion($discussion);
    //            $this->getDoctrine()->getManager()->persist($message);
    //            $this->getDoctrine()->getManager()->flush();
    //        }
    //        $referer = $request->headers->get('referer');
    //        $this->redirect($referer);
    //
    //    }

    private function checkAccess(Dysfonctionnement $dysfonctionnement, $translator)
    {
        $abris = $dysfonctionnement->getAbris();
        if (!$this->isGranted('ROLE_ADMIN') && !($this->getUser()->getGestionnaireAbris())->contains($abris) && !($this->getUser()->getProprietaireAbris())->contains($abris)) {
            throw new AccessDeniedException($translator->trans('Security.messages.accessDeniedException'));
        }
    }
}
