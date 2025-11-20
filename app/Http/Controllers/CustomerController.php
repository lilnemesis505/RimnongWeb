<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer; //
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Order; //

class CustomerController extends Controller
{
    // สำหรับหน้าเว็บ: แสดงรายชื่อลูกค้า
   public function index()
    {
        $customers = Customer::withSum('receipts', 'price_total')->paginate(50); 

        return view('layouts.customer', compact('customers')); 
    }

    // สำหรับ Flutter: สมัครสมาชิกผ่าน API
  public function register(Request $request)
    {
        // [แก้ไข] 1. อัปเดต Validation Rules
        $validator = Validator::make($request->all(), [
            'fullname' => 'required|string|max:255',
            'username' => 'required|string|unique:customer,username',
            'password' => 'required|string|min:8', // 👈 (แก้จาก 6 เป็น 8)
            'email'    => 'required|email|unique:customer,email',
            'cus_tel'  => 'required|string|digits:10', // 👈 (แก้จาก max:20 เป็น digits:10)
        ], [
            // [เพิ่ม] 2. เพิ่มข้อความแจ้งเตือน (สำหรับส่งกลับไปหา Flutter)
            'fullname.required' => 'กรุณากรอกชื่อ-สกุล',
            'username.required' => 'กรุณากรอกชื่อผู้ใช้',
            'username.unique'   => 'ชื่อผู้ใช้นี้มีคนใช้แล้ว',
            'password.required' => 'กรุณากรอกรหัสผ่าน',
            'password.min'      => 'รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร',
            'email.required'    => 'กรุณากรอกอีเมล',
            'email.email'       => 'รูปแบบอีเมลไม่ถูกต้อง',
            'email.unique'      => 'อีเมลนี้มีคนใช้แล้ว',
            'cus_tel.required'  => 'กรุณากรอกเบอร์โทร',
            'cus_tel.digits'    => 'เบอร์โทรต้องมี 10 หลักเท่านั้น',
        ]); 

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first() 
            ], 422); 
        } 

        // 3. (สร้าง Customer ... เหมือนเดิม)
        $customer = new Customer(); 
        $customer->fullname = $request->fullname; 
        $customer->username = $request->username; 
        $customer->password = Hash::make($request->password); 
        $customer->email    = $request->email; 
        $customer->cus_tel  = $request->cus_tel; 
        $customer->save(); 

        return response()->json([
            'status'  => 'success',
            'message' => 'สมัครสมาชิกสำเร็จ'
        ], 201);
    }

    public function checkUsername(Request $request)
    {
        $exists = Customer::where('username', $request->username)->exists(); //

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

        $customer = \App\Models\Customer::select('fullname', 'email')->find($cusId); //

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

    public function checkAvailability(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'field' => 'required|string|in:username,email',
            'value' => 'required|string',
        ]); //

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $field = $request->field; //
        $value = $request->value; //

        $exists = Customer::where($field, $value)->exists(); //

        return response()->json([
            'available' => !$exists 
        ]);
    }
    
    public function showApi($id)
    {
        $customer = Customer::select('fullname', 'email')->findOrFail($id); //
        
        return response()->json([
            'status' => 'success',
            'fullname' => $customer->fullname,
            'email' => $customer->email
        ]);
    }
}