<?php

namespace App\Services;

use DateTime;
use IntlDateFormatter;

/**
 * Classe utilitaire : cette classe ne contient que des méthodes statiques qui peuvent être appelées
 * directement sans avoir besoin d'instancier un objet Utils.
 */
class Utils
{
    /**
     * Formatage d'une date en français.
     *
     * @param string|DateTime $date La date à formater.
     * @param string $format Format de sortie ('short', 'medium', 'long', 'full').
     * @param string $locale Locale (fr_FR par défaut).
     * @return string Date formatée
     */
    public static function formatDate($date, string $format = 'long', string $locale = 'fr_FR'): string
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

        $style = $formats[$format] ?? IntlDateFormatter::LONG;

        // Formatter la date
        $formatter = new IntlDateFormatter(
            $locale,
            $style,
            IntlDateFormatter::NONE,
            'Europe/Paris',
            IntlDateFormatter::GREGORIAN
        );

        return $formatter->format($date);
    }


    /**
     * Protège une chaîne contre les attaques XSS et formate les retours à la ligne.
     *
     * @param string|null $string La chaîne à protéger.
     * @param bool $wrapInParagraphs Si true, chaque ligne est entourée de <p>...</p>.
     * @param string $encoding Encodage du texte (UTF-8 par défaut).
     * @return string La chaîne protégée et formatée.
     */
    public static function sanitizeText(string|null $string, bool $wrapInParagraphs = true, string $encoding = 'UTF-8'): string
    {
        if ($string === null) {
            return '';
        }

        // Protection XSS
        $escapedString = htmlspecialchars($string, ENT_QUOTES, $encoding);

        // Si pas besoin d'entourer les lignes, on retourne directement
        if (!$wrapInParagraphs) {
            return $escapedString;
        }

        // Gestion des retours à la ligne
        $lines = explode("\n", $escapedString);
        $formattedString = "";

        foreach ($lines as $line) {
            if (trim($line) !== "") {
                $formattedString .= "<p>$line</p>";
            }
        }

        return $formattedString;
    }
}
