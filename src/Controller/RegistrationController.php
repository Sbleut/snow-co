<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Email\SendMailService;
use App\Email\EmailVerifier;
use App\Email\VeryEmail\Exception\VerifyEmailExceptionInterface;
use App\Repository\ProfilPicRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Profiler\Profile;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Uid\Uuid;

class RegistrationController extends AbstractController
{
    public function __construct()
    {
    }

    #[Route('/register', name: 'app_register')]
    /**
     * Register function allows to create a new user with unique identifier, a hashed password, a verified email.
     *
     * @param Request $request Stores data from form.
     * @param UserPasswordHasherInterface $userPasswordHasher Tool for hashing password.
     * @param EntityManagerInterface $entityManager Tool to push data to bdd.
     * @param SendMailService $email Tool to verify user email.
     * @param TranslatorInterface $translator use dictionnary to translate email message.
     * @return Response
     */
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, SendMailService $email, ProfilPicRepository $profilPicRepository): Response
    {
        $user = new User();
        $profilPics = $profilPicRepository->findAll();

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // encode the plain password.
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $user->setTokenValidator(uniqid());
            $user->setUuid(Uuid::v6());

            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user.
            $email->sendEmail(
                'snowtricks@gmail.com',
                $user->getEmail(),
                'Please Confirm your Email',
                'registration/confirmation_email',
                [
                    'uuid' => $user->getUuid(),
                    'username' => $user->getUsername(),
                    'token' => $user->getTokenValidator(),
                ]
            );

            $this->addFlash('success', 'Registration.Done');

            return $this->redirectToRoute('homepage');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'profilPics' => $profilPics,
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    /**
     * Undocumented function
     *
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param UserRepository $userRepository
     * @param EmailVerifier $emailVerifier
     * @return Response
     */
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, UserRepository $userRepository, EmailVerifier $emailVerifier): Response
    {
        $uuid = Uuid::fromString($request->get('uuid'));
        $token = $request->get('token');
        $user = $userRepository->findOneByUuid($uuid->toBinary());

        if (isset($user) && !$user->isVerified()) {
            try {
                $emailVerifier->handleEmailConfirmation($user, $token);
            } catch (VerifyEmailExceptionInterface $exception) {
                $this->addFlash('error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));
                return $this->redirectToRoute('homepage');
            }
        }

        // IF from Uuid g.
        // validate email confirmation link, sets User::isVerified=true and persists.

        $this->addFlash('success', $translator->trans('Email.verify'));

        return $this->redirectToRoute('app_login');
    }
}
