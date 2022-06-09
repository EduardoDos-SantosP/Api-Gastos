<?php

namespace Edsp\ApiGastos;

use Edsp\ApiGastos\Http\Response;
use Edsp\ApiGastos\Http\Router;
use Edsp\ApiGastos\Throwables\CustomException;
use Exception;
use JsonException;
use Throwable;

class Application
{
    public static function run(): void
    {
        set_error_handler(fn($severity, $message) => throw new Exception($message));

        $router = new Router();
        $router->setHeader('Content-type', 'application/json');

        try {
            $response = new Response($router->route());
        } catch (Throwable $e) {
            $exception = CustomException::formatException($e);
            $response = new Response($exception, true);
        } finally {
            try {
                $encodedResponse = $response->encode();
            } catch (JsonException $je) {
                $encodedResponse = $je->getMessage();
            } finally {
                echo $encodedResponse;
            }
        }
    }
}