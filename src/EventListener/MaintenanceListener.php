<?php
namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class MaintenanceListener
{
    private $templating;

    public function __construct(Environment $templating)
    {
        $this->templating = $templating;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        $path = preg_match("/(^\/login|^\/2fa)(.*)/i", $event->getRequest()->getrequestUri());

        if ($_ENV['MAINTENANCE'] == "true" && $path == false ) {
             $response = new Response(
                $this->templating->render('base/error.html.twig', ['page' => array('title'=> 'Maintenance'), 'message' => "Viesti Reports is currenty in maintenance mode. Please try again later."]),
                Response::HTTP_SERVICE_UNAVAILABLE
            );
            $event->setResponse($response);
            $event->stopPropagation();
        } else {
            
        }
    }
}