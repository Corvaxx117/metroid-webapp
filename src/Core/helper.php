<?php

use App\Services\Utils;
use App\Services\FlashMessage;
use App\Services\UrlGenerator;


/**
 * Raccourci pour formater une date
 * @param [type] $date
 * @param string $format
 * @return string
 */
function formatDate($date, string $format = 'long'): string
{
    return Utils::formatDate($date, $format);
}

/**
 * Raccourci pour générer une URL
 * @param string $path
 * @param array $params
 * @return string
 */
function url(string $path, array $params = []): string
{
    return UrlGenerator::getUrlFromPath($path, $params);
}

/**
 * Raccourci pour protéger un texte contre XSS et optionnellement l'entourer avec <p>.
 * @param string|null $text
 * @param bool $wrapInParagraphs
 * @return string
 */
function clean(string|null $text, bool $wrapInParagraphs = true): string
{
    return Utils::sanitizeText($text, $wrapInParagraphs);
}

/**
 * Affiche les messages flash en appelant les templates correspondants.
 * @return void
 */
function renderFlashMessages(): void
{
    FlashMessage::render();
}
