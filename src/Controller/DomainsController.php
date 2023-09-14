<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use App\Entity\Domains;

use App\Repository\DomainsRepository;

class DomainsController extends AbstractController
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

    #[Route('/domains', name: 'app_domains')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $pages=array("page"=>1,"next" => false,"prev" => false);

        if(isset($_GET["page"]) && $_GET["page"] > 0)
        {
            $pages["page"] = intval($_GET["page"]);
        } else {
            $pages["page"] = 1;
        }

        if(isset($_GET["perpage"]) && $_GET["perpage"] > 0)
        {
            $pages["perpage"] = intval($_GET["perpage"]);
        } else {
            $pages["perpage"] = 17;
        }

        $repository = $this->em->getRepository(Domains::class);
        $domains = $repository->findAll(array(),array('fqdn' => 'DESC'),$pages["perpage"], ($pages["page"]-1)*$pages["perpage"]);
        $totaldomains = $repository->getTotalRows();

        if(count($domains) == 0 && $totaldomains != 0 ) { return $this->redirectToRoute('app_domains'); }
        
        if($totaldomains/$pages["perpage"] > $pages["page"]) { $pages["next"] = true; }
        if($pages["page"]-1 > 0) { $pages["prev"] = true; }

        return $this->render('domains/index.html.twig', [
            'domains' => $domains,
            'pages' => $pages,
            'menuactive' => 'domains',
            'breadcrumbs' => array(array('name' => $this->translator->trans("Domains"), 'url' => $this->router->generate('app_domains'))),
        ]);
    }
}
