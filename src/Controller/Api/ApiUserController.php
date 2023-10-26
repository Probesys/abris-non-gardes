<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Abris;
use App\Entity\ListingValue;
use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Rest\Route("/api/user")
 */
final class ApiUserController extends AbstractController
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
     * @throws BadRequestHttpException
     *
     * @Rest\Post("/{id}/addAbris", name="addAbrisToBookMark")
     *
     * @IsGranted("ROLE_USER")
     */
    public function addAbrisToBookMark(User $user, Request $request): JsonResponse
    {
        $id_abris = $request->request->get('id_abris');
        if (empty($id_abris)) {
            throw new BadRequestHttpException('id_abris cannot be empty');
        }
        $abris = $this->em->getRepository(Abris::class)->find($id_abris);
        $user->addAbrisFavori($abris);
        $data = $this->serializer->serialize($abris, 'json', ['groups' => ['abris']]);
        $this->em->flush();

        return new JsonResponse($data, Response::HTTP_CREATED, [], true);
    }

    /**
     * @throws BadRequestHttpException
     *
     * @Rest\Post("/{id}/removeAbris", name="removeAbrisBookMark")
     *
     * @IsGranted("ROLE_USER")
     */
    public function removeAbrisBookMark(User $user, Request $request): JsonResponse
    {
        $id_abris = $request->request->get('id_abris');
        if (empty($id_abris)) {
            throw new BadRequestHttpException('id_abris cannot be empty');
        }
        $abris = $this->em->getRepository(Abris::class)->find($id_abris);
        $user->removeAbrisFavori($abris);
        $this->em->flush();

        return new JsonResponse('', Response::HTTP_CREATED, [], true);
    }

    /**
     * Retourne la liste des des types d'usagers.
     *
     * @Rest\Get("/userTypes/", name="listeTypeUser")
     */
    public function listeTypeUser(): JsonResponse
    {
        $liste = $this->em->getRepository(ListingValue::class)->findBy(['listingType' => 23], ['name' => 'ASC']);
        $data = $this->serializer->serialize($liste, 'json', ['groups' => ['default']]);

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @throws BadRequestHttpException
     *
     * @Rest\Post("/{id}/updateProfile", name="updateProfile")
     */
    public function updateProfile(User $user, Request $request, TranslatorInterface $translator): JsonResponse
    {
        //        $dysfonctionnement = new Dysfonctionnement();

        $form = $this->createForm(RegistrationFormType::class, $user, ['translator' => $translator]);
        $form->remove('email');
        $form->remove('login');
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $this->em->persist($user);
            $this->em->flush();
        }

        $data = $this->serializer->serialize($user, 'json', ['groups' => ['user']]);

        return new JsonResponse($data, Response::HTTP_CREATED, [], true);
    }
}
