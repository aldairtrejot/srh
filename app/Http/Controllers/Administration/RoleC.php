<?php

namespace App\Http\Controllers\administration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Class ROLE
class RoleC extends Controller
{
    // La función retorna la vista role
    public function __invoke()
    {
        return view('administration/role');
    }
}
