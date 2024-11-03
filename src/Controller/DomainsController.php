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
    public function index(Request $request): Response
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

        $bimivmcinfo = $repository->get_bimi_vmc_details($domains);

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
            'timestamp' => array('now' => date('U'), 'soon' => date('U', strtotime('+60 days'))),
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
                    )
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

        $repository = $this->em->getRepository(Domains::class);
        $validation = $repository->findvalidtxtrecords($dnsrecords);
        $bimivmcinfo = $repository->get_bimi_vmc_details(array($domain));

        return $this->render('domains/check.html.twig', [
            'domain' => $domain,
            'validation' => $validation,
            'selectors' => $selectors,
            'bimivmcinfo' => $bimivmcinfo,
            'page' => array(
                'menu' => array(
                    'category' => 'domains',
                    'item' => 'check'
                ),
                'pretitle' => $this->translator->trans("Domains"),
                'title' => $this->translator->trans("Check domain ").$domain->getFqdn(),
                'actions' => array(
                    0 => array(
                        'primary' => false,
                        'name' => $this->translator->trans("VMC Details"),
                        'target' => "#modal-bimivmc",
                        'icon' => "certificate"
                    ),
                ),
            ),
            'breadcrumbs' => array(
                array('name' => $this->translator->trans("Domains"), 'url' => $this->router->generate('app_domains')),
                array('name' => $this->translator->trans("Check domain settings for ").$domain->getFqdn(), 'url' => $this->router->generate('app_domains'))
            ),
        ]);
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
                array('name' => $this->translator->trans("Add domain"), 'url' => $this->router->generate('app_domains_add'))
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
            return $this->render('base/error.html.twig', ['page' => array('title'=> $this->translator->trans('Not found')), 'message' => $exception->getMessage()]);
        }

        $form = $this->createForm(DomainFormType::class, $domain);

        $repository = $this->em->getRepository(Domains::class);
        $bimivmcinfo = $repository->get_bimi_vmc_details(array($domain));

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
                    1 => array(
                        'primary' => false,
                        'name' => $this->translator->trans("VMC Details"),
                        'target' => "#modal-bimivmc",
                        'icon' => "certificate"
                    ),
                ),
            ),
            'domain' => $domain,
            'bimivmcinfo' => $bimivmcinfo,
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
                $this->addFlash('success', $this->translator->trans('Domain deleted'));

                $this->em->remove($domain);
                $this->em->flush();
                
                return $this->redirectToRoute('app_domains');
            } else {
                $this->addFlash('danger', $this->translator->trans('The name you entered does not match the domain name'));
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
