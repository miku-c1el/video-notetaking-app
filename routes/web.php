<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\ExploreVideoController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\MomentController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\NoteTagController;
use App\Http\Controllers\Api\NoteApiController;
use App\Services\VideoService;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Note;
use App\Models\User;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome');
});


Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/password', [PasswordController::class, 'update'])->name('password.update');
});

require __DIR__.'/auth.php';
/*
|--------------------------------------------------------------------------
| Youtube Routes
|--------------------------------------------------------------------------
*/

// 動画関連
Route::middleware(['auth'])->group(function () {
    Route::get('/videos', [VideoController::class, 'index'])->name('videos.index');
    Route::get('/explore/videos', [ExploreVideoController::class, 'index'])->name('exploreVideos.index');
    Route::get('/quota-exceeded', function () {
        return Inertia::render('Errors/QuotaExceeded');
    })->name('quota.exceeded');
    Route::get('/test-quota-exceeded', function () {
        return redirect()->route('quota.exceeded')->with('message', 'これはテストメッセージです。');
    });
});

// note関連
Route::middleware(['auth'])->group(function () {
    Route::get('/notes/index', [NoteController::class, 'index'])->name('notes.index');
    Route::get('/api/notes', [NoteController::class, 'apiIndex']);
    Route::patch('/notes/{note}', [NoteController::class, 'update'])->name('notes.update');

    // 確認済み
    Route::post('/notes', [NoteController::class, 'store'])->name('notes.store');
    Route::get('/notes/{note}', [NoteController::class, 'show'])->name('notes.show');
    Route::delete('/notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');
});


// moment関連
Route::middleware(['auth'])->group(function () {
    Route::post('/moments', [MomentController::class, 'store'])->name('moments.store');
    Route::put('/moments/{moment}', [MomentController::class, 'update'])->name('moments.update');
    Route::delete('/moments/{moment}', [MomentController::class, 'destroy'])->name('moments.destroy');
    // 確認済み
    Route::get('/moments', [MomentController::class, 'index'])->name('moments.index');
});

// tag関連
Route::middleware(['auth'])->group(function () {
    Route::get('/tags', [TagController::class, 'index'])->name('tags.index');
});

// note tag 関連
Route::get('/api/tags/search', [TagController::class, 'search'])->name('api.tags.search');



//使ってる
Route::patch('/tags/{tag}', [TagController::class, 'update'])->name('tags.update');
Route::post('/notes/{note}/tags', [NoteTagController::class, 'store'])->name('notes.tags.store');
Route::post('/tags', [TagController::class, 'store'])->name('tags.store');
Route::delete('/notes/{note}/tags/{tag}', [NoteTagController::class, 'destroy'])->name('notes.tags.destroy');
Route::delete('/tags/{tag}', [TagController::class, 'destroy'])->name('tags.destroy');
