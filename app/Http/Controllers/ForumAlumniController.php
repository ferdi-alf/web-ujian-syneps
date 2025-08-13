<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ForumAlumniController extends Controller
{
    public function index() {
        return view('Dashboard.Forum-Alumni');
    }
}
