<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::get('/', [homeController::class, 'index'])->name('Home');
