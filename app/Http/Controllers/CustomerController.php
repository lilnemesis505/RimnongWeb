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
        // 1. ตรวจสอบความถูกต้องของข้อมูล (Validation)
        $validator = Validator::make($request->all(), [
            'fullname' => 'required|string|max:255',
            'username' => 'required|string|unique:customer,username',
            'password' => 'required|string|min:6',
            'email'    => 'required|email|unique:customer,email',
            'cus_tel'  => 'required|string|max:20',
        ]);

        // 2. ถ้าข้อมูลไม่ถูกต้อง ให้ส่ง error กลับไป
        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first() // ส่งข้อความ error แรกที่เจอ
            ], 422); // 422 Unprocessable Entity
        }

        // 3. ถ้าข้อมูลถูกต้อง สร้าง Customer ใหม่
        $customer = new Customer();
        $customer->fullname = $request->fullname;
        $customer->username = $request->username;
        $customer->password = Hash::make($request->password); // ✅ เข้ารหัสผ่านเสมอ
        $customer->email    = $request->email;
        $customer->cus_tel  = $request->cus_tel;
        $customer->save();

        // 4. ส่งข้อความสำเร็จกลับไป
        return response()->json([
            'status'  => 'success',
            'message' => 'สมัครสมาชิกสำเร็จ'
        ], 201); // 201 Created
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
    public function getCustomer(Request $request)
{
    $cusId = $request->query('cus_id'); // รับจาก query string เช่น ?cus_id=1

    if (!$cusId) {
        return response()->json([
            'status' => 'error',
            'message' => 'Customer ID is missing'
        ]);
    }

    $customer = \App\Models\Customer::select('fullname', 'email')->find($cusId);

    if ($customer) {
        return response()->json([
            'status' => 'success',
            'fullname' => $customer->fullname,
            'email' => $customer->email
        ]);
    } else {
        return response()->json([
            'status' => 'error',
            'message' => 'Customer not found'
        ]);
    }
}
public function showApi($id)
    {
        $customer = Customer::select('fullname', 'email')->findOrFail($id);
        
        return response()->json([
            'status' => 'success',
            'fullname' => $customer->fullname,
            'email' => $customer->email
        ]);
    }
}
