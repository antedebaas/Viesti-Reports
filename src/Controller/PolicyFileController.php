<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Domains;

class PolicyFileController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
   
    #[Route('/.well-known/mta-sts.txt', name: 'app_policy_file')]
    public function policyfile(Request $request, EntityManagerInterface $em): Response
    {
        preg_match('/(?:mta-sts\.)+(.*)/', $request->getHost(), $matches);
        $domainname = $matches[1];

        $repository = $this->em->getRepository(Domains::class);
        $domain = $repository->findOneBy(array('fqdn' => $domainname));

        foreach($domain->getMxRecords() as $mxrecord){
            $mxnames[] = $mxrecord->getName();
        }
        
        if($domain){
            $response = $this->render('policy_file/mta-sts.txt.twig', [
                'version' => $domain->getStsVersion(),
                'mode' => $domain->getStsMode(),
                'max_age' => $domain->getStsMaxAge(),
                'mxnames' => $mxnames
            ]);
        } else {
            $response = $this->render('policy_file/empty.txt.twig');
        }
        $response->headers->set('Content-Type', 'text/plain');
            return $response;
    }
}
