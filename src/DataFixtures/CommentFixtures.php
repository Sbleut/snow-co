<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Tableau des commentaires génériques sur les figures de snowboard
        $commentList = array(
            "Les figures de snowboard sont vraiment captivantes ! J'adore l'élégance des riders sur les pistes.",
            "Vos tutoriels sur les figures sont une source d'inspiration pour progresser en snowboard. Merci pour les conseils !",
            "Le snowboard offre une véritable sensation de liberté, surtout lorsqu'on exécute des figures sur la poudreuse.",
            "Les compétitions de snowboard sont un spectacle incroyable. Les riders semblent flotter dans les airs lors de leurs rotations.",
            "Les défis de figures sont une excellente façon de se dépasser et d'améliorer ses compétences en snowboard.",
            "Les techniques de carving sont essentielles pour des descentes fluides et contrôlées en snowboard.",
            "Grâce à vos partages d'expérience, j'ai compris les bases pour progresser dans ma pratique du snowboard.",
            "Les riders maîtrisent des figures techniques impressionnantes, comme le 'Double Cork'. Quel talent !",
            "La sécurité est primordiale en snowboard. Toujours porter son équipement de protection et respecter les règles sur les pistes.",
            "Votre blog m'inspire à essayer de nouvelles figures. J'ai hâte de repousser mes limites en snowboard !",
        );

        for ($i = 1; $i <= 10; $i++) {
            $comment = new Comment();
            $trick = $this->getReference('trick_'.mt_rand(0, 20));
            $comment->setContent($commentList[mt_rand(0,10)]);
            $comment->setTrick($trick);
            $days = (new \DateTime())->diff($trick->getCreatedAt())->days;
            $interval = new \DateInterval('P' . $days . 'D');
            $createdAt = (new \DateTimeImmutable())->sub($interval);
            $comment->setCreatedAt($createdAt);
            $comment->setAuthor($this->getReference('user_' . mt_rand(0, 2)));
            $manager->persist($comment);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            TrickFixtures::class,
        ];
    }
}
