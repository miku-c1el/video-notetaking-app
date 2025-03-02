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

Route::middleware(['auth'])->group(function () {
    Route::get('/notes/index', [NoteController::class, 'index'])->name('notes.index');
    Route::get('/api/notes', [NoteController::class, 'apiIndex']);
    Route::patch('/notes/{note}', [NoteController::class, 'update'])->name('notes.update');
    Route::post('/notes', [NoteController::class, 'store'])->name('notes.store');
    Route::get('/notes/{note}', [NoteController::class, 'show'])->name('notes.show');
    Route::delete('/notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');
});



