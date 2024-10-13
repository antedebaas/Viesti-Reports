<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use App\Entity\Users;
use App\Entity\SMTPTLS_Reports;
use App\Entity\Domains;

use App\Form\DeleteFormType;

use App\Repository\SMTPTLS_ReportsRepository;

class SMTPTLS_ReportsController extends AbstractController
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

    #[Route(path: '/reports/smtptls', name: 'app_smtptls_reports', methods: ['GET'])]
    public function index(): Response
    {
        if (!$this->getUser() || !$this->isGranted('IS_AUTHENTICATED')) {
            return $this->redirectToRoute('app_login');
        }

        $pages = array("page" => 1,"next" => false,"prev" => false);

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

        $repository = $this->em->getRepository(Domains::class);
        $userRepository = $this->em->getRepository(Users::class);
        $domains = $repository->findBy(array('id' => $userRepository->findDomains($this->getUser())));

        $repository = $this->em->getRepository(SMTPTLS_Reports::class);
        if(in_array("ROLE_ADMIN", $this->getUser()->getRoles())) {
            $reports = $repository->findBy(array(), array('id' => 'DESC'), $pages["perpage"], ($pages["page"] - 1) * $pages["perpage"]);
        } else {
            $reports = $repository->findBy(array('domain' => $domains), array('id' => 'DESC'), $pages["perpage"], ($pages["page"] - 1) * $pages["perpage"]);
        }
        $pages["totalitems"] = $repository->getTotalRows($reports);
        $pages["start"] = $pages["totalitems"] - (($pages["page"] - 1) * $pages["perpage"]); 
        $pages["end"] = $pages["totalitems"] - (($pages["page"] - 1) * $pages["perpage"]) - $pages['perpage'] + 1;
        if ($pages["end"] < 0) {
            $pages["end"] = 1;
        }

        if(count($reports) == 0 && $pages["totalitems"] != 0 && $pages["page"] != 1) {
            return $this->redirectToRoute('app_smtptls_reports');
        }

        $pages["total"] = ceil($pages["totalitems"] / $pages['perpage']);
        if($pages["totalitems"] / $pages['perpage'] > $pages["page"]) {
            $pages["next"] = true;
        }
        if($pages["page"] - 1 > 0) {
            $pages["prev"] = true;
        }

        return $this->render('smtptls_reports/index.html.twig', [
            'reports' => $reports,
            'pages' => $pages,
            'page' => array(
                'menu' => array(
                    'category' => 'reports',
                    'item' => 'smtptls'
                ),
                'pretitle' => $this->translator->trans("Reports"),
                'title' => $this->translator->trans("SMTP-TLS reports"),
                'actions' => array(),
            ),
            'breadcrumbs' => array(
                array('name' => $this->translator->trans("Reports"), 'url' => $this->router->generate('app_reports')),
                array('name' => $this->translator->trans("SMTPTLS"), 'url' => $this->router->generate('app_smtptls_reports'))
            ),
        ]);
    }

    #[Route(path: '/reports/smtptls/report/{report}', name: 'app_smtptls_reports_report', methods: ['GET'])]
    public function report(
        #[MapQueryParameter(filter: FILTER_VALIDATE_INT)] SMTPTLS_Reports $report
    ): Response {
        if (!$this->getUser() || !$this->isGranted('IS_AUTHENTICATED')) {
            return $this->redirectToRoute('app_login');
        }

        $repository = $this->em->getRepository(SMTPTLS_Reports::class);
        $userRepository = $this->em->getRepository(Users::class);

        if(!$userRepository->denyAccessUnlessOwned($repository->getDomain($report), $this->getUser())) {
            return $this->render('base/error.html.twig', ['page' => array('title'=> 'Not found'), 'message' => $exception->getMessage()]);
        }

        if(!in_array($this->getUser(), $report->getSeen()->getValues())) {
            $report->addSeen($this->getUser());
            $this->em->persist($report);
            $this->em->flush();
        }

        return $this->render('smtptls_reports/report.html.twig', [
            'page' => array(
                'menu' => array(
                    'category' => 'reports',
                    'item' => 'smtptls'
                ),
                'pretitle' => $this->translator->trans("Reports"),
                'title' => $this->translator->trans("SMTP-TLS report #".$report->getId()),
                'actions' => array(),
            ),
            'breadcrumbs' => array(
                array('name' => $this->translator->trans("Reports"), 'url' => $this->router->generate('app_reports')),
                array('name' => $this->translator->trans("SMTPTLS"), 'url' => $this->router->generate('app_smtptls_reports')),
                array('name' => $this->translator->trans("Report")." #".$report->getId(), 'url' => $this->router->generate('app_smtptls_reports'))
            ),
            'report' => $report
        ]);
    }

    #[Route('/reports/smtptls/delete/{report}', name: 'app_smtptls_reports_delete')]
    public function delete(SMTPTLS_Reports $report, Request $request): Response
    {
        if (!$this->getUser() || !$this->isGranted('IS_AUTHENTICATED')) {
            return $this->redirectToRoute('app_login');
        }

        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(DeleteFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $formdata = $form->getData();
            if($formdata['item'] == $report->getId()) {
                $this->addFlash('success', 'Report deleted');

                $this->em->remove($report);
                $this->em->flush();
                
                return $this->redirectToRoute('app_smtptls_reports');
            } else {
                $this->addFlash('danger', 'The id you typed does not match the report id');
            }
        }

        return $this->render('base/delete.html.twig', [
            'page' => array(
                'menu' => array(
                    'category' => 'domains',
                    'item' => 'edit'
                ),
                'pretitle' => $this->translator->trans("SMTP-TLS Reports"),
                'title' => $this->translator->trans("Delete report")." ".$report->getId(),
                'actions' => array(),
            ),
            'item' => $report->getId(),
            'form' => $form,
            'breadcrumbs' => array(
                array('name' => $this->translator->trans("Reports"), 'url' => $this->router->generate('app_reports')),
                array('name' => $this->translator->trans("SMTP-TLS"), 'url' => $this->router->generate('app_smtptls_reports')),
                array('name' => $this->translator->trans("Report")." #".$report->getId(), 'url' => $this->router->generate('app_smtptls_reports')),
                array('name' => $report->getId(), 'url' => $this->router->generate('app_smtptls_reports_delete', ['report' => $report->getId()]))
            ),
        ]);
    }
}
