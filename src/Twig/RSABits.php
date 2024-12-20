<?php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class RSABits extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('rsabits', [$this, 'rsabitsFilter']),
        ];
    }

    public function rsabitsFilter($rsakey)
    {
        if (empty($rsakey)) {
            return '';
        }
        $keyDetails= $this->getRsaKeyBitLengthFromDnsRecord($rsakey);
        return $keyDetails['bits'];
    }

    function getRsaKeyBitLengthFromDnsRecord($dkimKeyStr) {
        $dkimKeyStr = str_replace(["\n", "\r", " "], "", $dkimKeyStr);
        $binaryKey = base64_decode($dkimKeyStr);
        if ($binaryKey === false) {
            return array('bits' => 0);
        }
        $formattedKey = "-----BEGIN PUBLIC KEY-----\n" . chunk_split(base64_encode($binaryKey), 64, "\n") . "-----END PUBLIC KEY-----";
        $publicKey = openssl_pkey_get_public($formattedKey);
        if ($publicKey === false) {
            return array('bits' => 0);
        }
        $keyDetails = openssl_pkey_get_details($publicKey);
        openssl_free_key($publicKey);
        
        return $keyDetails;
    }
}