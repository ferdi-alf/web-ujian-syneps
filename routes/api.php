<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenticationController;

Route::post('/login', [AuthenticationController::class, 'apiLogin']);

Route::post('/upload-blog-image', function (Request $request) {
    $request->validate([
        'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048'
    ]);

    $path = $request->file('image')->store('blog-content', 'public');
    
    return response()->json([
        'url' => asset('storage/' . $path)
    ]);
})->middleware('auth');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthenticationController::class, 'apiUser']);
    Route::post('/logout', [AuthenticationController::class, 'apiLogout']);

    Route::get('/test', function () {
        return response()->json([
            'success' => true,
            'message' => 'API connection successful!',
            'timestamp' => now()
        ]);
    });
});
