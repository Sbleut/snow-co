<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Email\SendMailService;
use App\Email\EmailVerifier;
use App\Email\VeryEmail\Exception\VerifyEmailExceptionInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Uid\Uuid;



class RegistrationController extends AbstractController
{

    public function __construct()
    {
    }

    // VerifyEmailHelperInterface $verifyEmailHelper to add

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, SendMailService $email, TranslatorInterface $translator): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        // ADD Avatar 

        if ($form->isSubmitted() && $form->isValid()) {
            
            // encode the plain password
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

            // generate a signed url and email it to the user
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

            $this->addFlash('success', $translator->trans('Registration.Done'));

            return $this->redirectToRoute('homepage');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, UserRepository $userRepository, EmailVerifier $emailVerifier): Response
    {
        $uuid = Uuid::fromString($request->get('uuid'));
        $token = $request->get('token');
        $user = $userRepository->findOneByUuid($uuid->toBinary());

        if(isset($user) && !$user->isVerified())
        {
            try {
                $emailVerifier->handleEmailConfirmation($user, $token);
            } catch (VerifyEmailExceptionInterface $exception) {
                $this->addFlash('error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));
                return $this->redirectToRoute('homepage');
            }
        }        

        // IF from Uuid g
        // validate email confirmation link, sets User::isVerified=true and persists        
        
        $this->addFlash('success', $translator->trans('Email.verify'));

        return $this->redirectToRoute('app_login');
    }
}
