<?php

namespace App\Http\Controllers;

use App\Http\Middleware\AuthAdmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('home.index');

Route::middleware(['auth'])->group(function () {
    Route::get('/account-dashboard', [UserController::class, 'index'])->name('user.index');
});

Route::middleware(['auth', AuthAdmin::class])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/admin/brands', [AdminController::class, 'brand'])->name('admin.brand');
    Route::get('/admin/brands/add', [AdminController::class, 'brand_add'])->name('admin.brand.add');
    Route::post('/admin/brands/store', [AdminController::class, 'brand_store'])->name('admin.brand.store');
    Route::get('/admin/brands/edit/{id}', [AdminController::class, 'brand_edit'])->name('admin.brand.edit');
    Route::post('/admin/brands/update', [AdminController::class, 'brand_update'])->name('admin.brand.update');
    Route::post('/admin/brands/delete/{id}', [AdminController::class, 'brand_delete'])->name('admin.brand.delete');

    Route::get('/admin/categories', [AdminController::class, 'category'])->name('admin.category');
    Route::get('/admin/categories/add', [AdminController::class, 'category_add'])->name('admin.category.add');
    Route::post('/admin/categories/store', [AdminController::class, 'category_store'])->name('admin.category.store');
    Route::get('/admin/categories/edit/{id}', [AdminController::class, 'category_edit'])->name('admin.category.edit');
    Route::post('/admin/categories/update', [AdminController::class, 'category_update'])->name('admin.category.update');
    Route::post('/admin/categories/delete/{id}', [AdminController::class, 'category_delete'])->name('admin.category.delete');
});
