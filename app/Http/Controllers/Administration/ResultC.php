<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class ResultC extends Controller
{
    public function result()
    {
        $isUpdatePassword = session('isUpdatePassword');
        $email = session('email');
        $existsEmail = session('existsEmail');

        return view('administration.result', compact('isUpdatePassword', 'email', 'existsEmail'));
    }
}
