<?php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

use Ante\DnsParser\Dns;
use GeoIp2\Database\Reader;

class Domain2Ip extends AbstractExtension
{
    private $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('domain2ip', [$this, 'domain2ipFilter']),
        ];
    }

    public function domain2ipFilter($domain)
    {
        if (empty($domain)) {
            return '';
        }
        $email = null;
        if (is_array($domain)) {
            $email = $domain[1];
            $domain = $domain[0];
        }

        $dns = new Dns();
        try {
            $ipv4 = $dns->getRecords($domain, 'A')[0];
            if(!is_null($ipv4)) {
                $ipv4 = $ipv4->toArray()["ip"];
            } else {
                $ipv4 = '';
            }
        } catch (\Exception $e) {
            $ipv4 = '';
        }
        try {
            $ipv6 = $dns->getRecords($domain, 'AAAA')[0];
            if(!is_null($ipv6)) {
                $ipv6 = $ipv6->toArray()["ipv6"];
            } else {
                $ipv6 = '';
            }
        } catch (\Exception $e) {
            $ipv6 = '';
        }

        if(!empty($ipv6)) {
            $ip = $ipv6;
        } else {
            $ip = $ipv4;
        }

        if (!is_null($email)) {
            return array($ip, $email);
        } else {
            return array($ip, $domain);
        }
    }
} 