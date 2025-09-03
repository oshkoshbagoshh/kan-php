<?php

namespace Core\Routing;

use Core\Http\Request;
use Core\Http\Response;
use Core\Http\Application;
use Exception;


class Router
{
    private $routes = [];
    private $middleware = [];

    public function get($path, $handler)
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post($path, $handler)
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function put($path, $handler)
    {
        $this->addRoute('PUT', $path, $handler);
    }

    public function delete($path, $handler)
    {
        $this->addRoute('DELETE', $path, $handler);
    }

    public function middlware($middleware)
    {
        $this->middlware[] = $middleware;
        return $this;
    }

    private function addRoute($method, $path, $handler)
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'middleware' => $this->middleware
        ];

        // Reset middleware for next route
        $this->middleware = [];
    }

    public function dispatch(Request $request)
    {
        $method = $request->getMethod();
        $path = $request->getPath();

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchPath($route['path'], $path)) {
                return $this->handleRoute($route, $request, $this->extractParams($route['path'], $path));
            }


        }

        throw new Exception("Route not found: {$method} {$path}", 404);


    }

    private function matchPath($routePath, $requestPath)
    {
        // Convert route path to regex
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';

        return preg_match($pattern, $requestPath);

    }

    private function extractParams($routePath, $requestPath)
    {
        $params = [];

        // Extract parameter names from route path
        preg_match_all('/\{([^}]+)\}/', $routePath, $paramNames);

        // Extract parameter values from request path
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $requestPath, $matches)) {
            array_shift($matches); // Remove full match

            foreach ($paramNames[1] as $index => $name) {
                $params[$name] = $matches[$index] ?? null;
            }
        }

        return $params;


    }

    private function handleRoute($route, Request $request, array $params)
    {
        $request->setParams($params);

        // Execute middleware
        foreach ($route['middleware'] as $middleware) {
            $middlewareInstance = Application::getInstance()->getContainer()->get($middleware);
            $middlewareInstance->handle($request);
        }

        // Handle the route
        if (is_string($route['handler'])) {
            return $this->handleControllerAction($route['handler'], $request);
        } elseif (is_callable($route['handler'])) {
            return $route['handler']($request);
        }

        throw new Exception("Invalid route handler");
    }

    private function handleControllerAction($handler, Request $request)
    {
        list($controller, $action) = explode('@', $handler);

        $controllerClass = "App\\Controllers\\{$controller}";

        if (!class_exists($controllerClass)) {
            throw new Exception("Controller {$controllerClass} not found");
        }

        $controllerInstance = Application::getInstance()->getContainer()->get($controllerClass);

        if (!method_exists($controllerInstance, $action)) {
            throw new Exception("Action {$action} not found in {$controllerClass}");
        }

        return $controllerInstance->$action($request);
    }

}