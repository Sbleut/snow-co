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
use Symfony\Component\Uid\Uuid;

class SecurityController extends AbstractController
{
    /**
     * Login function is made to authenticate user and redirect to homepage
     *
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser() !== null) {
            return $this->redirectToRoute('homepage');
        }

        // Get the login error if there is one.
        $error = $authenticationUtils->getLastAuthenticationError();
        // Last username entered by the user.
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }


    /**
     * Logout function
     * @return void
     */
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        $this->addFlash('success', 'Logout.Success');
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }


    /**
     * Undocumented function
     *
     * @param Request $request
     * @param AuthenticationUtils $authenticationUtils
     * @param SendMailService $email
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     * @param TranslatorInterface $translator
     * @return Response
     */
    #[Route(path: '/forgot/password', name: 'app_forgot_password')]
    public function forgotPassword(Request $request, AuthenticationUtils $authenticationUtils, SendMailService $email, UserRepository $userRepository, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userRepository->findOneByUserEmail($form->get('email')->getData());
            if (isset($user) && $user->isVerified()) {
                $user->setTokenReset(uniqid());
                $entityManager->flush();
                try {
                    $email->sendEmail(
                        'snowtricks@gmail.com',
                        $user->getEmail(),
                        'Please Reset your password',
                        'security/reset_password_email',
                        [
                            'uuid'      => $user->getUuid(),
                            'username'  => $user->getUsername(),
                            'token'     => $user->getTokenReset(),
                        ]
                    );

                    $this->addFlash('success', $translator->trans('Forgot.Email.Send'));

                    return $this->redirectToRoute('homepage');
                } catch (ForgotPasswordEmailExceptionInterface $exception) {
                    $this->addFlash('forgot_password_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));
                    return $this->redirectToRoute('homepage');
                }
            }
        }
        $error = $authenticationUtils->getLastAuthenticationError();
        return $this->render(
            'security/forgot_password.html.twig',
            [
            'error'                 => $error,
            'forgotPasswordForm'    => $form->createView(),
        ]
        );
    }

    #[Route(path: '/reset/password', name: 'app_reset_password')]
    public function ResetPassword(Request $request, UserPasswordHasherInterface $userPasswordHasher, AuthenticationUtils $authenticationUtils, UserRepository $userRepository, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        $uuid = Uuid::fromString($request->get('uuid'));
        $token = $request->get('token');
        // Verify token from form and in bdd.
        $user = $userRepository->findOneByUuidToken($uuid->toBinary(), $token);

        if ($user === null) {
            $this->addFlash('error', $translator->trans('Reset.Error'));
            return $this->redirectToRoute('homepage');
        }

        $form = $this->createForm(ResetPasswordFormType::class, null, [
            'uuid'  => $uuid,
            'token' => $token, // Pass the UUID to the form type as an option.
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setTokenReset(null);
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $entityManager->flush();

            $this->addFlash('success', 'Reset.Success');
            return $this->redirectToRoute('homepage');
        }
        $error = $authenticationUtils->getLastAuthenticationError();
        return $this->render('security/reset_password.html.twig', [
            'error'             => $error,
            'resetPasswordForm' => $form->createView(),
        ]);
    }
}
