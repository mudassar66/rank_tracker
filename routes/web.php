<?php

use Illuminate\Support\Facades\Route;

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


Route::group(['middleware' => ['web','auth']], function () {
    Route::get('/', function () {
        return view('dashboard');
    });
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::post('/analyze', [\App\Http\Controllers\UserSearchController::class, 'analyze'])->name('analyze');
    Route::post('/task-post', [\App\Http\Controllers\UserController::class, 'taskPost'])->name('task_post');
    Route::get('/search-results/{id}', [\App\Http\Controllers\UserSearchController::class, 'index'])->name('search_results');
});

//Route::post('/postbackscript', [\App\Http\Controllers\UserController::class, 'taskPostBackScript'])->name('post');

// Route::post('/postbackscript', function(){
//     dd('received');
//     })->name('post');


require __DIR__.'/auth.php';
