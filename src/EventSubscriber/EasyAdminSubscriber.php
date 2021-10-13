<?php
namespace App\EventSubscriber;

use App\Entity\Blogpost;
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
            BeforeEntityPersistedEvent::class => [
                'setBlogPostSlugAndDateAndUser',
            ],
        ];
    }
    public function setBlogPostSlugAndDateAndUser(
        BeforeEntityPersistedEvent $event
    ) {
        $entity = $event->getEntityInstance();
        if (!($entity instanceof Blogpost)) {
            return;
        }

        $slug = $this->slugger->slug($entity->getTitle());
        $entity->setSlug($slug);

        $now = new DateTime('now');
        $entity->setDateUpload($now);

        $user = $this->security->getUser();
        $entity->setUser($user);
    }
}
