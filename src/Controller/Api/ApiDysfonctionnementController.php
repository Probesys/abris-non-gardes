<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Dysfonctionnement;
use App\Entity\ListingValue;
use App\Entity\User;
use App\Form\DysfonctionnementType;
use App\Repository\ListingValueRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
// use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Rest\Route("/api/dysfonctionnement")
 */
final class ApiDysfonctionnementController extends AbstractController
{
    public const newStatusID = 47;
    public const inprogressStatusID = 48;
    public const resolveStatusID = 49;

    /** @var EntityManagerInterface */
    private $em;

    /** @var SerializerInterface */
    private $serializer;

    public function __construct(EntityManagerInterface $em, SerializerInterface $serializer)
    {
        $this->em = $em;
        $this->serializer = $serializer;
    }

    /**
     * @Rest\Get("/detail/{id}", name="detailDysfunction")
     */
    public function detailAction($id): JsonResponse
    {
        $dysfonctionnement = $this->em->getRepository(Dysfonctionnement::class)->findBy(['id' => $id], ['id' => 'DESC']);
        $data = $this->serializer->serialize($dysfonctionnement, 'json', ['groups' => ['dysfunction']]);

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * Retourne la liste des natures dysfonctionnements.
     *
     * @Rest\Get("/natureDys", name="ApiListNatureDys")
     */
    public function findAllNatureDys(): JsonResponse
    {
        $liste = $this->em->getRepository(ListingValue::class)->findBy(['listingType' => '21', 'parent' => null], ['id' => 'DESC']);
        $data = $this->serializer->serialize($liste, 'json', ['groups' => ['default']]);

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * Retourne la liste des éléments de dysfonctionnement par nature de dysfonctionnement.
     *
     * @Rest\Get("/elementsDys/{natureDysId}", name="ApiListElementsDys", options={"expose"=true})
     */
    public function findElementsDys($natureDysId): JsonResponse
    {
        $liste = $this->em->getRepository(ListingValue::class)->findBy(['parent' => $natureDysId], ['id' => 'DESC']);
        $data = $this->serializer->serialize($liste, 'json', ['groups' => ['default']]);

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * Retourne la liste des éléments de dysfonctionnement par nature de dysfonctionnement.
     *
     * @Rest\Get("/detailsDys/{elementDysId}", name="ApiListDetailsDys", options={"expose"=true})
     */
    public function findDetailsDys($elementDysId): JsonResponse
    {
        $liste = $this->em->getRepository(ListingValue::class)->findBy(['parent' => $elementDysId], ['id' => 'DESC']);
        $data = $this->serializer->serialize($liste, 'json', ['groups' => ['default']]);

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @throws BadRequestHttpException
     *
     * @Rest\Post("/create/", name="createDysfuction")
     */
    public function createAction(Request $request, FileUploader $fileUploader, TranslatorInterface $translator, \Swift_Mailer $mailer, ListingValueRepository $listingValueRepository): JsonResponse
    {
        $dysfonctionnement = new Dysfonctionnement();
        $newStatus = $listingValueRepository->find(self::newStatusID);
        $dysfonctionnement->setStatusDys($newStatus);

        $form = $this->createForm(DysfonctionnementType::class, $dysfonctionnement);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $uploadedFiles = $form['files']->getData();
            foreach ($uploadedFiles as $file) {
                $uploadedDoc = $fileUploader->upload($file, $dysfonctionnement);
            }
            $dysfonctionnement->setStatusDys($newStatus);
            $this->em->persist($dysfonctionnement);
            $this->em->flush();

            $this->sendCreateEmail($dysfonctionnement, $translator, $mailer);
        }

        $data = $this->serializer->serialize($dysfonctionnement, 'json', ['groups' => ['dysfunction']]);

        return new JsonResponse($data, Response::HTTP_CREATED, [], true);
    }

    private function sendCreateEmail($dysfonctionnement, $translator, $mailer)
    {
        // #### envoi du mail à la personne ayant ajouté le dysfonctionnement
        $abris = $dysfonctionnement->getAbris();
        $destMail = $dysfonctionnement->getCreatedBy()->getEmail();
        $url = 'https://abris.parc-du-vercors.fr/';

        $body = str_replace(['%id%', '%abris%', '%url%'], [$dysfonctionnement->getId(), $abris, $url], $translator->trans('Emails.Dysfonctionnement.newDysfonctionnement.body'));
        $subject = str_replace(['%id%', '%abris%'], [$dysfonctionnement->getId(), $abris], $translator->trans('Emails.Dysfonctionnement.newDysfonctionnement.subject'));
        try {
            $message = (new \Swift_Message($subject))
                ->setFrom($this->getParameter('app.genericMail'))
                ->setTo($destMail)
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

        // ####  mail aux proprietaires / gestionnaires
        $destMails = [];
        /** @var User $user */
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
                $message = (new \Swift_Message($abris->getName().' '.$translator->trans('Security.messages.newDysfunctionReported')))
                    ->setFrom($this->getParameter('app.genericMail'))
                    ->setBody(
                        $this->renderView(
                            'emails/newDysfunction.html.twig',
                            ['dysfonctionnement' => $dysfonctionnement]
                        ),
                        'text/html'
                    );
                foreach ($destMails as $email) {
                    $message->addTo($email);
                }
                $mailer->send($message);
            } catch (\Exception $exc) {
                echo "Imposssible d'envoyer le mail aux destinataires".$exc->getTraceAsString();
            }
        }
    }
}
