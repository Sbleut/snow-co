<?php

namespace App\FileManagement;

use App\Entity\Image;

class UploadImage
{
    public function saveImage($image)
    {
        $file = md5(uniqid()) . '.' . $image->guessExtension();

        $image->move($this->params->get('trick_image_directory'), $file);

        return $file;
    }
}