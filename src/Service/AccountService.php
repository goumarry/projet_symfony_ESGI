<?php


namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class AccountService
{
    public function __construct(
        private UserPasswordHasherInterface $hasher,
        private EntityManagerInterface      $em,
        private VerifyEmailHelperInterface  $verifyEmailHelper,
        private MailerInterface             $mailer
    )
    {
    }

    public function registerUser(User $user, string $plainPassword): void
    {
        $hashedPassword = $this->hasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);

        $this->em->persist($user);
        $this->em->flush();

        $this->sendVerificationEmail($user);
    }

    private function sendVerificationEmail(User $user): void
    {
        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            'app_verify_email',
            $user->getId(),
            $user->getEmail(),
            ['id' => $user->getId()]
        );

        $email = (new Email())
            ->from('admin@monsite.com')
            ->to($user->getEmail())
            ->subject('Confirmez votre email')
            ->html('<p>Merci de confirmer votre compte : <a href="' . $signatureComponents->getSignedUrl() . '">Valider</a></p>');

        $this->mailer->send($email);
    }

    public function verifyUserEmail(User $user, string $signedUri): void
    {
        $this->verifyEmailHelper->validateEmailConfirmation(
            $signedUri,
            $user->getId(),
            $user->getEmail()
        );

        $user->setIsVerified(true);

        $this->em->persist($user);
        $this->em->flush();
    }
}