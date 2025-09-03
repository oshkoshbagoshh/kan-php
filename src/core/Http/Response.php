<?php

namespace Core\Http;

class Response
{
    private $content;
    private $statusCode = 200;
    private $headers = [];

    public function __construct($content = '', $statusCode = 200, array $headers = [])
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = $headers;

    }

    // Set Content 
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function setHeader($name, $value)
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function setHeaders(array $headers)
    {
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }

    // return JSON

    public function json(array $data, $statusCode = 200)
    {
        $this->setContent(json_encode($data));
        $this->setStatusCode($statusCode);
        $this->setHeader('Content-Type', 'application/json');
        return $this;
    }

    public function redirect($url, $statusCode = 302)
    {
        $this->setstatusCode($statusCode);
        $this->setHeader('Location', $url);
        return $this;
    }

    public function view($template, array $data = [])
    {
        $view = new View();
        $this->setContent($view->render($template, $data));
        return $this;
    }

    public function send()
    {
        // Send status code
        http_response_code($this->statusCode);

        // Send headers
        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }

        // Send content 
        echo $this->content;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getHeaders()
    {
        return $this->headers;
    }



}