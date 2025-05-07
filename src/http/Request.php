<?php

namespace Mini\Http;

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

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->get[$key] ?? $this->post[$key] ?? $default;
    }
}
