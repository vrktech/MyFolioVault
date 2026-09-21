<?php

namespace App\Controllers;

class Guide extends BaseController
{
    public function index(): string
    {
        return view('guide/index', [
            'title'     => 'User Guide - Portfolio Tracker',
            'activeNav' => 'guide',
        ]);
    }
}

