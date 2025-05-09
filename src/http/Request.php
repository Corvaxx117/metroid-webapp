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
     * Retrieve a value from the GET or POST arrays using a given key.
     *
     * @param string $key The key to search for in the GET and POST arrays.
     * @param mixed $default The default value to return if the key is not found.
     * @return mixed The value associated with the key, or the default value if the key is not present.
     */

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->get[$key] ?? $this->post[$key] ?? $default;
    }

    /**
     * Checks if the request was made using the POST method.
     *
     * @return bool True if the request was made using the POST method, false otherwise.
     */
    public function isPost(): bool
    {
        return $this->method === 'POST';
    }

    /**
     * Retrieve a value from the POST array using a given key.
     *
     * @param string $key The key to search for in the POST array.
     * @param mixed $default The default value to return if the key is not found.
     * @return mixed The value associated with the key, or the default value if the key is not present.
     */
    public function getPost(string $key, mixed $default = null): mixed
    {
        return $this->post[$key] ?? $default;
    }

    /**
     * Retrieve all values from the POST array.
     *
     * @return array An associative array containing all POST data.
     */

    public function getAllPost(): array
    {
        return $this->post;
    }
}
