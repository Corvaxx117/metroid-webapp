<?php

namespace App\Services;

use DateTime;
use IntlDateFormatter;

class formatToFrenchDate
{
    /**
     * Formatage d'une date en français.
     *
     * @param string|DateTime $date La date à formater.
     * @param string $format Format de sortie ('short', 'medium', 'long', 'full').
     * @param string $locale Locale (fr_FR par défaut).
     * @return string Date formatée
     */

    public static function formatDate(string|DateTime $date, string $format = 'long', string $locale = 'fr_FR'): string
    {
        // Vérifier si l'entrée est déjà un objet DateTime
        if (!$date instanceof DateTime) {
            $date = new DateTime($date);
        }
        // Déterminer le style du format
        $formats = [
            'short' => IntlDateFormatter::SHORT,
            'medium' => IntlDateFormatter::MEDIUM,
            'long' => IntlDateFormatter::LONG,
            'full' => IntlDateFormatter::FULL
        ];
        // Formatter la date
        $formatter = new IntlDateFormatter(
            $locale,
            $formats[$format] ?? IntlDateFormatter::LONG,
            IntlDateFormatter::NONE,
            'Europe/Paris',
            IntlDateFormatter::GREGORIAN
        );

        return $formatter->format($date);
    }
}
