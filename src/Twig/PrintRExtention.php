<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class PrintRExtention extends AbstractExtension
{

    public function getFilters(): array
    {
        return [
            new TwigFilter('printr', [$this, 'printr']),
        ];
    }

    public function printr(array $data): string
    {
        return print_r($data);
    }
}
