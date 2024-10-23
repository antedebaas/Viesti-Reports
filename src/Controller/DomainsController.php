<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use App\Form\DomainFormType;
use App\Form\DeleteFormType;

use App\Entity\Users;
use App\Entity\Domains;
use App\Entity\MXRecords;

use App\Repository\DomainsRepository;

use Ante\DnsParser\Dns;
use Ante\DnsParser\TXTRecords;
use App\Enums\TXTRecordStates;

class DomainsController extends AbstractController
{
    private $em;
    private $router;
    private $translator;
    private DomainsRepository $DomainsRepository;

    public function __construct(EntityManagerInterface $em, UrlGeneratorInterface $router, TranslatorInterface $translator, DomainsRepository $domainsRepository)
    {
        $this->em = $em;
        $this->router = $router;
        $this->translator = $translator;
        $this->DomainsRepository = $domainsRepository;
    }

    #[Route('/domains', name: 'app_domains')]
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
            $pages["perpage"] = intval($_GET["perpage"]);
        } else {
            $pages["perpage"] = 10;
        }

        $repository = $this->em->getRepository(Domains::class);
        $usersRepository = $this->em->getRepository(Users::class);

        if(in_array("ROLE_ADMIN", $this->getUser()->getRoles())) {
            $domains = $repository->findAll(array(), array('fqdn' => 'DESC'), $pages["perpage"], ($pages["page"] - 1) * $pages["perpage"]);
        } else {
            $domains = $repository->findOwnedBy($usersRepository->findDomains($this->getUser()), array('fqdn' => 'DESC'), $pages["perpage"], ($pages["page"] - 1) * $pages["perpage"]);
        }

        $bimivmcinfo = array();
        foreach($domains as $key => $domain) {
            if(!is_null($domain->getBimivmcfile())){
                $bimivmcinfo[$domain->getId()] = openssl_x509_parse($domain->getBimivmcfile());
            }
        }

        $pages["totalitems"] = $repository->getTotalRows();
        $pages["start"] = $pages["totalitems"] - (($pages["page"] - 1) * $pages["perpage"]); 
        $pages["end"] = $pages["totalitems"] - (($pages["page"] - 1) * $pages["perpage"]) - $pages['perpage'] + 1;
        if ($pages["end"] < 0) {
            $pages["end"] = 1;
        }

        if(count($domains) == 0 && $pages["totalitems"] != 0) {
            return $this->redirectToRoute('app_domains');
        }

        if($pages["totalitems"] / $pages["perpage"] > $pages["page"]) {
            $pages["next"] = true;
        }
        if($pages["page"] - 1 > 0) {
            $pages["prev"] = true;
        }
        $pages["total"] = ceil($pages["totalitems"] / $pages['perpage']);

        return $this->render('domains/index.html.twig', [
            'domains' => $domains,
            'bimivmcinfo' => $bimivmcinfo,
            'pages' => $pages,
            'page' => array(
                'menu' => array(
                    'category' => 'domains',
                    'item' => 'domains'
                ),
                'pretitle' => $this->translator->trans("Domains"),
                'title' => $this->translator->trans("Home"),
                'actions' => array(
                    0 => array(
                        'primary' => true,
                        'name' => $this->translator->trans("Add"),
                        'target' => $this->router->generate('app_domains_add'),
                        'icon' => "plus"
                    ),
                ),
            ),
            'breadcrumbs' => array(array('name' => $this->translator->trans("Domains"), 'url' => $this->router->generate('app_domains'))),
        ]);
    }

    #[Route('/domains/check/{id}', name: 'app_domains_check')]
    public function check(Domains $domain, Request $request): Response
    {
        if (!$this->getUser() || !$this->isGranted('IS_AUTHENTICATED')) {
            return $this->redirectToRoute('app_login');
        }

        $usersRepository = $this->em->getRepository(Users::class);
        if(!$usersRepository->denyAccessUnlessOwned(array($domain->getId()), $this->getUser())) {
            return $this->render('base/error.html.twig', ['page' => array('title'=> 'Not found'), 'message' => $exception->getMessage()]);
        }

        $dkimselector = $domain->getDkimselector();
        if($dkimselector == null || $dkimselector == '') { $dkimselector = 'default'; }
        $bimiselector = $domain->getBimiselector();
        if($bimiselector == null || $bimiselector == '') { $bimiselector = 'default'; }
        $selectors = array(
            'dkim' => $dkimselector,
            'bimi' => $bimiselector,
        );

        $dnsrecords = array();

        $dns = new Dns();
        $dnsrecords = array_merge($dnsrecords,$dns->getRecords($domain->getFqdn(), 'TXT'));
        $dnsrecords = array_merge($dnsrecords,$dns->getRecords($dkimselector.'._domainkey.'.$domain->getFqdn(), 'TXT'));
        $dnsrecords = array_merge($dnsrecords,$dns->getRecords($bimiselector.'._bimi.'.$domain->getFqdn(), 'TXT'));
        $dnsrecords = array_merge($dnsrecords,$dns->getRecords('_mta-sts.'.$domain->getFqdn(), 'TXT'));
        $dnsrecords = array_merge($dnsrecords,$dns->getRecords('_dmarc.'.$domain->getFqdn(), 'TXT'));
        $dnsrecords = array_merge($dnsrecords,$dns->getRecords('_smtp._tls.'.$domain->getFqdn(), 'TXT'));
        
        //dnssec
        //mx ptr records

        $validation = $this->findvalidtxtrecords($dnsrecords);

        return $this->render('domains/check.html.twig', [
            'domain' => $domain,
            'validation' => $validation,
            'selectors' => $selectors,
            'page' => array(
                'menu' => array(
                    'category' => 'domains',
                    'item' => 'check'
                ),
                'pretitle' => $this->translator->trans("Domains"),
                'title' => $this->translator->trans("Check domain ").$domain->getFqdn(),
                'actions' => array(),
            ),
            'breadcrumbs' => array(
                array('name' => $this->translator->trans("Domains"), 'url' => $this->router->generate('app_domains')),
                array('name' => $this->translator->trans("Check domain settings for ").$domain->getFqdn(), 'url' => $this->router->generate('app_domains'))
            ),
        ]);
    }

    private function findvalidtxtrecords(array $records): array {
        $result = array(
            'SPF'=> array(new TXTRecords\SPF1(""),TXTRecordStates::Fail),
            'DKIM'=> array(new TXTRecords\DKIM1(""),TXTRecordStates::Fail),
            'BIMI'=> array(new TXTRecords\BIMI1(""),TXTRecordStates::Fail),
            'STS'=> array(new TXTRecords\STSV1(""),TXTRecordStates::Fail),
            'DMARC'=> array(new TXTRecords\DMARC1(""),TXTRecordStates::Fail),
            'TLSRPT'=> array(new TXTRecords\TLSRPTV1(""),TXTRecordStates::Fail),
        );

        foreach($records as $record) {
            if($record->v()->version() == 1) {
                $result[$record->v()->type()] = array($record->v(),TXTRecordStates::Good);
            }
        }
        
        return $result;
    }

    #[Route('/domains/add', name: 'app_domains_add')]
    public function add(Request $request): Response
    {
        if (!$this->getUser() || !$this->isGranted('IS_AUTHENTICATED')) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(DomainFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formdata = $form->getData();

            $bimisvgfile = $form->get('bimisvgfile')->getData();
            if ($bimisvgfile) {
                $validate = $this->DomainsRepository->validate_bimiv1_svg_file(file_get_contents($bimisvgfile));
                if (!$validate['result']) {
                    foreach($validate['errors'] as $error) {
                        $this->addFlash('danger', $error);
                    }
                } else {
                    $formdata->setBimisvgfile(file_get_contents($bimisvgfile));
                }
            }
            $bimivmcfile = $form->get('bimivmcfile')->getData();
            if ($bimivmcfile) {
                $formdata->setBimivmcfile(file_get_contents($bimivmcfile));
            }

            $this->em->persist($formdata);
            $this->em->flush();

            $usersRepository = $this->em->getRepository(Users::class);
            $user = $this->getUser();
            if(!$usersRepository->findIsAdmin($user->getId())) {
                $user->addDomain($formdata);
                $this->em->persist($user);
                $this->em->flush();
            }

            return $this->redirectToRoute('app_domains');
        }
        $setup['users_form'] = $form->createView();

        $dns_info = array(
            'now' => new \DateTime('now'),
            'ip' => $request->server->get('SERVER_ADDR'),
            'email' => $this->getParameter('app.mailbox_username'),
        );

        return $this->render('domains/edit.html.twig', [
            'page' => array(
                'menu' => array(
                    'category' => 'domains',
                    'item' => 'add'
                ),
                'pretitle' => $this->translator->trans("Domains"),
                'title' => $this->translator->trans("Add domain"),
                'actions' => array(
                    0 => array(
                        'primary' => false,
                        'name' => $this->translator->trans("Example DNS Records"),
                        'target' => "#modal-dnssettings",
                        'icon' => "globe"
                    ),
                ),
            ),
            'domain' => null,
            'dns_info' => $dns_info,
            'form' => $form,
            'breadcrumbs' => array(
                array('name' => $this->translator->trans("Domains"), 'url' => $this->router->generate('app_domains')),
                array('name' => "Add domain", 'url' => $this->router->generate('app_domains_add'))
            ),
        ]);
    }

    #[Route('/domains/edit/{id}', name: 'app_domains_edit')]
    public function edit(Domains $domain, Request $request): Response
    {
        if (!$this->getUser() || !$this->isGranted('IS_AUTHENTICATED')) {
            return $this->redirectToRoute('app_login');
        }

        $usersRepository = $this->em->getRepository(Users::class);
        if(!$usersRepository->denyAccessUnlessOwned(array($domain->getId()), $this->getUser())) {
            return $this->render('base/error.html.twig', ['page' => array('title'=> 'Not found'), 'message' => $exception->getMessage()]);
        }

        $form = $this->createForm(DomainFormType::class, $domain);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formdata = $form->getData();
            
            $bimisvgfile = $form->get('bimisvgfile')->getData();
            if ($bimisvgfile) {
                $validate = $this->DomainsRepository->validate_bimiv1_svg_file(file_get_contents($bimisvgfile));
                if (!$validate['result']) {
                    foreach($validate['errors'] as $error) {
                        $this->addFlash('danger', $error);
                    }
                } else {
                    $formdata->setBimisvgfile(file_get_contents($bimisvgfile));
                }
            }
            $bimivmcfile = $form->get('bimivmcfile')->getData();
            if ($bimivmcfile) {
                $formdata->setBimivmcfile(file_get_contents($bimivmcfile));
            }

            $this->em->persist($formdata);
            $this->em->flush();

            return $this->redirectToRoute('app_domains');
        }
        $setup['users_form'] = $form->createView();

        $dns_info = array(
            'now' => new \DateTime('now'),
            'ip' => $request->server->get('SERVER_ADDR'),
            'email' => $this->getParameter('app.mailbox_username'),
        );

        return $this->render('domains/edit.html.twig', [
            'page' => array(
                'menu' => array(
                    'category' => 'domains',
                    'item' => 'edit'
                ),
                'pretitle' => $this->translator->trans("Domains"),
                'title' => $this->translator->trans("Edit domain")." ".$domain->getFqdn(),
                'actions' => array(
                    0 => array(
                        'primary' => false,
                        'name' => $this->translator->trans("Example DNS Records"),
                        'target' => "#modal-dnssettings",
                        'icon' => "globe"
                    ),
                ),
            ),
            'domain' => $domain,
            'dns_info' => $dns_info,
            'form' => $form,
            'breadcrumbs' => array(
                array('name' => $this->translator->trans("Domains"), 'url' => $this->router->generate('app_domains')),
                array('name' => $domain->getFqdn(), 'url' => $this->router->generate('app_domains'))
            ),
        ]);
    }

    #[Route('/domains/delete/{id}', name: 'app_domains_delete')]
    public function delete(Domains $domain, Request $request): Response
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
            if($formdata['item'] == $domain->getFqdn()) {
                $this->addFlash('success', 'Domain deleted');

                $this->em->remove($domain);
                $this->em->flush();
                
                return $this->redirectToRoute('app_domains');
            } else {
                $this->addFlash('danger', 'The name you typed does not match the domain name');
            }
        }

        return $this->render('base/delete.html.twig', [
            'page' => array(
                'menu' => array(
                    'category' => 'domains',
                    'item' => 'edit'
                ),
                'pretitle' => $this->translator->trans("Domains"),
                'title' => $this->translator->trans("Delete domain")." ".$domain->getFqdn(),
                'actions' => array(),
            ),
            'item' => $domain->getFqdn(),
            'form' => $form,
            'breadcrumbs' => array(
                array('name' => $this->translator->trans("Domains"), 'url' => $this->router->generate('app_domains')),
                array('name' => $domain->getFqdn(), 'url' => $this->router->generate('app_domains')),
                array('name' => $domain->getFqdn(), 'url' => $this->router->generate('app_domains_delete', ['id' => $domain->getId()]))
            ),
        ]);
    }
}
