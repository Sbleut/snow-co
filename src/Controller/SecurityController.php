<?php

namespace App\Controller;

use App\Form\ForgotPasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\UserRepository;
use App\Email\SendMailService;
use App\Email\VeryEmail\Exception\ForgotPasswordEmailExceptionInterface;
use App\Form\ResetPasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/forgot/password', name: 'app_forgot_password')]
    public function forgotPassword(Request $request, AuthenticationUtils $authenticationUtils, SendMailService $email, UserRepository $userRepository, TranslatorInterface $translator): Response
    {
        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userRepository->findOneByUsername($form->get('username')->getData());
            if(isset($user) && $user->isVerified())
            {
                try {
                    $email->sendEmail(
                        'snowtricks@gmail.com', 
                        $user->getEmail(), 
                        'Please Reset your password', 
                        'security/reset_password_email', 
                        [
                            'uuid' => $user->getUuid(),
                            'username' => $user->getUsername(),
                            'token' => $user->getTokenValidator(),
                        ]
                    );
                    // SEt TOken in BDD for the user
                    $this->addFlash('forgot_password_email', 'An email has been send to you to reset your password');
                    return $this->redirectToRoute('app_login');
                } catch (ForgotPasswordEmailExceptionInterface $exception){
                    $this->addFlash('forgot_password_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

                    return $this->redirectToRoute('app_register');
                }                
            }           

        }
        $error = $authenticationUtils->getLastAuthenticationError();
        return $this->render('security/forgot_password.html.twig', [
            'error' => $error,
            'forgotPasswordForm'=> $form->createView(),
        ]);
    }

    #[Route(path: '/reset/password', name: 'app_reset_password')]
    public function ResetPassword(Request $request, UserPasswordHasherInterface $userPasswordHasher, AuthenticationUtils $authenticationUtils, SendMailService $email, UserRepository $userRepository, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        $form = $this->createForm(ResetPasswordFormType::class);
        $form->handleRequest($request);

        // FORM Email instead of username
        // Verify User exist in BDD AND Validated (Validation constraint in form)
        // Verify token from form and in bdd  
        // Handling suppression of token AND Message to REtry operation        
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userRepository->findOneByUsername($form->get('username')->getData());
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_home_homepage');
        }
        $error = $authenticationUtils->getLastAuthenticationError();
        return $this->render('security/reset_password.html.twig', [
            'error' => $error,
            'resetPasswordForm'=> $form->createView(),
        ]);
    }
}
