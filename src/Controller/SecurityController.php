<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use App\Entity\Config;

class SecurityController extends AbstractController
{
    private $em;
    private $translator;

    public function __construct(EntityManagerInterface $em, TranslatorInterface $translator)
    {
        $this->em = $em;
        $this->translator = $translator;
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_dashboard');
        }
        if (!file_exists(dirname(__FILE__).'/../../.env.local')) {
            return $this->redirectToRoute('app_setup');
        }


        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

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

        return $this->render('security/login.html.twig', [
            'page' => array('title' => $this->translator->trans('Login')),
            'last_username' => $lastUsername,
            'error' => $error,
            'enable_registration' => $enable_registration->getValue()
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
