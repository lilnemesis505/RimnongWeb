<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class HistoryController extends Controller
{
   public function index()
{
    $orders = Order::with('details.product') 
                   ->orderBy('order_date', 'desc')
                   ->paginate(50);
        
        return view('layouts.history.history', compact('orders'));
    }
}