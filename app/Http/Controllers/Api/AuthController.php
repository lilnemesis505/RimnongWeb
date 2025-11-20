<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer; // ✅ Import Model Customer
use App\Models\Employee; // ✅ Import Model Employee
use Illuminate\Support\Facades\Hash; // ✅ Import Hash Facade

class AuthController extends Controller
{
    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // 1. ตรวจสอบข้อมูลที่ส่งมา (Validation)
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('username', 'password');

        // 2. ลองค้นหาจากตาราง Customer ก่อน
        $customer = Customer::where('username', $credentials['username'])->first();

        if ($customer && Hash::check($credentials['password'], $customer->password)) {
            // ✅ ถ้าเป็น Customer และรหัสผ่านถูกต้อง
            // *** หมายเหตุ: ส่วนนี้ควรจะสร้าง Token ตอบกลับไปด้วย (ดูใน "ข้อเสนอแนะเพิ่มเติม")
            return response()->json([
                "status" => "success",
                "role" => "customer",
                "id" => $customer->cus_id
                // "token" => $customer->createToken('auth_token')->plainTextToken // ตัวอย่างการใช้ Sanctum
            ]);
        }

        // 3. ถ้าไม่ใช่ Customer ให้ลองค้นหาจากตาราง Employee
        $employee = Employee::where('username', $credentials['username'])->first();

        if ($employee && Hash::check($credentials['password'], $employee->password)) {
            // ✅ ถ้าเป็น Employee และรหัสผ่านถูกต้อง
            return response()->json([
                "status" => "success",
                "role" => "employee",
                "id" => $employee->em_id
                // "token" => $employee->createToken('auth_token')->plainTextToken // ตัวอย่างการใช้ Sanctum
            ]);
        }

        // 4. ถ้าไม่เจอใครเลย หรือรหัสผ่านผิด
        return response()->json([
            "status" => "error",
            "message" => "Username หรือ Password ของผู้ใช้ไม่ถูกต้อง"
        ], 401); // 401 Unauthorized
    }
}