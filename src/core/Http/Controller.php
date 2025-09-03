<?php

namespace Core\Http;

use Core\Application;

abstract class Controller
{
    protected $app;
    protected $request;
    protected $response;


    public function __construct()
    {
        $this->app = Application::getInstance();
        $this->request = $this->app->getContainer()->get('request');
        $this->response = $this->app->getContainer()->get('response');
    }

    protected function view($template, array $data = [])
    {
        return $this->response->view($template, $data);
    }

    protected function json(array $data, $statusCode = 200)
    {
        return $this->response->json($data, $statusCode);
    }

    protected function redirect($url, $statusCode = 302)
    {
        return $this->response->redirect($url, $statusCode);
    }

    protected function validate(array $rules)
    {
        return $this->request->validate($rules);
    }

    protected function db()
    {
        return $this->app->getContainer()->get('database');
    }
}