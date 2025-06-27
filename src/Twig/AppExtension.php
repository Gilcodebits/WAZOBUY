<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('ago', [$this, 'ago']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('image_exists', [$this, 'imageExists']),
            new TwigFunction('format_price', [$this, 'formatPrice']),
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

    public function imageExists(string $imagePath): bool
    {
        $fullPath = dirname(__DIR__, 2) . '/public/' . $imagePath;
        return file_exists($fullPath);
    }

    public function formatPrice(float $amount, string $currency = 'FCFA'): string
    {
        $formattedAmount = number_format($amount, 0, ',', ' ');
        return $formattedAmount . ' <span class="currency">' . $currency . '</span>';
    }
} 