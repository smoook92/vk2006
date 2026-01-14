<?php
declare(strict_types=1);

namespace App\Http;

final class Response
{
    private int $status;
    private array $headers = [];
    private string $body = '';

    public function __construct(string $body = '', int $status = 200)
    {
        $this->body   = $body;
        $this->status = $status;
    }


    public function withHeader(string $name, string $value): self
    {
        $clone = clone $this;
        $clone->headers[$name] = $value;
        return $clone;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getBody(): string
    {
        return $this->body;
    }
}
