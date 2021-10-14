<?php
namespace App\EventSubscriber;

use App\Entity\Blogpost;
use App\Entity\Pictures;
use DateTime;
use Doctrine\Common\EventSubscriber;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\SluggerInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    private $slugger;
    private $security;
    public function __construct(SluggerInterface $slugger, Security $security)
    {
        $this->slugger = $slugger;
        $this->security = $security;
    }
    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => ['setDateAndUser'],
        ];
    }
    public function setDateAndUser(BeforeEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();
        if ($entity instanceof Blogpost || $entity instanceof Pictures) {
            $now = new DateTime('now');
            $entity->setDateUpload($now);

            $user = $this->security->getUser();
            $entity->setUser($user);
        }

        return;
    }
}
