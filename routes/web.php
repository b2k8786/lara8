<?php

use App\Http\Controllers\Wiki;
use App\Http\Controllers\Stream;
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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/live', [Stream::class, "sse"]);
Route::get('/dash', [Stream::class, "main"]);
Route::get('/wiki/{query}', [Wiki::class, "parseData"]);
