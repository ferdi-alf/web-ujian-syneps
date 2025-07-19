<?php

namespace App\Helpers;

class AlertHelper 
{
    public static function success($message, $title = 'Berhasil!') {
        session()->flash('alert', [
            'type' => 'success',
            'title' => $title,
            'message' => $message
        ]);
    }
    
    public static function error($message, $title = 'Error!') {
        session()->flash('alert', [
            'type' => 'error',
            'title' => $title, 
            'message' => $message
        ]);
    }
    
    public static function warning($message, $title = 'Peringatan!') {
        session()->flash('alert', [
            'type' => 'warning',
            'title' => $title,
            'message' => $message
        ]);
    }
    
    public static function info($message, $title = 'Info') {
        session()->flash('alert', [
            'type' => 'info',
            'title' => $title,
            'message' => $message
        ]);
    }
}