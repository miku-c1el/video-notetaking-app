<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\NoteController;
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
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});


Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';


// PostControllerのindex関数という意味
// Route::get('/post/{id}', [PostsController::class, 'index']);

// resource関数でプリセットされたルートを使える
// php artisan route:listで確認する
// Route::resource('posts', PostsController::class);

Route::get('/contact', [PostsController::class, 'contact']);

Route::get('post/{id}/{name}/{password}', [PostsController::class, 'show_post']);

/*
|--------------------------------------------------------------------------
| Eloquent Routes(練習)
|--------------------------------------------------------------------------
*/

Route::get('/find', function(){

    // all method
    $notes = Note::all();
    foreach($notes as $note){
        echo $note->title;
    }

    // find method
    $notes = Note::find(2);
    return $notes->title;
});

Route::get('/findwhere', function(){

    $note = Note::where('id', 2)->orderBy('id', 'desc')->take(1)->get();
    return $note;
});

Route::get('/findmore', function(){

    // findOrFail method
    $note = Note::findOrfail(1);
    return $note;

    // 条件式
    $note = Note::where('users_count', '<', 50)->firstOrFail();
});

Route::get('/insert', function(){
    //新しいデータ挿入するとき
    $note = new Note;
    // データを更新するとき
    $note = Note::find(2);
    $note->title = 'title';
    $note->content = 'hello hello';
    $note->timestamp = 0;
    $note->save();
});

Route::get('/insertUser', function(){
    User::create(['name' => 'David','email'=>'amdskal@gmail.com','password'=>'ahsjdkfl;alasdf']);
});



/*
|--------------------------------------------------------------------------
| Youtube Routes
|--------------------------------------------------------------------------
*/

Route::get('/videos', [VideoController::class, 'index'])->name('videos.index');


// routes/web.php
// Route::post('/notes', [NoteController::class, 'store'])->name('notes.store');
// Route::get('/notes/{note}', [NoteController::class, 'show'])->name('notes.show');

Route::middleware(['auth'])->group(function () {
    Route::post('/notes', [NoteController::class, 'store'])->name('notes.store');
    Route::get('/notes/{noteId}', [NoteController::class, 'create'])->name('notes.create');
});

/*
|--------------------------------------------------------------------------
| eloquent練習
|--------------------------------------------------------------------------
*/
Route::get('/notes', function(){
    $user = User::find(1);
    foreach($user->notes as $note){
        echo $note->title . '<br>';
    }
});

Route::get('/tags', function(){
    $tags = Note::find(1)->tags()->orderBy('id', 'desc')->get();
    return $tags;
});

// searchクエリお試し用
Route::get('/youtube/search', [VideoService::class, 'searchVideos']);
