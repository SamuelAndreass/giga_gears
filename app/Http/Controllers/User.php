<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class User extends Controller
{
    //
    public function viewhome(){
        return view('customer.view');
    }
}
