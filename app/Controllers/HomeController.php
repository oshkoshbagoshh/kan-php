<?php

namespace App\Controllers;

use Core\Http\Controller;
use Core\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $data = [
            'title' => 'Welcome to MVC Framwork',
            'message' => 'This is a custom PHP MVC framework',
            'users' => $this->getUsers()
        ];

        return $this->view('home.index', $data);
    }

    public function about(Request $request)
    {
        return $this->view('home.about', [
            'title' => 'About Us',
            'content' => 'This is the about page.'
        ]);
    }

    private function getUsers()
    {
        return $this->db()->select('SELECT * FROM users LIMIT 10');
    }


}