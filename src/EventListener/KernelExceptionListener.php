<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Symfony\Component\Routing\RouterInterface;

class KernelExceptionListener
{
    private $templating;
    private $router;

    public function __construct(Environment $templating, RouterInterface $router)
    {
        $this->templating = $templating;
        $this->router = $router;
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        if($_ENV["APP_ENV"]  == 'dev') {
            return;
        }

        switch($exception) {
            case $exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException:
                $response = new Response(
                    $this->templating->render('base/error.html.twig', ['page' => array('title'=> 'Not found'), 'message' => "The object you tried to access could not be found."]),
                    Response::HTTP_NOT_FOUND
                );
                break;
            case $exception instanceof \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException:
                $response = new Response(
                    $this->templating->render('base/error.html.twig',['page' => array('title'=> 'Unauthorized'), 'message' => "Nope! Access denied!"]),
                    Response::HTTP_UNAUTHORIZED
                );
                break;
            case $exception instanceof \Doctrine\DBAL\Exception\TableNotFoundException:
                if (!file_exists(dirname(__FILE__).'/../../.env.local')) {
                    return $this->router->redirectToRoute('app_setup');
                }
                else {
                    $response = new Response(
                        $this->templating->render('base/error.html.twig',['page' => array('title'=> 'Error'), 'message' => $exception->getMessage()]),
                        Response::HTTP_INTERNAL_SERVER_ERROR
                    );
                }
                break;
            default:
                $response = new Response(
                    $this->templating->render('base/error.html.twig',['page' => array('title'=> 'Error'), 'message' => $exception->getMessage()]),
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
                break;
        }
        $event->setResponse($response);
        $event->stopPropagation();
    }
}
