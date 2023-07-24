<?php

namespace App\Security;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class EmailHelper
{
    /**
     * Undocumented function
     *
     * @param string $routeName
     * @param string $userId
     * @param string $userEmail
     * @param array $extraParams
     * @return Object {$expiresAt, $uri, $generatedAt}
     */
    public function generateSignature(string $routeName, string $userId, string $userEmail, array $extraParams = [])
    {
        // If We want to put a duration constraint on validation we should set 


        return new Signature(); 
    }

    /**
     * IF VValidation 
     *
     * @param string $signedUrl
     * @param string $userId
     * @param string $userEmail
     * @return void
     */
    public function validateEmailConfirmation(string $signedUrl, string $userId, string $userEmail): void
    {
        // 
        // if (!$this->uriSigner->check($signedUrl)) {
        //     throw new InvalidSignatureException();
        // }

        // if ($this->queryUtility->getExpiryTimestamp($signedUrl) <= time()) {
        //     throw new ExpiredSignatureException();
        // }

        // $knownToken = $this->tokenGenerator->createToken($userId, $userEmail);
        // $userToken = $this->queryUtility->getTokenFromQuery($signedUrl);

        // if (!hash_equals($knownToken, $userToken)) {
        //     throw new WrongEmailVerifyException();
        // }

    }
}