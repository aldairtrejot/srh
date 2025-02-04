<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AboutC extends Controller
{
    public function __invoke()
    {
        return view('home/about');
    }
}
