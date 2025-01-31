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

Route::get('/about', function () {
    return "Hi, about!";
});

Route::get('/contuct', function () {
    return "Hi, countuc!";
});

// パラメータを渡す
Route::get('/podcast/{id}/{name}', function($id, $name){
    return "This is podcast number " . $id . "and name: ". $name;
});

Route::get('/admin/podcast/example', array('as' => 'admin.home', function(){
    $url = route('admin.home');
    return "this url is ". $url;
}));

// PostControllerのindex関数という意味
// Route::get('/post/{id}', [PostsController::class, 'index']);

// resource関数でプリセットされたルートを使える
// php artisan route:listで確認する
// Route::resource('posts', PostsController::class);

Route::get('/contact', [PostsController::class, 'contact']);

Route::get('post/{id}/{name}/{password}', [PostsController::class, 'show_post']);

/*
|--------------------------------------------------------------------------
| Eloquent Routes
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

Route::get('/youtube/search', [VideoService::class, 'searchVideos']);
Route::get('/videos', [VideoController::class, 'index'])->name('videos.index');
