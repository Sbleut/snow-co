<?php

namespace App\Email;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

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
        private VerifyEmailHelperInterface $verifyEmailHelper,
        private MailerInterface $mailer,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function sendEmailConfirmation(string $verifyEmailRouteName, UserInterface $user, TemplatedEmail $email): void
    {
        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            $verifyEmailRouteName,
            $user->getId(),
            $user->getEmail(),
            ['id' => $user->getId()]
        );

        // $context = $email->getContext();
        // $context['signedUrl'] = $signatureComponents->getSignedUrl();
        // $context['expiresAtMessageKey'] = $signatureComponents->getExpirationMessageKey();
        // $context['expiresAtMessageData'] = $signatureComponents->getExpirationMessageData();

        // $email->context($context);

        $this->mailer->send($email);
    }

    /**
     * @throws VerifyEmailExceptionInterface
     */
    public function handleEmailConfirmation(Request $request, UserInterface $user): void
    {
        $this->verifyEmailHelper->validateEmailConfirmation($request->getUri(), $user->getId(), $user->getEmail());

        $user->setIsVerified(true);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
