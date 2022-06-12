<?php

namespace Edsp\ApiGastos\Http;

use Edsp\ApiGastos\Throwables\InvalidRouteException;
use Edsp\ApiGastos\Controllers\Controller;
use Exception;
use ReflectionException;
use ReflectionMethod;

class Router
{
    /*public const CONTROLLER_DEFAULT = 'Home';
    public const ACTION_DEFAULT = 'view';*/

    private string $uri;
    private array $explodedUriCache;
    private Controller $controller;
    private string $action;
    private array $arguments;

    /*** @throws Exception */
    public function route(): mixed
    {
        $this->init();
        return $this->invoke();
    }

    /*** @throws Exception */
    private function init(): void
    {
        try {
            $this->setUri();
        } catch (InvalidRouteException $e) {
            throw new Exception("A rota '$this->uri' é inválida!", previous: $e);
        }
    }

    private function invoke(): mixed
    {
        return $this->controller->{$this->action}(...$this->arguments);
    }

    /*** @throws InvalidRouteException|ReflectionException */
    private function setUri(): void
    {
        $uri = rtrim($_GET['uri'] ?? '', '/');

        if (!preg_match('/^[A-z\d\/]*$/', $uri))
            throw new InvalidRouteException("O formato da url é inválido!");

        $this->uri = $uri;
        $this->explodedUriCache = explode('/', $uri);

        $this->setController()->setAction()->setArguments();
    }

    /*** @throws InvalidRouteException */
    private function setController(): self
    {
        $controllerName = array_shift($this->explodedUriCache);
        if (!$controllerName) throw new InvalidRouteException("Nenhum controlador informado!");

        $controllerClass = "Edsp\\ApiGastos\\Controllers\\$controllerName" . 'Controller';

        if (!class_exists($controllerClass))
            throw new InvalidRouteException("O controlador '$controllerName' não foi encontrado!");

        if (!is_subclass_of($controllerClass, Controller::class))
            throw new InvalidRouteException(
                "O controlador '$controllerName' precisa herdar a classe '" . Controller::class . "'!"
            );

        $this->controller = new $controllerClass;

        return $this;
    }

    /*** @throws InvalidRouteException */
    private function setAction(): self
    {
        $actionName = array_shift($this->explodedUriCache);
        if (!$actionName) throw new InvalidRouteException("Nenhuma action informada!");

        if (!method_exists($this->controller, $actionName))
            throw new InvalidRouteException(
                "O método '$actionName' não existe no controlador '" . get_class($this->controller) . "'!"
            );
        $this->action = $actionName;

        return $this;
    }

    /*** @throws ReflectionException|InvalidRouteException */
    private function setArguments(): void
    {
        $reflectionMethod = new ReflectionMethod($this->controller, $this->action);

        $this->arguments = $this->explodedUriCache;
        $this->explodedUriCache = [];

        if (count($this->arguments) < $reflectionMethod->getNumberOfRequiredParameters())
        throw new InvalidRouteException("O número de argumentos fornecidos é insuficiente!");
    }

    public function setHeader(string $name, string $value): void
    {
        header("$name: $value");
    }
}