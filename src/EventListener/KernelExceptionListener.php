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
        if(get_class($exception) == "Symfony\Component\HttpKernel\Exception\NotFoundHttpException") {
            $event->setResponse(
                new Response(
                    $this->templating->render('not_found.html.twig'),
                    Response::HTTP_NOT_FOUND
                )
            );
        }
        $event->stopPropagation();
    }
}
