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

use App\Entity\Domains;
use App\Entity\MXRecords;

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


    #[Route('/domains/edit/{id}', name: 'app_domains_edit')]
    public function edit(Domains $domain, Request $request): Response
    {
        $form = $this->createForm(DomainFormType::class, $domain);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $formdata = $form->getData();
            // foreach($formdata->getMXRecords() as $mxrecord_name) {
            //     #$repository = $this->em->getRepository(MXRecords::class);
            //     #$mxrecord = $repository->findBy(array('domain' => $formdata->getId()));
            //     //gets all MX Records, now forece write and add/remove elements
            //     dump($mxrecord_name);
            // }
            #dd($formdata);

            // $password1 = $form->get("password1")->getData();
            // $password2 = $form->get("password2")->getData();

            // if($password1 == $password2 && $password1 != ""){
            //     $formdata->setPassword(
            //         $userPasswordHasher->hashPassword(
            //             $formdata,
            //             $form->get('password1')->getData()
            //         )
            //     );
            // }

            // $is_admin = $form->get("isAdmin")->getData();

            // $domain_roles = $form->get("roles")->getData();
            // $roles = array();
            // foreach($domain_roles as $domain_role) {
            //     array_push($roles,$domain_role->getId());
            // }
            // if($is_admin == true) {
            //     array_push($roles,"ROLE_ADMIN");
            // }
            // $formdata->setRoles($roles);

            $this->em->persist($formdata);
            $this->em->flush();

            return $this->redirectToRoute('app_domains');
        }
        $setup['users_form'] = $form->createView();

        return $this->render('domains/edit.html.twig', [
            'menuactive' => 'domains',
            'domain' => $domain,
            'form' => $form,
            'breadcrumbs' => array(
                array('name' => $this->translator->trans("Domains"), 'url' => $this->router->generate('app_domains')),
                array('name' => $domain->getFqdn(), 'url' => $this->router->generate('app_domains'))
            ),
        ]);
    }

    // #[Route('/domains/delete/{id}', name: 'app_domains_delete')]
    // public function delete(Domains $domain ): Response
    // {
    //     $this->denyAccessUnlessGranted('ROLE_ADMIN');



    //     $this->em->remove($domain);
    //     $this->em->flush();

    //     return $this->redirectToRoute('app_domains');
    // }
}
