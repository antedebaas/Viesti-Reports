<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Config;
use App\Form\RegistrationFormType;
use App\Repository\UsersRepository;
use App\Security\Authenticator;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class RegistrationController extends AbstractController
{
    private $em;
    private EmailVerifier $emailVerifier;
    private RequestStack $requestStack;

    public function __construct(EntityManagerInterface $em, EmailVerifier $emailVerifier, RequestStack $requestStack)
    {
        $this->em = $em;
        $this->emailVerifier = $emailVerifier;
        $this->requestStack = $requestStack;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, Authenticator $authenticator): Response
    {
        $repository = $this->em->getRepository(Config::class);
        $enable_registration = $repository->getKey('enable_registration');
        if(!$enable_registration) {
            $enable_registration = new Config();
            $enable_registration->setName('enable_registration');
            $enable_registration->setValue('0');
            $enable_registration->setType('boolean');
            $this->em->persist($enable_registration);
            $this->em->flush();
        }

        if ($enable_registration->getValue() == false) {
            return $this->render('base/error.html.twig', ['page' => array('title'=> $this->translator->trans('Registration disabled')), 'message' => $this->translator->trans('Registration is disabled.')]);
        }
        $repository = $this->em->getRepository(Users::class);
        if(!file_exists(dirname(__FILE__).'/../../.env.local') || $repository->count([]) == 0) {
            return $this->redirectToRoute('app_setup');
        }

        $user = new Users();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation(
                'app_verify_email',
                $user,
                (new TemplatedEmail())
                    ->from(new Address($_ENV['MAILBOX_USERNAME'], 'Viesti Reports'))
                    ->to($user->getEmail())
                    ->subject($this->translator->trans('Please Confirm your Email'))
                    ->htmlTemplate('emails/confirmation_email.html.twig')
                    ->textTemplate('emails/confirmation_email.txt.twig')
                    ->context(['domain' => $this->requestStack->getCurrentRequest()->getHost()])
            );
            // do anything else you need here, like send an email

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'page' => array('title'=> $this->translator->trans('Create account')),
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, UsersRepository $usersRepository): Response
    {
        $id = $request->query->get('id');

        if (null === $id) {
            return $this->redirectToRoute('app_login');
        }

        $user = $usersRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('app_login');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('danger', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }

        $this->addFlash('success', $this->translator->trans('Your email address has been verified.'));

        return $this->redirectToRoute('app_login');
    }
}
