<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Symfony\Component\HttpFoundation\RequestStack;

class GravatarUrlExtention extends AbstractExtension
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('gravatarurl', [$this, 'gravatarurl']),
        ];
    }

    public function gravatarurl(string $email): string
    {
        $host = $this->requestStack->getCurrentRequest()->getHost();
        if ($host == 'localhost' || '127.0.0.1' || '::1') {
            $host = 'https://epicgreen.fsn1.your-objectstorage.com/viesti';
        }
        
        $size = 32;
        $default = $host."/profile-default.jpg";
        $grav_url = "https://www.gravatar.com/avatar/" . hash( "sha256", strtolower( trim( $email ) ) ) . "?d=" . urlencode( $default ) . "&s=" . $size;

        return $grav_url;
    }
}
