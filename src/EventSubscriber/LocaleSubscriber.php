<?php
namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleSubscriber implements EventSubscriberInterface
{
    private $defaultLocale;

    public function __construct($defaultLocale = 'en')
    {
        $this->defaultLocale = $defaultLocale;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        if (!$request->hasPreviousSession()) {
            return;
        }

        // try to see if the locale has been set as a _locale routing parameter
        if ($locale = $request->attributes->get('_locale')) {
            $request->getSession()->set('_locale', $locale);
        } elseif(array_key_exists('_locale', $_GET)) {
            $request->setLocale($_GET['_locale']);
            $request->getSession()->set('_locale', $_GET['_locale']);
        } else {
            // if no explicit locale has been set on this request
            if(!$request->getSession()->get('_locale')) {
                if(array_key_exists('HTTP_ACCEPT_LANGUAGE',$_SERVER)){
                    $clientLocale = strtolower(str_split($_SERVER['HTTP_ACCEPT_LANGUAGE'], 2)[0]);
                } else {
                    $clientLocale = $this->defaultLocale;
                }
                $request->setLocale($clientLocale);
            } else {
                // or use one from the session
                $request->setLocale($request->getSession()->get('_locale', $this->defaultLocale));
            }

        }
    }

    public static function getSubscribedEvents()
    {
        return [
            // must be registered before (i.e. with a higher priority than) the default Locale listener
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }
}
?>