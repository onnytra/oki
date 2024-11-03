<?php

use App\Livewire\Tryout;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('filament.user.pages.dashboard');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/tryout/{id}', Tryout::class)->name('tryout.start');
});
