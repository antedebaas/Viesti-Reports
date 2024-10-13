<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use App\Entity\Config;
use App\Form\ConfigurationFormType;
use App\Form\ConfigurationItemFormType;

class ConfigurationController extends AbstractController
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

    #[Route('/configuration', name: 'app_configuration')]
    public function index(Request $request): Response
    {
        $repository = $this->em->getRepository(Config::class);
        $entries = $repository->findAll();

        $form = $this->createForm(ConfigurationFormType::class, ['entries' => $entries]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formdata = $form->getData();
            
            foreach ($formdata as $key => $value) {
                if($key == "entries") {
                    continue;
                }
                $entry = $repository->findOneBy(['key' => $key]);
                if($entry == null) {
                    continue;
                }
                if($entry->getType() == 'boolean') {
                    $entry->setValue($value == 'true' ? '1' : '0');
                } else {
                    $entry->setValue($value);
                }
                $this->em->persist($entry);
            }
            $this->em->flush();
        }

        $pages = array("page" => 1,"next" => false, "prev" => false);

        if(isset($_GET["page"]) && $_GET["page"] > 0) {
            $pages["page"] = intval($_GET["page"]);
        } else {
            $pages["page"] = 1;
        }

        if(isset($_GET["perpage"]) && $_GET["perpage"] > 0) {
            $pages['perpage'] = intval($_GET["perpage"]);
        } else {
            $pages['perpage'] = 10;
        }

        $pages["totalitems"] = $repository->getTotalRows($entries);
        $pages["start"] = $pages["totalitems"] - (($pages["page"] - 1) * $pages["perpage"]); 
        $pages["end"] = $pages["totalitems"] - (($pages["page"] - 1) * $pages["perpage"]) - $pages['perpage'] + 1;
        if ($pages["end"] < 0) {
            $pages["end"] = 1;
        }

        $pages["total"] = ceil($pages["totalitems"] / $pages['perpage']);
        if($pages["totalitems"] / $pages['perpage'] > $pages["page"]) {
            $pages["next"] = true;
        }
        if($pages["page"] - 1 > 0) {
            $pages["prev"] = true;
        }

        return $this->render('configuration/index.html.twig', [
            'pages' => $pages,
            'page' => array(
                'menu' => array(
                    'category' => 'settings',
                    'item' => 'configuration',
                ),
                'pretitle' => $this->translator->trans("Settings"),
                'title' => $this->translator->trans("Configuration"),
                'actions' => array(),
            ),
            'form' => $form->createView(),
            'breadcrumbs' => array(array('name' => $this->translator->trans("Settings"), 'url' => $this->router->generate('app_configuration'))),
        ]);
    }
}
