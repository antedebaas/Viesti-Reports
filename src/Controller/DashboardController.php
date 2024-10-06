<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use App\Entity\Users;
use App\Entity\Domains;
use App\Entity\DMARC_Reports;
use App\Entity\SMTPTLS_Reports;
use App\Entity\Logs;

use App\Entity\DMARC_Results;
use App\Entity\SMTPTLS_Policies;

class DashboardController extends AbstractController
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

    #[Route('/', name: 'app_dashboard')]
    public function index(): Response
    {
        if (!file_exists(dirname(__FILE__).'/../../.env.local')) {
            return $this->redirectToRoute('app_setup');
        }

        $userRepository = $this->em->getRepository(Users::class);
        if($userRepository->count([]) == 0) {
            return $this->redirectToRoute('app_setup');
        }

        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $repository = $this->em->getRepository(Domains::class);
        $domains = $repository->findBy(array('id' => $userRepository->findDomains($this->getUser())));

        $repository = $this->em->getRepository(DMARC_Reports::class);
        if(in_array("ROLE_ADMIN", $this->getUser()->getRoles())) {
            $dmarcreports = $repository->findBy(array(), array('id' => 'DESC'), 5, 0);
        } else {
            $dmarcreports = $repository->findBy(array('domain' => $domains), array('id' => 'DESC'), 5, 0);
        }
        $totalreports = $repository->getTotalRows($domains);

        $repository = $this->em->getRepository(SMTPTLS_Reports::class);
        if(in_array("ROLE_ADMIN", $this->getUser()->getRoles())) {
            $smtptlsreports = $repository->findBy(array(), array('id' => 'DESC'), 5, 0);
        } else {
            $smtptlsreports = $repository->findBy(array('domain' => $domains), array('id' => 'DESC'), 5, 0);
        }
        $totalreports = $repository->getTotalRows($domains);

        $repository = $this->em->getRepository(Logs::class);
        $logs = $repository->findBy(array(), array('id' => 'DESC'), 3, 0);

        $dmarcresults_repository = $this->em->getRepository(DMARC_Results::class);
        $smtptlspolicies_repository = $this->em->getRepository(SMTPTLS_Policies::class);
        
        $charts = array(
            'dkim' => array(
                'pass' => $dmarcresults_repository->findaligned("dkim","pass"),
                'fail' => $dmarcresults_repository->findaligned("dkim","fail"),
            ),
            'spf' => array(
                'pass' => $dmarcresults_repository->findaligned("spf","pass"),
                'softfail' => $dmarcresults_repository->findaligned("spf","softfail"),
                'temperror' => $dmarcresults_repository->findaligned("spf","temperror"),
                'fail' => $dmarcresults_repository->findaligned("spf","fail"),
            ),
            'policy' => array (
                'sts' => $smtptlspolicies_repository->findpolicy("sts"),
                'nopolicy' => $smtptlspolicies_repository->findpolicy("no-policy-found"),
            ),
            'stsmode' => array (
                'enforce' => $smtptlspolicies_repository->findstsmode("enforce"),
                'testing' => $smtptlspolicies_repository->findstsmode("testing"),
                'none' => $smtptlspolicies_repository->findstsmode(null),
            ),
        );
        $repository = $this->em->getRepository(DMARC_Reports::class);
        
        return $this->render('dashboard/index.html.twig', [
            'page' => array(
                'menu' => array(
                    'category' => 'home',
                    'item' => 'dashboard'
                ),
                'pretitle' => $this->translator->trans("Home"),
                'title' => $this->translator->trans("Dashboard"),
                'actions' => array(),
            ),
            'charts' => $charts,
            'stats' => $repository->getReportsGroupedByMonth($this->getUser()),
            'breadcrumbs' => array(array('name' => $this->translator->trans("Dashboard"), 'url' => $this->router->generate('app_dashboard'))),
            'dmarcreports' => $dmarcreports,
            'smtptlsreports' => $smtptlsreports,
            'logs' => $logs,
        ]);
    }
}
