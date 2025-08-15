<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    public function index() {
        return view('Dashboard.Pembayaran-Masuk');
    }
    public function history() {
        return view('Dashboard.History-Pembayaran');
    }
}
