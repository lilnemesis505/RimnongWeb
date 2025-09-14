<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ProductController;

Route::post('/register', [CustomerController::class, 'register']);
Route::get('/customer', [CustomerController::class, 'getCustomer']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/promotions/check', [PromotionController::class, 'check']);

// order
Route::post('/orders', [OrderController::class, 'store']); // สำหรับสร้างออเดอร์ใหม่
Route::get('/orders/pending', [OrderController::class, 'getPendingOrders']); // สำหรับดึงออเดอร์ที่รอ
Route::post('/orders/update-status', [OrderController::class, 'updateStatus']);
// customer
Route::get('/customers/{id}', [CustomerController::class, 'showApi']);
Route::get('/customers/{cusId}/history', [OrderController::class, 'getCustomerHistory']);

// employee
Route::get('/employees/{id}', [EmployeeController::class, 'showApi']);
Route::get('/employees/{emId}/history', [EmployeeController::class, 'getOrderHistory']);
Route::get('/products', [ProductController::class, 'indexApi']);
