<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RecoverC extends Controller
{
    public function __invoke(){
        return view ('administration/recover');
    }
}
