<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Abris;
use App\Entity\Discussion;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Rest\Route("/api/abris")
 
 */
final class ApiAbrisController extends AbstractController
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
     * @Rest\Get("/liste", name="findAllAbris")
     */
    public function findAllAction(): JsonResponse
    {
        $abris = $this->em->getRepository(Abris::class)->findBy([], ['id' => 'DESC']);
        $data = $this->serializer->serialize($abris, 'json', ['groups' => ['abris']]);

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }    

    /**
     * @Rest\Get("/search", name="searchAbris")
     */
    public function search(Request $request): JsonResponse
    {        
        $searchFilter = ['name' => $request->query->get("keySearch")];
        $abris = $this->em->getRepository(Abris::class)->search($searchFilter)->getResult();
        $data = $this->serializer->serialize($abris, 'json', ['groups' => ['abris']]);

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }     

    /**
     * @Rest\Get("/detail/{id}", name="detailAbris")
     */
    public function detailAction($id): JsonResponse
    {
        $abris = $this->em->getRepository(Abris::class)->findBy(['id' => $id], ['id' => 'DESC']);
        $data = $this->serializer->serialize($abris, 'json', ['groups' => ['abris']]);

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }       
      
    /**
     * @Rest\Get("/{id}/discussions", name="discussionsAbris")
     */
    public function discussions(Abris $abris, Request $request): JsonResponse
    {        
//        dd($abris);
        $discussions = $this->em->getRepository(Discussion::class)->listForAbris($abris);
        $data = $this->serializer->serialize($discussions, 'json', ['groups' => ['abris']]);

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }      
}

