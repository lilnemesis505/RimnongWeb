<?php

namespace App\Http\Controllers; // (หมายเหตุ: ถ้าไฟล์นี้อยู่ใน App\Http\Controllers\Api\ ให้แก้ namespace ตรงนี้)

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\Customer;
use App\Models\Employee;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminPasswordResetMail;

class ApiPasswordResetController extends Controller
{
    /**
     * [ลบ] 1. ลบ getModel() ออก
     */

    /**
     * 1. ขอ OTP (ส่งอีเมล)
     */
    public function sendOtpEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            // [ลบ] 2. ลบ user_type ออก
        ]);

        // [แก้ไข] 3. ค้นหาทั้ง 2 ตาราง (โดยใช้ชื่อคอลัมน์ที่ถูกต้อง)
        $customerExists = Customer::where('email', $request->email)->exists();
        $employeeExists = Employee::where('em_email', $request->email)->exists(); // 👈 (ใช้ em_email)

        if (!$customerExists && !$employeeExists) {
            return response()->json(['status' => 'error', 'message' => 'ไม่พบอีเมลนี้ในระบบ'], 404);
        }

        // สร้าง OTP (Logic เหมือนเดิม)
        $otp = random_int(100000, 999999);
        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($otp),
                'created_at' => Carbon::now()
            ]
        );

        // ส่งอีเมล (Logic เหมือนเดิม)
        try {
             Mail::to($request->email)->send(new AdminPasswordResetMail($otp));
        } catch (\Exception $e) {
             return response()->json(['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการส่งอีเมล'], 500);
        }
       
        return response()->json([
            'status' => 'success',
            'message' => 'OTP ได้ถูกส่งไปยัง ' . $request->email
        ]);
    }

    /**
     * 2. ตรวจสอบ OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|numeric|digits:6',
            // [ลบ] 4. ลบ user_type ออก
        ]);

        $record = DB::table('password_resets')->where('email', $request->email)->first();

        // (Logic ตรวจสอบ Token และ วันหมดอายุ ... เหมือนเดิม)
        if (!$record || !Hash::check($request->otp, $record->token)) {
            return response()->json(['status' => 'error', 'message' => 'OTP ไม่ถูกต้อง'], 422);
        }
        $expiresAt = Carbon::parse($record->created_at)->addMinutes(10);
        if (Carbon::now()->isAfter($expiresAt)) {
            DB::table('password_resets')->where('email', $request->email)->delete();
            return response()->json(['status' => 'error', 'message' => 'OTP หมดอายุแล้ว กรุณาขอใหม่'], 422);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'OTP ถูกต้อง'
        ]);
    }

    /**
     * 3. รีเซ็ตรหัสผ่าน
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|numeric|digits:6',
            'password' => 'required|string|min:8|confirmed',
            // [ลบ] 5. ลบ user_type ออก
        ]);

        // --- 1. ตรวจสอบ OTP อีกครั้ง (เหมือนเดิม) ---
        $record = DB::table('password_resets')->where('email', $request->email)->first();
        if (!$record || !Hash::check($request->otp, $record->token)) {
            return response()->json(['status' => 'error', 'message' => 'OTP ไม่ถูกต้อง'], 422);
        }
        $expiresAt = Carbon::parse($record->created_at)->addMinutes(10);
        if (Carbon::now()->isAfter($expiresAt)) {
            DB::table('password_resets')->where('email', $request->email)->delete();
            return response()->json(['status' => 'error', 'message' => 'OTP หมดอายุแล้ว กรุณาขอใหม่'], 422);
        }

        // --- [แก้ไข] 6. อัปเดตรหัสผ่าน (ค้นหาและอัปเดตทั้ง 2 ตาราง) ---
        $passwordUpdated = false;

        $customer = Customer::where('email', $request->email)->first();
        if ($customer) {
            $customer->password = Hash::make($request->password);
            $customer->save();
            $passwordUpdated = true;
        }

        $employee = Employee::where('em_email', $request->email)->first(); // 👈 (ใช้ em_email)
        if ($employee) {
            $employee->password = Hash::make($request->password);
            $employee->save();
            $passwordUpdated = true;
        }

        // 3. ลบ Token ที่ใช้แล้ว
        DB::table('password_resets')->where('email', $request->email)->delete();

        if ($passwordUpdated) {
            return response()->json([
                'status' => 'success',
                'message' => 'เปลี่ยนรหัสผ่านสำเร็จแล้ว'
            ]);
        }
        
        return response()->json(['status' => 'error', 'message' => 'ไม่พบผู้ใช้นี้ในระบบ'], 404);
    }
}