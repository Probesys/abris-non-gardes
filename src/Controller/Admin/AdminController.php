<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Repository\DysfonctionnementRepository;
use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/admin")
 */
final class AdminController extends AbstractController
{
    /** @var SerializerInterface */
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @Route("/", name="admin_index")
     */
    public function indexAction(DysfonctionnementRepository $dysfonctionnementRepository, MessageRepository $messageRepository): Response
    {
        $user = $this->getUser();
        $userType = strtolower(str_replace('ROLE_', '', $this->getUser()->getRoles()[0]));

        // récupération des derniers dyfonctionnements
        $lastDysfonctionnements = $dysfonctionnementRepository->getLastDysfonctionnements($user, 10);

        // récupération des derniers messages
        $lastMessages = $messageRepository->getLastMessages($user, 10);

        return $this->render('admin/index.html.twig', [
            'user' => $user,
            'userType' => $userType,
            'lastDysfonctionnements' => $lastDysfonctionnements,
            'lastMessages' => $lastMessages,
        ]);
    }
}
