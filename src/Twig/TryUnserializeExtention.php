<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TryUnserializeExtention extends AbstractExtension
{

    public function getFilters(): array
    {
        return [
            new TwigFilter('try_unserialize', [$this, 'try_unserialize']),
        ];
    }

    public function try_unserialize(string $data): array
    {
        try {
            return unserialize($data);
        } catch (\Exception $e) {
        }
        return array($data);
    }
}
