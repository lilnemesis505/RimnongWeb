<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    // สำหรับหน้าเว็บ: แสดงรายชื่อลูกค้า
    public function index()
    {
        $customers = Customer::paginate(50);
        return view('layouts.customer', compact('customers'));
    }

    // สำหรับ Flutter: สมัครสมาชิกผ่าน API
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fullname' => 'required|string|max:255',
            'username' => 'required|string|unique:customers,username',
            'password' => 'required|string|min:6',
            'email' => 'required|email|unique:customers,email',
            'cus_tel' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ]);
        }

        $customer = new Customer();
        $customer->fullname = $request->fullname;
        $customer->username = $request->username;
        $customer->password = Hash::make($request->password);
        $customer->email = $request->email;
        $customer->cus_tel = $request->cus_tel;
        $customer->save();

        return response()->json([
            'status' => 'success',
            'message' => 'สมัครสมาชิกสำเร็จ'
        ]);
    }
    public function checkUsername(Request $request)
{
    $exists = Customer::where('username', $request->username)->exists();

    if ($exists) {
        return response()->json([
            'status' => 'error',
            'message' => 'Username นี้มีผู้ใช้งานแล้ว'
        ]);
    }
}

}
