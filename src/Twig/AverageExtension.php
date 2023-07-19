<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class AverageExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('average', [$this, 'calculateAverage'])
        ];
    }

    public function calculateAverage($avis): int
    {
        $totalNotes = 0;
        $nbAvis = count($avis);

        foreach ($avis as $avi) {
            $totalNotes += $avi->getRating();
        }

        // Éviter une division par zéro
        $moyenne = $nbAvis > 0 ? $totalNotes / $nbAvis : 0;

        $moyenneArrondie = ceil($moyenne);

        return $moyenneArrondie;
    }
}
