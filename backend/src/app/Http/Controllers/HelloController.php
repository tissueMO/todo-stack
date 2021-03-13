<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HelloController extends Controller
{
    public function index()
    {
        abort(403, 'TESTだよ');
        return view('index', [
            'message' => [
                'Hello',
                'World',
                '!',
            ],
        ]);
    }
}
