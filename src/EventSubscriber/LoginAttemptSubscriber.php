<?php

namespace App\EventSubscriber;

use App\Entity\LoginAttempt;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class LoginAttemptSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private RequestStack $requestStack
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => 'onLoginSuccess',
            LoginFailureEvent::class => 'onLoginFailure',
        ];
    }

    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();
        $this->logAttempt($user->getUserIdentifier(), true);
    }

    public function onLoginFailure(LoginFailureEvent $event): void
    {
        //
        $passport = $event->getPassport();
        $email = $passport ? $passport->getUser()->getUserIdentifier() : 'Inconnu';

        $this->logAttempt($email, false);
    }

    private function logAttempt(string $email, bool $success): void
    {
        $request = $this->requestStack->getCurrentRequest();
        $ip = $request ? $request->getClientIp() : null;

        $attempt = new LoginAttempt();
        $attempt->setEmail($email);
        $attempt->setIpAddress($ip);
        $attempt->setIsSuccessful($success);

        $this->em->persist($attempt);
        $this->em->flush();
    }
}