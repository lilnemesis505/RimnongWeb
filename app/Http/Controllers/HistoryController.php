<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;


class HistoryController extends Controller
{
    public function index()
    {
        $orders = Order::orderBy('order_date', 'desc')->paginate(10);
        
        return view('layouts.history.history', compact('orders'));
    }
}