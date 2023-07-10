<?php

namespace App\Service;

/**
 * Class Slug : Manage everything regarding Slug management.
 *
 */
class Slug
{
    /**
     * slug function
     *
     * @param [type] $string
     * @return $string
     */
    public function generateSlug(string $string)
    {
        // Remove special characters and symbols
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string);

        // Replace spaces with hyphens
        $string = str_replace(' ', '-', $string);

        // Convert to lowercase
        $string = strtolower($string);

        return $string;
    }
}
