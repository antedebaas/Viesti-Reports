<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class HelpController extends AbstractController
{
    private $em;
    private $router;
    private $translator;

    public function __construct(EntityManagerInterface $em, UrlGeneratorInterface $router, TranslatorInterface $translator)
    {
        $this->em = $em;
        $this->router = $router;
        $this->translator = $translator;
    }

    #[Route('/help/', name: 'app_help')]
    public function index(): Response
    {
        return $this->render('help/index.html.twig', [
            'page' => array(
                'menu' => array(
                    'category' => 'help',
                    'item' => 'index'
                ),
                'pretitle' => $this->translator->trans("Help"),
                'title' => $this->translator->trans("Index"),
                'actions' => array(),
            ),
            'breadcrumbs' => array(
                array('name' => $this->translator->trans("help"), 'url' => $this->router->generate('app_help'))
            ),
        ]);
    }

    #[Route('/help/documentation', name: 'app_help_documentation')]
    public function documentation(): Response
    {
        return $this->render('help/documentation.html.twig', [
            'page' => array(
                'menu' => array(
                    'category' => 'help',
                    'item' => 'documentation'
                ),
                'pretitle' => $this->translator->trans("Help"),
                'title' => $this->translator->trans("Documentation"),
                'actions' => array(),
            ),
            'breadcrumbs' => array(
                array('name' => $this->translator->trans("help"), 'url' => $this->router->generate('app_help')),
                array('name' => $this->translator->trans("documentation"), 'url' => $this->router->generate('app_help_documentation')),
            ),
        ]);
    }

    #[Route('/help/licence', name: 'app_help_licence')]
    public function licence(): Response
    {
        return $this->render('help/licence.html.twig', [
            'page' => array(
                'menu' => array(
                    'category' => 'help',
                    'item' => 'licence'
                ),
                'pretitle' => $this->translator->trans("Help"),
                'title' => $this->translator->trans("Licence"),
                'actions' => array(),
            ),
            'breadcrumbs' => array(
                array('name' => $this->translator->trans("help"), 'url' => $this->router->generate('app_help')),
                array('name' => $this->translator->trans("licence"), 'url' => $this->router->generate('app_help_licence')),
            ),
        ]);
    }
}
