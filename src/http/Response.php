<?php

namespace Metroid\Http;

class Response
{
    public function __construct(
        private int $statusCode = 200,
        private array $headers = [],
        private string $content = ''
    ) {}


    public function setStatusCode(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }

    public function setHeaders(array|string $key, ?string $value = null): self
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->headers[$k] = $v;
            }
        } else {
            $this->headers[$key] = $value;
        }
        return $this;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function send(): void
    {
        http_response_code($this->statusCode);
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }
        echo $this->content;
    }
}
