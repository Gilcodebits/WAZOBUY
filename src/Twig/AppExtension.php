<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('ago', [$this, 'ago']),
        ];
    }

    public function ago(\DateTime $date): string
    {
        $now = new \DateTime();
        $diff = $now->diff($date);
        
        if ($diff->y > 0) {
            return $diff->y == 1 ? 'Il y a 1 an' : "Il y a {$diff->y} ans";
        }
        
        if ($diff->m > 0) {
            return $diff->m == 1 ? 'Il y a 1 mois' : "Il y a {$diff->m} mois";
        }
        
        if ($diff->d > 0) {
            return $diff->d == 1 ? 'Hier' : "Il y a {$diff->d} jours";
        }
        
        if ($diff->h > 0) {
            return $diff->h == 1 ? 'Il y a 1 heure' : "Il y a {$diff->h} heures";
        }
        
        if ($diff->i > 0) {
            return $diff->i == 1 ? 'Il y a 1 minute' : "Il y a {$diff->i} minutes";
        }
        
        return 'À l\'instant';
    }
} 