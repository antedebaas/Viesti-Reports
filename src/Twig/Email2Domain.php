<?php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class Email2Domain extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('email2domain', [$this, 'email2domainFilter']),
        ];
    }

    public function email2domainFilter($email)
    {
        if (empty($email)) {
            return '';
        }
        preg_match('/(\S*)?@(\S*)/', $email, $match);
        $domain = $match[2];
        return array($domain, $email);
    }
}