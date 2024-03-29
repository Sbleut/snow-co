<?php

namespace App\Email;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;

class EmailVerifier
{
    public function __construct(
        /**
         * VerifyEmailHelperInterface is used for :
         *  - generateToken using @param $verifyEmailRouteName, @param $userMail
         *  - validateEmailConfirmation using @param $requestUri, @param $uuid, @param $token
         *  - Token Générer comme un mot de passe stocker sur le token.
         * @var VerifyEmailHelperInterface
         */
        private MailerInterface $mailer,
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * @throws VerifyEmailExceptionInterface
     */
    public function handleEmailConfirmation(User $user, $token): void
    {
        if($token == $user->getTokenValidator()) {
            $user->setIsVerified(true);
            $user->setTokenValidator(null);
        }
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
