<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\VideoController;
use App\Services\VideoService;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Note;
use App\Models\User;

// Route::middleware(['auth'])->group(function () {
//     Route::resource('notes', NoteController::class);
// });

// Route::get('/notes/{id}', [NoteController::class, 'index']);
// Route::get('/videos/{video}', [VideoController::class, 'show'])->name('videos.show');
// Route::post('/moments', [MomentController::class, 'store'])->name('moments.store');

// モデル勉強

