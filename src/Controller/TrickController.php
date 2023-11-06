<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Entity\Image;
use App\FileManagement\UploadImage;
use App\Form\CommentFormType;
use App\Form\TrickCreateFormType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommentRepository;
use App\Repository\ImageRepository;
use App\Repository\TrickRepository;
use DateTimeImmutable;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TrickController extends AbstractController
{
    #[Route(
        '/trick/{slug}/{pageNb}',
        name: 'app_trick_detail',
        requirements: [
            'pageNb' => '\d+',
        ]
    )]
    /**
     * index function display a detailled trick with comments. I ensure
     *
     * @param Request $request Permits to retrieve form data for comment creation.
     * @param TrickRepository $trickRepository Necessary to fetch given trick by slug.
     * @param CommentRepository $commentRepository necessary to fetch comment in bdd depending on how many comments there are and how many times loading more has click on.
     * @param EntityManagerInterface $entityManager Tools to push to bdd.
     * @param string $slug parameter to fetch the given trick.
     * @param Security $security tools to set Author of the comment.
     * @param integer $pageNb given page to compute how many comment to retrieve.
     * @return Response
     */


    public function index(Request $request, TrickRepository $trickRepository, CommentRepository $commentRepository, EntityManagerInterface $entityManager, string $slug, Security $security, int $pageNb = 0): Response
    {
        $trick = $trickRepository->findOneBy(['slug' =>$slug]);
        $commentList = $trick->getComments();
        $commentTotal = $trick->getComments()->count([]);
        $limit = 2;
        $limitReached = false;

        if ($commentTotal > $limit * $pageNb) {
            $pageNb++;
        }
        if ($commentTotal <= $limit * $pageNb) {
            $limitReached = true;
        }

        $commentList = $commentRepository->findBy(['trick' => $trick], ['createdAt' => 'DESC'], $limit * $pageNb, 0);

        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setContent($form->get('content')->getData());
            $comment->setUuid(Uuid::v6());
            $comment->setCreatedAt(new DateTimeImmutable());
            $comment->setTrick($trick);
            $comment->setAuthor($security->getUser());

            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('success', ['Trick.Comment.Done']);

            return $this->redirectToRoute('app_trick_detail', ['slug' => $trick->getSlug()]);
        }

        return $this->render('trick/index.html.twig', [
            'controller_name'       => 'TrickController',
            'trick'                 => $trick,
            'commentList'           => $commentList,
            'form'                  => $form->createView(),
            'pageNb'                => $pageNb,
            'commentTotal'          => $commentTotal,
            'limit'                 => $limit,
            'limitReached'          => $limitReached,
        ]);
    }

    #[Route('/trickCreate', name: 'app_trick_create', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    /**
     * Creates a new trick.
     *
     * This method handles the creation of a new trick by a user. It processes the submitted form data, validates and saves the trick, and associates images and videos with it if provided.
     *
     * @param Request $request The HTTP request object containing form data.
     * @param UploadImage $uploadImage An instance of the UploadImage service for handling image uploads.
     * @param EntityManagerInterface $entityManager The Doctrine EntityManager to manage database operations.
     * @param SluggerInterface $slugger The Slugger service to generate a unique slug for the trick.
     *
     * @return Response A Symfony Response object representing the rendered view or a redirection to the trick's detail page.
     */


    public function trickCreate(Request $request, UploadImage $uploadImage, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $trick = new Trick();
        $form = $this->createForm(TrickCreateFormType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trickName = $form->get('name')->getData();
            $trick->setName($trickName);
            $trick->setDescription($form->get('description')->getData());
            $trick->setCreatedAt(new DateTimeImmutable());
            $trick->setCategory($form->get('category')->getData());
            $trick->setUser($this->getUser());
            $trick->setSlug($slugger->slug($trickName));
            $images = $form->get('images')->getData();

            foreach ($images as $image) {
                if ($uploadImage->validateUploadedFile($image)) {
                    $fichier = $uploadImage->saveImage($image, $trick->getSlug());
                    $img = new Image();
                    $img->setFileName($fichier);
                    $img->setMainImage(false);
                    $img->setUuid(Uuid::v6());
                    $trick->addImage($img);
                };
            }
            if ($uploadImage->hasError()) {
                $this->addFlash('errorfile', $uploadImage->getErrorMessages());
            }

            foreach ($trick->getVideos() as $video) {
                $video->setUuid(Uuid::v6());
            }

            // VErification embeded link youtube Regex.
            if (!$uploadImage->hasError()) {
                $entityManager->persist($trick);
                $entityManager->flush();
                $this->addFlash('success', ['Trick.Created']);
                return $this->redirectToRoute('app_trick_detail', ['slug' => $trick->getSlug()]);
            }
        }

        return $this->render('trick/create.html.twig', [
            'controller_name' => 'TrickController',
            'trick'           => $trick,
            'form'            => $form->createView(),
        ]);
    }

    #[Route(
        '/trick/update/{slug}',
        name: 'app_trick_update',
        methods: ['GET', 'POST'],
    )]
    #[IsGranted('ROLE_USER')]
    /**
     * Update an existing trick.
     *
     * This method handles the update of an existing trick by a user. It processes the submitted form data, validates and updates the trick, and associates images and videos with it if provided.
     *
     * @param Request $request The HTTP request object containing form data.
     * @param TrickRepository $trickRepository The repository to fetch the existing trick by slug.
     * @param EntityManagerInterface $entityManager The Doctrine EntityManager to manage database operations.
     * @param string $slug The unique identifier (slug) of the trick to be updated.
     * @param UploadImage $uploadImage An instance of the UploadImage service for handling image uploads.
     * @param SluggerInterface $slugger The Slugger service to generate a unique slug for the trick.
     *
     * @return Response A Symfony Response object representing the rendered view or a redirection to the trick's detail page.
     *
     * @throws NotFoundHttpException if the specified trick does not exist.
     */


    public function update(Request $request, TrickRepository $trickRepository, EntityManagerInterface $entityManager, string $slug, UploadImage $uploadImage, SluggerInterface $slugger): Response
    {

        $trick = $trickRepository->findOneBy(['slug' =>$slug]);
        if (!$trick) {
            throw $this->createNotFoundException("This trick doesn't exist");
        }

        $form = $this->createForm(TrickCreateFormType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trickName = $form->get('name')->getData();
            $trick->setName($trickName);
            $trick->setDescription($form->get('description')->getData());
            $trick->setUpdatedAt(new DateTimeImmutable());
            $trick->setSlug($slugger->slug($trickName));
            $trick->setCategory($form->get('category')->getData());
            $images = $form->get('images')->getData();

            foreach ($images as $image) {
                if ($uploadImage->validateUploadedFile($image)) {
                    $fichier = $uploadImage->saveImage($image, $trick->getSlug());
                    $img = new Image();
                    $img->setFileName($fichier);
                    $img->setMainImage(false);
                    $img->setUuid(Uuid::v6());
                    $trick->addImage($img);
                };
            }
            if ($uploadImage->hasError()) {
                $this->addFlash('errorfile', $uploadImage->getErrorMessages());
            }

            foreach ($trick->getVideos() as $video) {
                $video->setUuid(Uuid::v6());
            }

            if (!$uploadImage->hasError()) {
                $entityManager->persist($trick);
                $entityManager->flush();
                $this->addFlash('success', ['Trick.Updated']);
                return $this->redirectToRoute('app_trick_detail', ['slug' => $trick->getSlug()]);
            }
        }

        return $this->render('trick/update.html.twig', [
            'controller_name' => 'TrickController',
            'trick' => $trick,
            'form' => $form->createView(),
        ]);
    }

    #[Route(
        '/main/image/{uuid}',
        name: 'app_main_image',
        methods: ['GET', 'POST'],
    )]
    #[IsGranted('ROLE_USER')]
    /**
     * Set an image as the main image for a trick.
     *
     * This method allows a user to mark an image as the main image for a trick. The image with the specified UUID
     * will be set as the main image, and the previously designated main image will be unmarked. After updating
     * the main image, the user will be redirected to the trick update page.
     *
     * @param string $uuid The UUID of the image to set as the main image.
     * @param ImageRepository $imageRepository The repository to fetch images.
     * @param EntityManagerInterface $manager The Doctrine EntityManager for managing the database.
     *
     * @return Response A Symfony Response object representing a redirection to the trick update page.
     *
     * @throws NotFoundHttpException if the specified image does not exist.
     */


    public function setMainImage($uuid, ImageRepository $imageRepository, EntityManagerInterface $manager)
    {

        $image = $imageRepository->findOneBy(['uuid' => $uuid], []);
        $actualMain = $imageRepository->findOneBy(['mainImage' => true], []);

        $actualMain->setMainImage(false);
        $image->setMainImage(true);

        $manager->persist($image);
        $manager->flush();
        return $this->redirectToRoute('app_trick_update', ['slug' => $image->getTrick()->getSlug()]);
    }

    #[Route(
        '/delete/image/{uuid}',
        name: 'app_delete_image',
        methods: ['POST', 'DELETE'],
    )]
    #[IsGranted('ROLE_USER')]
    /**
     * Delete an image from a trick.
     *
     * This method allows a user to delete an image associated with a trick using its UUID. It first verifies
     * the CSRF token provided in the request to prevent unauthorized deletions. If the token is valid,
     * the image file is deleted, the image entity is removed from the database, and the user is redirected
     * to the trick update page.
     *
     * @param Request $request The request object used for CSRF token validation.
     * @param string $uuid The UUID of the image to delete.
     * @param ImageRepository $imageRepository The repository to fetch images by UUID.
     * @param EntityManagerInterface $manager The Doctrine EntityManager for managing the database.
     *
     * @return Response A Symfony Response object representing a redirection to the trick update page after image deletion.
     *
     * @throws NotFoundHttpException if the specified image does not exist.
     * @throws AccessDeniedException if the CSRF token is invalid.
     */


    public function deleteImage(Request $request, $uuid, ImageRepository $imageRepository, EntityManagerInterface $manager)
    {

        $image = $imageRepository->findOneBy(['uuid' => $uuid], []);

        $data = $request->get('_token');

        if ($this->isCsrfTokenValid('delete' . $image->getUuid(), $data)) {
            unlink('uploads/image/' . $image->getFileName());

            $manager->remove($image);
            $manager->flush();

            $this->addFlash('success', 'Image.Deleted');

            return $this->redirectToRoute('app_trick_update', ['slug' => $image->getTrick()->getSlug()]);
        }

        $this->addFlash('error', ['Token.Error']);
        return $this->redirectToRoute('app_homepage');
    }

    #[Route(
        '/trick/delete/{slug}',
        name: 'app_delete_trick',
        methods: ['POST', 'DELETE'],
    )]
    #[IsGranted('ROLE_USER')]
    /**
     * Delete a trick.
     *
     * This method allows a user to delete a trick based on its slug. It verifies the action is allowed by validating
     * the CSRF token provided in the request. If the token is valid, it fetches the files linked to the trick and unlinks them.
     * After unlinking the files and removing the trick entity from the database, the user is redirected to the homepage.
     *
     * @param Request $request The request object used for CSRF token validation.
     * @param string $slug The slug of the trick to delete.
     * @param EntityManagerInterface $manager The Doctrine EntityManager for managing the database.
     * @param TrickRepository $trickRepository The repository to fetch tricks by slug.
     *
     * @return Response A Symfony Response object representing a redirection to the homepage after trick deletion.
     *
     * @throws NotFoundHttpException if the specified trick does not exist.
     * @throws AccessDeniedException if the CSRF token is invalid.
     */


    public function deleteTrick(Request $request, $slug, EntityManagerInterface $manager, TrickRepository $trickRepository)
    {
        $data = $request->get('_token');
        $trick = $trickRepository->findOneBy(['slug' =>$slug]);
        if (!$trick) {
            throw $this->createNotFoundException("This trick doesn't exist");
        }


        if ($this->isCsrfTokenValid('delete' . $trick->getSlug(), $data)) {
            foreach ($trick->getImages() as $image) {
                unlink('uploads/image/' . $image->getFileName());
            }

            $manager->remove($trick);
            $manager->flush();

            $this->addFlash('success', ['Trick.Deleted']);

            return $this->redirectToRoute('homepage');
        }

        $this->addFlash('error', ['Token.error']);

        return $this->redirectToRoute('homepage');
    }
}
