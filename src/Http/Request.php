<?php
declare(strict_types=1);

namespace App\Http;

final class Request
{
    private string $method;
    private string $uri;
    private array $query;
    private array $body;
    private array $cookies;
    private array $headers;

    private function __construct() {}

    public static function fromGlobals(): self
    {
        $req = new self();

        $req->method  = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $req->uri     = strtok($_SERVER['REQUEST_URI'], '?');
        $req->query   = $_GET;
        $req->body    = $_POST;
        $req->cookies = $_COOKIE;
        $req->headers = getallheaders();

        return $req;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return rtrim($this->uri, '/') ?: '/';
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $default;
    }

    public function post(string $key, mixed $default = null): mixed
    {
        return $this->body[$key] ?? $default;
    }

    public function header(string $name): ?string
    {
        return $this->headers[$name] ?? null;
    }

    private array $attributes = [];

    public function setAttribute(string $key, mixed $value): void
    {
        $this->attributes[$key] = $value;
    }

    public function getAttribute(string $key, mixed $default = null): mixed
    {
        return $this->attributes[$key] ?? $default;
    }
}
