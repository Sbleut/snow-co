<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ImageFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        
        //Image 
        /**
         * dossier /public/uploads/image pour les vrais images à DL 
         * dossier /public/asset/image pour les images du site.
         * 
         * Set Main de manière aléatoire
         * Tous les cas de figure pour les images
         * 
         * Certains tricks sans image
         * 
         */
        // $product = new Product();
        // $manager->persist($product);
        $directory = "/uploads/image";
        $contents = scandir($directory);
        foreach ($contents as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $path = $directory . DIRECTORY_SEPARATOR . $item;
            $files = scandir($path);
            foreach ($files as $file){
                if (is_dir($path)) {
                    $subdirectoryData = array(
                        'directory' => $item,
                        'files' => $file
                    );
                    $results[] = $subdirectoryData;
                }
            }
            
            $manager->flush();
        }
    }

    public function getDependencies()
    {
        return [
            TrickFixtures::class,
        ];
    }
}
