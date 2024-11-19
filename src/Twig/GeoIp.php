<?php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

use GpsLab\Bundle\GeoIP2Bundle\Reader\ReaderFactory;
use GeoIp2\Database\Reader;

class GeoIp extends AbstractExtension
{
    private $reader;
    private $cityDatabasePath = __DIR__ . '/../../var/GeoLite2/GeoLite2-City.mmdb';
    private $countryDatabasePath = __DIR__ . '/../../var/GeoLite2/GeoLite2-Country.mmdb';


    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('geoip', [$this, 'geoipFunction'], ['is_safe' => ['html']]),
        ];
    }

    public function geoipFunction($type, $ip)
    {

        if (empty($ip)) {
            return '';
        }
        $domain = null;
        if (is_array($ip)) {
            $domain = $ip[1];
            $ip = $ip[0];
        }

        try {
            $iso = strtolower($this->reader->country($ip)->country->isoCode);
        } catch (\Exception $e) {
            $iso = '';
        }
        try {
            switch ($type) {
                case 'country' || 'default':
                    $response = '<span class="flag flag-xxs flag-country-'.$iso.'"></span>';

                    if (!is_null($domain)) {
                        return $domain.' '.$response;
                    } else {
                        return $ip.' '.$response;
                    }

                default:
                    return $ip;
            }
        }
        catch (\Exception $e) {
            return $ip;
        }
    }
}