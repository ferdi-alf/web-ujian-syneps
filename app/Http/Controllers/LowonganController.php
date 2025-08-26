<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LowonganController extends Controller
{
    public function index()
    {
        if (auth()->user()->role == 'admin') {
            return view('Dashboard.Lowongan-Admin');
        } else {
            return view('Dashboard.Lowongan-Alumni');
        }
    }
}
