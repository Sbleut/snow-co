<?php

namespace App\FileManagement;


use Symfony\Component\Filesystem\Filesystem;
use App\FileManagement\FileExceptionInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UploadImage
{
    private $validator;
    private array $errorList;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validateUploadedFile($image): bool
    {
        // Apply Symfony validation constraints to the file
        $violations = $this->validator->validate($image, [
            new Assert\File([
                'maxSize' => '1M',
                'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif'],
            ])
        ]);

        if (count($violations) > 0) {
            $this->errorList = $violations;
            return false;
        }
        return true;
    }

    public function hasError()
    {
        return !empty($this->errorList);
    }

    public function getErrorMessages(): array
    {
        $errorMessages = [];

        /** @var ConstraintViolation $violation */
        foreach ($this->errorList as $violation) {
            $errorMessages[] = $violation->getMessage();
        }

        return $errorMessages;
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