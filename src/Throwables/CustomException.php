<?php

namespace Edsp\ApiGastos\Throwables;

use Exception;
use ReflectionClass;
use ReflectionMethod;
use stdClass;
use Throwable;

class CustomException extends Exception
{
    public function getAll(): object
    {
        return self::formatObj($this);
    }

    private static function formatObj(Throwable $e): object
    {
        $result = new stdClass();

        $exceptionGetters =
            collect(
                (new ReflectionClass(Exception::class))
                    ->getMethods(ReflectionMethod::IS_PUBLIC)
            )->filter(
                fn(ReflectionMethod $method) => str_starts_with($method->name, 'get')
            );

        foreach ($exceptionGetters as $method) {
            $propName = lcfirst(substr($method->getName(), strlen('get')));
            $result->{$propName} = $e->{$method->getName()}();
        }
        if ($result->previous)
            $result->previous = self::formatException($result->previous);
        return $result;
    }

    public static function formatException(Throwable $exception): object
    {
        return self::formatObj($exception);
    }
}