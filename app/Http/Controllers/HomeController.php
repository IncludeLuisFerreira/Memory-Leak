<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        $this->render('home');
    }

    public function main()
    {
        $this->render('main');
    }
}
