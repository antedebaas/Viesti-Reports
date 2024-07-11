<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class KernelExceptionListener
{
    private $templating;

    public function __construct(Environment $templating)
    {
        $this->templating = $templating;
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        switch($exception) {
            case $exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException:
                $response = new Response(
                    $this->templating->render('not_found.html.twig', ['message' => $exception->getMessage()]),
                    Response::HTTP_NOT_FOUND
                );
                break;
            case $exception instanceof \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException:
                $response = new Response(
                    $this->templating->render('error.html.twig',['message' => "Nope! Access denied!"]),
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
                break;
            default:
                $response = new Response(
                    $this->templating->render('error.html.twig',['message' => $exception->getMessage()]),
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
                break;
        }
        $event->setResponse($response);
        $event->stopPropagation();
    }
}
