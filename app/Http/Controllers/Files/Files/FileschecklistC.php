<?php

namespace App\Http\Controllers\Files\Files;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\MessagesC;

class FileschecklistC extends Controller
{
  public function view($id)
    {
        return view('files.fileschecklist.list', compact('id'));
    }


}