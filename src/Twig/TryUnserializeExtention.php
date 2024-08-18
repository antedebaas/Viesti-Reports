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

    public function try_unserialize($data): array
    {
        if(is_string($data)) {
            try {
                $data = unserialize($data);
            } catch (\Exception $e) {
                $data = array($data);
            }
        } elseif (is_null($data)) {
            $data = array();
        } else {
            $data = array($data);
        }
        return $data;
    }
}
