<?php

namespace App\EventListener;

use App\Entity\Discussion;
use App\Entity\Dysfonctionnement;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Psr\Log\LoggerInterface;

class DysfonctionnementListener
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->index($args);
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->index($args);
    }

    public function index(LifecycleEventArgs $event)
    {
        $entity = $event->getEntity();

        $em = $event->getEntityManager();

        if ($entity instanceof Dysfonctionnement && is_null($entity->getDiscussion())) {
            // $this->postUpdateConsultationFeedingInfos($entity, $em);
            /** @var Dysfonctionnement $entity */
            $abris = $entity->getAbris();
            $discussion = new Discussion();
            $discussion->setDysfonctionnement($entity);
            $discussion->setAbris($entity->getAbris());
            $discussion->setName('Abris '.$abris.' : Dysfonctionnement '.$discussion->getId());
            $discussion->setDescription($entity->getDescription());
            $entity->setDiscussion($discussion);
            $em->persist($entity);
            $em->persist($discussion);

            $em->flush();
        }
    }
}
