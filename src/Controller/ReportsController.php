<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use App\Entity\DMARC_Reports;
use App\Entity\MTASTS_Reports;
use App\Entity\Domains;

class ReportsController extends AbstractController
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

    #[Route('/reports', name: 'app_reports')]
    public function index(): Response
    {
        $repository = $this->em->getRepository(Domains::class);
        $domains = $repository->findBy(array('id' => $this->getUser()->getRoles()));

        $repository = $this->em->getRepository(DMARC_Reports::class);
        $dmarc_count = $repository->getTotalRows($domains);

        $repository = $this->em->getRepository(MTASTS_Reports::class);
        $mtasts_count = $repository->getTotalRows($domains, $this->getUser()->getRoles());

        return $this->render('reports/index.html.twig', [
            'menuactive' => 'reports',
            'dmarc_count' => $dmarc_count,
            'mtasts_count' => $mtasts_count,
            'breadcrumbs' => array(array('name' => $this->translator->trans("Reports"), 'url' => $this->router->generate('app_reports'))),
        ]);
    }
}
