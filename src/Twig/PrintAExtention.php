<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class PrintAExtention extends AbstractExtension
{

    public function getFilters(): array
    {
        return [
            new TwigFilter('printa', [$this, 'printa']),
        ];
    }

    public function printa(object|array $data): string
    {
        return print_r($data);
    }
}
