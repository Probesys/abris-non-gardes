<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Abris;
use App\Entity\Discussion;
use App\Service\FileUploader;
use App\Entity\Dysfonctionnement;
use App\Entity\Message;
use App\Form\DiscussionType;
use App\Form\DysfonctionnementType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @Rest\Route("/api/discussion")

 */
final class ApiDiscussionController extends AbstractController
{
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
     * @Rest\Get("/detail/{id}", name="detailDiscussion")
     */
    public function detailAction(Discussion $discussion): JsonResponse
    {
        if (!$discussion) {
            throw new BadRequestHttpException('Discussion non trouvée');
        }

        $data = $this->serializer->serialize($discussion, 'json', ['groups' => ['discussion']]);

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @throws BadRequestHttpException
     *
     * @Rest\Post("/create/", name="createDiscussion")
     */
    public function createAction(Request $request, TranslatorInterface $translator, \Swift_Mailer $mailer): JsonResponse
    {

        $discussion = new Discussion();
        $form = $this->createForm(DiscussionType::class, $discussion);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $this->em->persist($discussion);
            $this->em->flush();
            if(!$discussion->getDysfonctionnement()) {
                $this->sendCreateEmail($discussion, $translator, $mailer);
            }
        }

        $data = $this->serializer->serialize($discussion, 'json', ['groups' => ['abris']]);

        return new JsonResponse($data, Response::HTTP_CREATED, [], true);
    }

    /**
     * @throws BadRequestHttpException
     *
     * @Rest\Post("/{id}/new-message/", name="api_message_new")
     */
    public function newMessageAction(Discussion $discussion, Request $request, TranslatorInterface $translator, \Swift_Mailer $mailer): JsonResponse
    {
        if (!$discussion) {
            throw new BadRequestHttpException('Discussion non trouvée');
        }

        $message = $request->request->get('message');
        if (empty($message)) {

            throw new BadRequestHttpException('message cannot be empty');
        }

        $objMessage = new Message();
        $objMessage->setDiscussion($discussion);
        $objMessage->setMessage($message);
        $discussion->addMessage($objMessage);
        $discussion->setUpdated(new \DateTime());
        $this->em->persist($objMessage);
        $this->em->persist($discussion);
        $this->em->flush();

        $data = $this->serializer->serialize($objMessage, 'json', ['groups' => ['abris']]);

        return new JsonResponse($data, Response::HTTP_CREATED, [], true);
    }

    private function sendCreateEmail($discussion, $translator, $mailer)
    {
        $abris = $discussion->getAbris();
        $destMail = [];
        // /** @var  User $user */
        $users = $abris->getGestionnaires() ?: $abris->getProprietaires();
        foreach ($users as $user) {
            if (filter_var($user->getEmail(), FILTER_VALIDATE_EMAIL)) {
                $destMail[] = $user->getEmail();
            }
        }
        if (!empty($destMail)) {
            $url = "https://abris.parc-du-vercors.fr/";

            $body = str_replace(['%id%', '%abris%', '%url%'], [$discussion->getId(), $abris, $url], $translator->trans('Emails.Discussion.newDiscussion.body'));
            $subject = str_replace(['%id%', '%abris%'], [$discussion->getId(), $abris], $translator->trans('Emails.Discussion.newDiscussion.subject'));
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
            } catch (Exception $exc) {
                echo "Imposssible d'envoyer le mail aux destinataires" . $exc->getTraceAsString();
            }
        }
    }

}
