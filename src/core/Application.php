<?php

namespace Core;

use Core\Http\Request;
use Core\Http\Response;
use Core\Routing\Router;
use Core\Database\Database;
use Exception;

class Application
{
    private static $instance;
    private $container;

    private $router;

    private $config;

    public function __construct($basePath)
    {
        self::$instance = $this;
        $this->container = new Container();
        $this->router = new Router();
        $this->basePath = $basePath;

        $this->loadConfiguration();
        $this->registerServices();





    }

    public static function getInstance(): Application
    {
        return self::$instance;

    }

    private function loadConfiguration()
    {
        $this->config = [
            'database' => require $this->basePath . '/config/database.php',
            'app' => require $this->basePath . '/config/app.php'
        ];
    }

    private function registerServices()
    {
        // Register Database
        $this->container->bind('database', function () {
            return new Database($this->config['database']);

        });

        // Register Request
        $this->container->bind('request', function () {
            return new Request();

        });

        // Register Response
        $this->container->bind('response', function () {
            return new Response();
        });


    }

    public function run()
    {

        try {
            $request = $this->container->get('request');
            $response = $this->router->dispatch($request);

            $response->send();
        } catch (Exception $e) {
            $this->handleException($e);
        }

    }

    private function handleException(Exception $e)
    {
        http_response_code(500);

        if ($this->config['app']['debug']) {
            echo "<h1>Error: " . $e->getMessage() . "</h1>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
        } else {
            echo "Internal Server Error";
        }


    }

    /**
     * get
     *
     * @param  mixed $path
     * @param  mixed $handler
     * @return void
     */
    public function get($path, $handler)
    {
        $this->router->get($path, $handler);
        return $this;
    }

    public function post($path, $handler)
    {
        $this->router->post($path, $handler);
        return $this;
    }

    public function put($path, $handler)
    {
        $this->router->put($path, $handler);
        return $this;
    }

    public function delete($path, $handler)
    {
        $this->router->delete($path, $handler);
        return $this;
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function getConfig($key = null)
    {
        if ($key === null) {
            return $this->config;
        }

        return $this->config[$key] ?? null;
    }









}