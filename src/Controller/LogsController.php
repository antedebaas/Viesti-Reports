<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use App\Entity\Logs;

use App\Repository\LogsRepository;

class LogsController extends AbstractController
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

    #[Route('/logs', name: 'app_logs')]
    public function index(): Response
    {
        if (!$this->getUser() || !$this->isGranted('IS_AUTHENTICATED')) {
            return $this->redirectToRoute('app_login');
        }

        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $pages = array("page" => 1,"next" => false,"prev" => false);

        if(isset($_GET["page"]) && $_GET["page"] > 0) {
            $pages["page"] = intval($_GET["page"]);
        } else {
            $pages["page"] = 1;
        }

        if(isset($_GET["perpage"]) && $_GET["perpage"] > 0) {
            $pages["perpage"] = intval($_GET["perpage"]);
        } else {
            $pages["perpage"] = 10;
        }

        $repository = $this->em->getRepository(Logs::class);
        $logs = $repository->findBy(array(), array('id' => 'DESC'), $pages["perpage"], ($pages["page"] - 1) * $pages["perpage"]);
        
        $pages["totalitems"] = $repository->getTotalRows();
        $pages["start"] = $pages["totalitems"] - (($pages["page"] - 1) * $pages["perpage"]); 
        $pages["end"] = $pages["totalitems"] - (($pages["page"] - 1) * $pages["perpage"]) - $pages['perpage'] + 1;
        if ($pages["end"] < 0) {
            $pages["end"] = 1;
        }

        if(count($logs) == 0 && $pages["totalitems"] != 0) {
            return $this->redirectToRoute('app_logs');
        }

        $pages["total"] = ceil($pages["totalitems"] / $pages['perpage']);
        if($pages["totalitems"] / $pages["perpage"] > $pages["page"]) {
            $pages["next"] = true;
        }
        if($pages["page"] - 1 > 0) {
            $pages["prev"] = true;
        }

        return $this->render('logs/index.html.twig', [
            'logs' => $logs,
            'pages' => $pages,
            'page' => array(
                'menu' => array(
                    'category' => 'settings',
                    'item' => 'logs'
                ),
                'pretitle' => $this->translator->trans("Settings"),
                'title' => $this->translator->trans("Logs"),
                'actions' => array(),
            ),
            'breadcrumbs' => array(array('name' => $this->translator->trans("Logs"), 'url' => $this->router->generate('app_logs'))),
        ]);
    }

    #[Route('/logs/details/{id}', name: 'app_logs_details')]
    public function details(Logs $log): Response
    {
        if (!$this->getUser() || !$this->isGranted('IS_AUTHENTICATED')) {
            return $this->redirectToRoute('app_login');
        }

        $details = array();
        $repository = $this->em->getRepository(Logs::class);
        
        foreach($log->getDetails() as $key => $value) {
            $details[$key] = $value;
        }

        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('logs/details.html.twig', [
            'log' => $log,
            'details' => $details,
            'page' => array(
                'menu' => array(
                    'category' => 'settings',
                    'item' => 'logs'
                ),
                'pretitle' => $this->translator->trans("Settings"),
                'title' => $this->translator->trans("Details of log entry #". $log->getId()),
                'actions' => array(),
            ),
            'breadcrumbs' => array(array('name' => $this->translator->trans("Logs"), 'url' => $this->router->generate('app_logs'))),
        ]);
    }
}
