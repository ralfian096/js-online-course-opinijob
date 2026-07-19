<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
  if (file_exists(public_path('index.html'))) {
    return redirect('/index.html');
  }

  return view('welcome');
});

Route::redirect('/login', '/login/index.html');
Route::redirect('/register', '/register/index.html');
Route::redirect('/admin/setting/product', '/admin/setting/product/index.html');
Route::redirect('/admin/setting/product-category', '/admin/setting/product-category/index.html');
Route::redirect('/admin/setting/product-price', '/admin/setting/product-price/index.html');

Route::view('/docs/api', 'docs.api');
