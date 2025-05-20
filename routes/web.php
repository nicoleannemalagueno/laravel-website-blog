<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Auth;

// Show login form
Route::get('/', function () {
    return view('login');  // Adjust to your login view name
});

// Register routes
Route::get('/register', function () {
    return view('registration');  
});

// Route to handle form submission for registration
Route::post('/register', [UserController::class, 'store']);

// Route to handle login
Route::post('/login', [AuthController::class, 'login']);

// Logout route
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard route (Newsfeed page)
Route::middleware('auth')->group(function () {
    // Route to view all posts (accessible only if logged in)
Route::get('/newsfeed', [PostController::class, 'index'])->name('newsfeed');

    // Route to store a new post
Route::post('/posts', [PostController::class, 'store'])->name('posts.store');

    // Route to view all posts
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
    
    // Route to view and update a single post
Route::put('/update_posts/{postId}', [PostController::class, 'update'])->name('posts.update');

// routes/web.php
Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

});
