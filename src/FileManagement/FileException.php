<?php

namespace App\FileManagement;


interface FileExceptionInterface extends \Throwable
{
    /**
     * Returns a safe string that describes why verification failed.
     */
    public function getReason(): string;
}