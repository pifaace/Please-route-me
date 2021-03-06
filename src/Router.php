<?php

namespace Piface\Router;

use Piface\Router\Exception\MethodNotAllowedException;
use Psr\Http\Message\ServerRequestInterface;

class Router implements RouterInterface
{
    /**
     * @var RouterContainer
     */
    private $routeContainer;

    public function __construct()
    {
        $this->routeContainer = new RouterContainer();
    }

    /**
     * Register a GET route.
     *
     * @param callable|string $action
     */
    public function get(string $path, string $name, $action): Route
    {
        $route = $this->createRoute($path, $name, $action);
        $route->allows('GET');

        return $this->routeContainer->addRoute($route);
    }

    public function post(string $path, string $name, $action): Route
    {
        $route = $this->createRoute($path, $name, $action);
        $route->allows('POST');

        return $this->routeContainer->addRoute($route);
    }

    public function put(string $path, string $name, $action): Route
    {
        $route = $this->createRoute($path, $name, $action);
        $route->allows('PUT');

        return $this->routeContainer->addRoute($route);
    }

    public function delete(string $path, string $name, $action): Route
    {
        $route = $this->createRoute($path, $name, $action);
        $route->allows('DELETE');

        return $this->routeContainer->addRoute($route);
    }

    public function patch(string $path, string $name, $action): Route
    {
        $route = $this->createRoute($path, $name, $action);
        $route->allows('PATCH');

        return $this->routeContainer->addRoute($route);
    }

    public function options(string $path, string $name, $action): Route
    {
        $route = $this->createRoute($path, $name, $action);
        $route->allows('OPTIONS');

        return $this->routeContainer->addRoute($route);
    }

    /**
     * Compare the given request with routes in the routerContainer.
     */
    public function match(ServerRequestInterface $request): ?Route
    {
        foreach ($this->routeContainer->getRoutes() as $route) {
            if ($this->routeContainer->match($request, $route)) {
                if (!in_array($request->getMethod(), $route->getAllows())) {
                    throw new MethodNotAllowedException($route->getAllows(), $request->getUri()->getPath());
                }

                return $route;
            }
        }

        return null;
    }

    /**
     * @return Route[]
     */
    public function getRoutes(): array
    {
        return $this->routeContainer->getRoutes();
    }

    /**
     * Allow to generate a path for an existing route by its name.
     */
    public function generate(string $name, array $params = []): string
    {
        return $this->routeContainer->generatePath($name, $params);
    }

    /**
     * Create a new route.
     *
     * @param callable|string $action
     */
    private function createRoute(string $path, string $name, $action): Route
    {
        return new Route($path, $name, $action);
    }
}
