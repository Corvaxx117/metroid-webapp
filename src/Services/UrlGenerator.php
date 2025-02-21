<?php

namespace App\Services;

class UrlGenerator
{
    /**
     * Génère une URL complète en remplaçant les paramètres dynamiques et en ajoutant des paramètres GET.
     * 
     * @param string $path Chemin avec placeholders (ex: "/users/:id")
     * @param array $params Tableau des paramètres à injecter (ex: ['id' => 5, 'filter' => 'active'])
     * @return string URL générée
     */
    public static function getUrlFromPath(string $path, array $params = []): string
    {
        // Base URL définie dans le .env ou config.php
        $baseUrl = APP_BASE_URL;

        // Remplacement des placeholders (ex: :id -> 5)
        foreach ($params as $key => $value) {
            if (strpos($path, ':' . $key) !== false) {
                $path = str_replace(':' . $key, urlencode($value), $path);
                unset($params[$key]); // Supprimer du tableau pour éviter doublon en query string
            }
        }
        // Construit la query string GET
        $queryString = http_build_query($params);

        // Génére l'URL finale
        return rtrim($baseUrl, '/') . '/' . ltrim($path, '/') . ($queryString ? '?' . $queryString : '');
    }

    public function __invoke(string $path, array $params = []): string
    {
        return self::getUrlFromPath($path, $params);
    }
}
