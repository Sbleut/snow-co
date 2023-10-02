<?php

namespace App\FileManagement;


use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UploadImage
{
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validateUploadedFile($image): void
    {
        // Apply Symfony validation constraints to the file
        $violations = $this->validator->validate($image, [
            new Assert\File([
                'maxSize' => '5M',
                'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif'],
            ])
        ]);

        if (count($violations) > 0) {
            // Handle validation errors (e.g., throw an exception, log errors)
            throw new FileException('These errors occurs'.$violations);
        }
    }

    public function saveImage($image, $trickSlug)
    {
        $file = md5(uniqid()) . '.' . $image->guessExtension();

        $filesystem = new Filesystem();

        $directory = 'uploads/image' . strtolower($trickSlug);

        if (!$filesystem->exists($directory)) {
            $filesystem->mkdir($directory);
        }

        $image->move('uploads/image' . $trickSlug . '/' , $file);

        return $file;
    }
}