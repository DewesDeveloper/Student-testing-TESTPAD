<?php

namespace App\Http\Controllers;

class IndexController extends Controller
{
    public function index()
    {

        $featuredTests = [
            ['title' => 'Основы PHP', 'questions' => 15, 'time' => '20 мин'],
            ['title' => 'Высшая математика', 'questions' => 10, 'time' => '30 мин'],
            ['title' => 'История мира', 'questions' => 25, 'time' => '15 мин'],
        ];

        return view('welcome', compact('featuredTests'));
    }
}
