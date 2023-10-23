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

        foreach ($categoryList as $key => $categoryName) {
            $category = new Category();
            $category->setName($categoryName);
            $category->setUuid(Uuid::v6());

            $manager->persist($category);

            $this->addReference('category_' . $key, $category);
        }

        $manager->flush();


    }
}
