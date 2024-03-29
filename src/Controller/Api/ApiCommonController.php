<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Page;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Rest\Route("/api")
 */
final class ApiCommonController extends AbstractController
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
     * @Rest\Get("/pages", name="findAllPages")
     */
    public function findAllPagesAction(): JsonResponse
    {
        $abris = $this->em->getRepository(Page::class)->findBy(['dontListedInFrontPage' => 0], ['id' => 'DESC']);
        $data = $this->serializer->serialize($abris, 'json', ['groups' => ['default']]);

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Rest\Get("/pages/{id}", name="loadPage")
     */
    public function loadPageAction(Page $page): JsonResponse
    {
        $data = $this->serializer->serialize($page, 'json', ['groups' => ['default']]);

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }
}
