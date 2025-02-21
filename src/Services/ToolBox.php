<?php

// namespace App\Services;

// /**
//  * Classe utilitaire : cette classe ne contient que des méthodes statiques qui peuvent être appelées
//  * directement sans avoir besoin d'instancier un objet Utils.
//  * Regroupe les méthodes statiques des services disponibles.
//  */

// class ToolBox
// {
//     const ERROR = 'error';
//     const SUCCESS = 'success';
//     const WARNING = 'warning';

//     /**
//      * Génère une URL complète en remplaçant les paramètres dynamiques et en ajoutant des paramètres GET.
//      * @param string $path chemin de la vue
//      * @param array $params paramètres de la vue
//      * @return string URL complète
//      */
//     public static function url(string $path, array $params = []): string
//     {
//         return UrlGenerator::getUrlFromPath($path, $params);
//     }

//     /**
//      * Nettoie une chaîne de caractères contre les attaques XSS.
//      * @param string $string La chaîne à protéger.
//      * @return string La chaîne protégée.
//      */
//     public static function clean(string $string, bool $wrapInParagraphs = true): string
//     {
//         $textHandler = new TextHandler();
//         return $textHandler->sanitizeText($string, $wrapInParagraphs);
//     }
//     /**
//      * Tronque un texte selon un certain nombre de mots.
//      *
//      * @param string $content Le contenu à tronquer.
//      * @param integer $wordLimit Le nombre de mots maximum.
//      * @return string Le contenu tronqué.
//      */
//     public static function truncate(string $content, int $wordLimit = 30): string
//     {
//         $textHandler = new TextHandler();
//         return $textHandler->truncateContent($content, $wordLimit);
//     }

//     /**
//      * Formate une date en français.
//      * @param string|\DateTime $date La date à formater.
//      * @param string $format Le format de la date.
//      * @param string $locale Le locale de la date.
//      * @return string La date formattée.
//      */
//     public static function formatDate(string|\DateTime $date, string $format = 'long', string $locale = 'fr_FR'): string
//     {
//         return formatToFrenchDate::formatDate($date, $format, $locale);
//     }

//     /**
//      * Ajoute un message flash.
//      * @param string $type Le type de message (error, success, warning).
//      * @param string $message Le message à ajouter.
//      * @return void
//      */
//     public static function addFlash(string $type, string $message): void
//     {
//         FlashMessage::add($type, $message);
//     }

//     /**
//      * Vérifie s'il y a un message flash d'un type donné.
//      * @param string $type Le type de message (error, success, warning).
//      * @return bool true si un message flash existe, false sinon.
//      */
//     public static function hasFlash(string $type): bool
//     {
//         return FlashMessage::has($type);
//     }

//     /**
//      * Récupère les messages flash d'un type donné.
//      * @param string $type Le type de message (error, success, warning).
//      * @return array Les messages flash.
//      */
//     public static function getFlash(string $type): array
//     {
//         return FlashMessage::get($type);
//     }

//     /**
//      * Efface tous les messages flash.
//      * @return void
//      */
//     public static function clearFlash(): void
//     {
//         FlashMessage::clear();
//     }

//     /**
//      * Affiche les messages flash.
//      * @return void
//      */
//     public static function renderFlash(): void
//     {
//         FlashMessage::render();
//     }
// }
