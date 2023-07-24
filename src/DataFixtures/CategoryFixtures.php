<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Uid\Uuid;

class CategoryFixtures extends Fixture
{
    public const CATEGORY_REFERENCE = 'category';

    public function load(ObjectManager $manager): void
    {
        $categoryList = [
            'Les grabs',
            'Les rotations',
            'Les flips',
            'Les slides',
            'Les rotations désaxées',
            'Les one foot tricks',
            'Old School',
        ];

        foreach ($categoryList as $categoryName) {
            $category = new Category();
            $category->setName($categoryName);
            $uuid = Uuid::v6();
            $category->setUuid($uuid);

            $manager->persist($category);           
        }

        $manager->flush();

        $this->addReference(self::CATEGORY_REFERENCE, $category);
    }
}
