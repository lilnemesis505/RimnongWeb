<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;


use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProtypeController;
use App\Http\Controllers\StockMatController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\AdminLoginController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\HomeController; 

Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AdminLoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');



//admin loginก่อน
Route::middleware('admin.auth')->group(function () {
   Route::get('/', [HomeController::class, 'index'])->name('welcome');
    // product
    Route::get('/products', [ProductController::class, 'index'])->name('product.index');
    Route::get('/products/add', [ProductController::class, 'create'])->name('product.add');
    Route::post('/products', [ProductController::class, 'store'])->name('product.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('product.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('product.update');
    Route::get('/products/filter', [ProductController::class, 'filter'])->name('product.filter');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('product.destroy');

    // protype
    Route::get('/protype/add', [ProtypeController::class, 'create'])->name('protype.add');
    Route::post('/protype', [ProtypeController::class, 'store'])->name('protype.store');
    Route::delete('/protype/{id}', [ProtypeController::class, 'destroy'])->name('protype.delete');

    // employee
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employee.index');
    Route::get('/employees/add', [EmployeeController::class, 'create'])->name('employee.add');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employee.store');
    Route::get('/employees/{id}/edit', [EmployeeController::class, 'edit'])->name('employee.edit');
    Route::put('/employees/{id}', [EmployeeController::class, 'update'])->name('employee.update');
    Route::delete('/employees/{id}', [EmployeeController::class, 'destroy'])->name('employee.destroy');

    // customer
    Route::get('/customers', [CustomerController::class, 'index'])->name('customer.index');

    // stock
    Route::get('/stocks', [StockMatController::class, 'index'])->name('stock.index');
    Route::get('/stocks/add', [StockMatController::class, 'create'])->name('stock.add');
    Route::post('/stocks', [StockMatController::class, 'store'])->name('stock.store');
    Route::get('/stocks/{id}/edit', [StockMatController::class, 'edit'])->name('stock.edit');
    Route::put('/stocks/{id}', [StockMatController::class, 'update'])->name('stock.update');
    Route::delete('/stocks/{id}', [StockMatController::class, 'destroy'])->name('stock.destroy');

    // promotion
    Route::get('/promotions', [PromotionController::class, 'index'])->name('promotion.index');
    Route::post('/promotions', [PromotionController::class, 'store'])->name('promotion.store');
    Route::get('/promotions/add', [PromotionController::class, 'create'])->name('promotion.add');
    Route::delete('/promotions/{id}', [PromotionController::class, 'destroy'])->name('promotion.delete');

    // history
    Route::delete('/order/{id}', [OrderController::class, 'destroy'])->name('order.destroy');
    Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
    Route::get('/order/{id}/receipt', [OrderController::class, 'generateReceipt'])->name('order.receipt');
    Route::get('/order/{id}', [OrderController::class, 'show'])->name('order.details');
    
    

    // reports
 Route::get('/salereport', [ReportController::class, 'saleReport'])->name('salereport.index');

    Route::get('/expreport', function () {
        return view('layouts.expreport');
    })->name('expreport.index');

    // dashboard


});