<?php

namespace App\Email\VeryEmail\Exception;


/**
 * An exception that is thrown by VerifyEmailHelperInterface::validateEmailConfirmation().
 *
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
 */
interface ForgotPasswordEmailExceptionInterface extends \Throwable
{
    /**
     * Returns a safe string that describes why verification failed.
     * 
     * @return string
     */


    public function getReason(): string;
}
