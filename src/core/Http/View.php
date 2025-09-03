<?php

namespace Core\Http;

use Exception;

class View
{
    private $viewPath;
    private $data = [];

    public function __construct()
    {
        $this->viewPath = dirname(__DIR__, 2) . '/app/Views';
    }

    public function render($template, array $data = [])
    {
        $this->data = $data;

        $templateFile = $this->viewPath . str_replace('.', '/', $template) . '.php';

        if (!file_exists($templateFile)) {
            throw new Exception("View template not found: {$template}");
        }

        // Extract data to variables
        extract($this->data);

        // Start output buffering
        ob_start();

        // Include the template
        include $templateFile;

        // Get the content and clean the buffer
        $content = ob_get_clean();

        return $content;
    }

    public function extend($layout, array $data = [])
    {
        $this->data = array_merge($this->data, $data);
        return $this->render($layout, $this->data);
    }

    public function include($partial, array $data = [])
    {
        $partialData = array_merge($this->data, $data);
        return $this->render($partial, $partialData);
    }

    public function escape($value)
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    public function asset($path)
    {
        return '/assets/' . ltrim($path, '/');
    }

    public function url($path)
    {
        return '/' . ltrim($path, '/');
    }


}