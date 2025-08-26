<?php

namespace App\Http\Controllers;

use App\Http\Middleware\AuthAdmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('home.index');
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{product_slug}', [ShopController::class, 'product_details'])->name('shop.product.details');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add_to_cart'])->name('cart.add');
Route::post('/cart/increase/{rowId}', [CartController::class, 'increase_cart_quantity'])->name('cart.qty.increase');
Route::post('/cart/decrease/{rowId}', [CartController::class, 'decrease_cart_quantity'])->name('cart.qty.decrease');
Route::post('/cart/remove/{rowId}', [CartController::class, 'remove_item'])->name('cart.remove.item');
Route::post('/cart/clear', [CartController::class, 'clear_cart'])->name('cart.clear.items');

Route::post('/cart/coupon-apply', [CartController::class, 'coupon_apply'])->name('cart.coupon.apply');
Route::post('/cart/coupon-remove', [CartController::class, 'coupon_remove_code'])->name('cart.coupon.remove');

Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
Route::post('/wishlist/add', [WishlistController::class, 'add_to_wishliist'])->name('wishlist.add');
Route::post('/wishlist/item/remove/{rowId}', [WishlistController::class, 'remove_wishlist'])->name('wishlist.item.remove');
Route::post('/wishlist/clear', [WishlistController::class, 'clear_wishlist'])->name('wishlist.clear');
Route::post('/wishlist/move-to-cart/{rowId}', [WishlistController::class, 'move_to_cart'])->name('wishlist.move.to.cart');

Route::get('/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
Route::post('/place-an-order', [CartController::class, 'place_an_order'])->name('cart.place.order');
Route::get('/order-confirmation', [CartController::class, 'order_confirmation'])->name('cart.order.confirmation');

Route::middleware(['auth'])->group(function () {
    Route::get('/account-dashboard', [UserController::class, 'index'])->name('user.index');
    Route::get('/account-order', [UserController::class, 'order'])->name('user.order');
    Route::get('/account-order/{order_id}/detail', [UserController::class, 'order_detail'])->name('user.order.detail');
    Route::post('/account-order/cancel-order', [UserController::class, 'order_cancel'])->name('user.order.cancel');
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

    Route::get('/admin/products', [AdminController::class, 'product'])->name('admin.product');
    Route::get('/admin/products/add', [AdminController::class, 'product_add'])->name('admin.product.add');
    Route::post('/admin/products/store', [AdminController::class, 'product_store'])->name('admin.product.store');
    Route::get('/admin/products/edit/{id}', [AdminController::class, 'product_edit'])->name('admin.product.edit');
    Route::post('/admin/products/update', [AdminController::class, 'product_update'])->name('admin.product.update');
    Route::post('/admin/products/delete/{id}', [AdminController::class, 'product_delete'])->name('admin.product.delete');

    Route::get('/admin/coupons', [AdminController::class, 'coupons'])->name('admin.coupon');
    Route::get('/admin/coupons/add', [AdminController::class, 'coupon_add'])->name('admin.coupon.add');
    Route::post('/admin/coupons/store', [AdminController::class, 'coupon_store'])->name('admin.coupon.store');
    Route::get('/admin/coupons/edit/{id}', [AdminController::class, 'coupon_edit'])->name('admin.coupon.edit');
    Route::post('/admin/coupons/update', [AdminController::class, 'coupon_update'])->name('admin.coupon.update');
    Route::post('/admin/coupons/delete/{id}', [AdminController::class, 'coupon_delete'])->name('admin.coupon.delete');

    Route::get('/admin/orders', [AdminController::class, 'order'])->name('admin.orders');
    Route::get('/admin/order/details/{order_id}', [AdminController::class, 'order_detail'])->name('admin.order.detail');
    Route::post('/admin/order/update-status', [AdminController::class, 'update_order_status'])->name('admin.order.status.update');

    Route::get('/admin/slides', [AdminController::class, 'slides'])->name('admin.slide');
    Route::get('/admin/slides/add', [AdminController::class, 'slide_add'])->name('admin.slide.add');
    Route::post('/admin/slides/store', [AdminController::class, 'slide_store'])->name('admin.slide.store');
});
