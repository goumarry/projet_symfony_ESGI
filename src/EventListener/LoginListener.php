<?php


namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

final class LoginListener
{
    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {
    }

    #[AsEventListener(event: InteractiveLoginEvent::class)]
    public function onInteractiveLogin(InteractiveLoginEvent $event): void
    {
        // 1. Récupérer l'utilisateur qui vient de se connecter
        $user = $event->getAuthenticationToken()->getUser();

        // 2. Vérifier que c'est bien notre entité User (et pas un admin système ou autre)
        if (!$user instanceof User) {
            return;
        }

        // 3. Mettre à jour la date
        $user->setLastLoginAt(new \DateTimeImmutable());

        // 4. Sauvegarder en base de données
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
