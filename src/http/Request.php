<?php

namespace Metroid\Http;

class Request
{
    public string $method;
    public string $uri;
    public array $get;
    public array $post;
    public array $server;
    public array $files;
    public array $cookies;
    public array $headers;
    public array $session;

    public function __construct()
    {
        $this->method  = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->uri     = strtok($_SERVER['REQUEST_URI'], '?') ?? '/';
        $this->get     = $_GET;
        $this->post    = $_POST;
        $this->server  = $_SERVER;
        $this->files   = $_FILES;
        $this->cookies = $_COOKIE;
        $this->headers = function_exists('getallheaders') ? getallheaders() : [];
        $this->session = $_SESSION ?? [];
    }

    /**
     * Retourne la valeur de la clé $_GET ou $_POST associée  la clé $key.
     * Si la clé n'existe pas, renvoie la valeur par défaut $default.
     *
     * @param string $key La clé de la valeur à chercher.
     * @param mixed $default La valeur par défaut.
     * @return mixed La valeur trouvée ou la valeur par défaut.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->get[$key] ?? $this->post[$key] ?? $default;
    }


    /**
     * Vérifie si la méthode de la requête HTTP est POST.
     *
     * @return bool Retourne true si la méthode est POST, sinon false.
     */

    public function isPost(): bool
    {
        return $this->method === 'POST';
    }

    /**
     * Retourne la valeur de la clé $_POST associée  la clé $key.
     * Si la clé n'existe pas, renvoie la valeur par défaut $default.
     *
     * @param string $key La clé de la valeur à chercher.
     * @param mixed $default La valeur par défaut.
     * @return mixed La valeur trouvée ou la valeur par défaut.
     */
    public function getPost(string $key, mixed $default = null): mixed
    {
        return $this->post[$key] ?? $default;
    }


    /**
     * Renvoie un tableau associatif contenant les données de la requête HTTP envoyée via la méthode POST.
     *
     * @return array Un tableau associatif contenant les données envoyées via la méthode POST.
     */
    public function getAllPost(): array
    {
        return $this->post;
    }
}
