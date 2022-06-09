<?php

namespace Edsp\ApiGastos\Http;

use JsonException;

class Response
{
    public bool $error;
    public array $header;
    public mixed $body;

    public function __construct(mixed $body, bool $error = false)
    {
        $this->error = $error;
        $this->header = headers_list();
        $this->body = $body;
    }

    /*** @throws JsonException */
    public function encode(): string
    {
        return json_encode($this, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
    }
}