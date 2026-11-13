<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Errors extends Controller
{
    protected $helpers = ['html'];

    public function error404()
    {
        return view('errors/error_404');
    }
}
