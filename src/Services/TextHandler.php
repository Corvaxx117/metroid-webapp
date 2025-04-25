<?php

namespace Mini\Services;

class TextHandler
{
    /**
     * Protège une chaîne contre les attaques XSS et formate les retours à la ligne.
     *
     * @param string|null $string La chaîne à protéger.
     * @param bool $wrapInParagraphs Si true, chaque ligne est entourée de <p>...</p>.
     * @param string $encoding Encodage du texte (UTF-8 par défaut).
     * @return string La chaîne protégée et formatée.
     */
    public function clean(string|null $string, bool $wrapInParagraphs = true, string $encoding = 'UTF-8'): string
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

    /**
     * Fonction pour tronquer le texte après un certain nombre de mots.
     * @param string $content Le contenu à tronquer.
     * @param int $wordLimit Le nombre de mots maximum.
     * @return string Le contenu tronqué.
     */
    public function truncate(string $content, int $wordLimit = 30): string
    {
        $words = explode(' ', $content);

        if (count($words) > $wordLimit) {
            return implode(' ', array_slice($words, 0, $wordLimit)) . '...';
        }

        return $content;
    }
}
