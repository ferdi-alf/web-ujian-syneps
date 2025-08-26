<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ForumAlumniController extends Controller
{
    public function index() {
        if (auth()->user()->role == 'admin') {
            return view('Dashboard.Forum-Alumni');
        } else {
            return view('Dashboard.Alumni-Forum');
        }
    }
}
