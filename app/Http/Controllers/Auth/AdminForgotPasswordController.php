<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\Admin;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminPasswordResetMail; // (เราจะแก้ไข Mailable นี้ด้วย)

class AdminForgotPasswordController extends Controller
{
    // 1. แสดงฟอร์มให้กรอกอีเมล (เหมือนเดิม)
    public function showLinkRequestForm()
    {
        return view('admin.admin-forgot-password');
    }

    // 2. [OTP CHANGE] สร้าง OTP และส่งอีเมล
    public function sendOtpEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $admin = Admin::where('email', $request->email)->first();

        if (!$admin) {
            return back()->withErrors(['email' => 'ไม่พบอีเมลนี้ในระบบ']);
        }

        // สร้าง OTP 6 หลัก
        $otp = random_int(100000, 999999);

        // บันทึก "OTP ที่ Hash แล้ว" ลงในคอลัมน์ token
        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($otp), // *สำคัญ: ต้อง Hash OTP*
                'created_at' => Carbon::now()
            ]
        );

        // ส่งอีเมล (ส่ง OTP 6 หลัก (ตัวเลข) ไป)
        try {
             Mail::to($request->email)->send(new AdminPasswordResetMail($otp));
        } catch (\Exception $e) {
             return back()->withErrors(['email' => 'เกิดข้อผิดพลาดในการส่งอีเมล']);
        }
       
        // ส่งไปหน้า "ยืนยัน OTP" พร้อมจำอีเมลไว้ใน Session ชั่วคราว
        return redirect()->route('admin.otp.verify')->with('email', $request->email);
    }

    // 3. [OTP CHANGE] แสดงฟอร์มกรอก OTP
    public function showVerifyForm()
    {
        // ถ้าผู้ใช้เข้าหน้านี้โดยตรง (ไม่มี session 'email') ให้เด้งกลับ
        if (!session('email')) {
            return redirect()->route('admin.password.request');
        }
        return view('admin.admin-verify-otp', ['email' => session('email')]);
    }

    // 4. [OTP CHANGE] ตรวจสอบ OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|numeric|digits:6',
        ]);

        $record = DB::table('password_resets')->where('email', $request->email)->first();

        // 1. ตรวจสอบว่ามี record
        // 2. ตรวจสอบว่า OTP (ที่ Hash) ตรงกัน
        if (!$record || !Hash::check($request->otp, $record->token)) {
            return back()->withInput()->withErrors(['otp' => 'OTP ไม่ถูกต้อง']);
        }

        // 3. ตรวจสอบว่าหมดอายุหรือยัง (เช่น 10 นาที)
        $expiresAt = Carbon::parse($record->created_at)->addMinutes(10);
        if (Carbon::now()->isAfter($expiresAt)) {
            DB::table('password_resets')->where('email', $request->email)->delete();
            return back()->withInput()->withErrors(['otp' => 'OTP หมดอายุแล้ว กรุณาขอใหม่']);
        }

        // --- ถ้า OTP ถูกต้อง ---
        
        // 1. ลบ OTP ที่ใช้แล้วทิ้ง
        DB::table('password_resets')->where('email', $request->email)->delete();

        // 2. เก็บ "สถานะยืนยันแล้ว" ไว้ใน Session ถาวร
        session(['otp_verified_email' => $request->email]);
        
        // 3. ส่งไปหน้าตั้งรหัสผ่านใหม่
        return redirect()->route('admin.password.reset');
    }

    // 5. [OTP CHANGE] แสดงฟอร์มตั้งรหัสผ่านใหม่
    public function showResetForm()
    {
        // *สำคัญ: ตรวจสอบว่าผ่านการยืนยัน OTP มาหรือยัง*
        if (!session('otp_verified_email')) {
            return redirect()->route('admin.password.request')->withErrors('กรุณายืนยัน OTP ก่อน');
        }

        return view('admin.admin-reset-password', [
            'email' => session('otp_verified_email')
        ]);
    }

    // 6. [OTP CHANGE] อัปเดตรหัสผ่านใหม่
    public function resetPassword(Request $request)
    {
        // *สำคัญ: ตรวจสอบ Session อีกครั้ง*
        if (!session('otp_verified_email') || $request->email != session('otp_verified_email')) {
             return redirect()->route('admin.password.request')->withErrors('Session หมดอายุ กรุณาทำรายการใหม่');
        }

        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // ✅ [FIX] เปลี่ยน 'admin' เป็น 'email'
        $admin = Admin::where('email', $request->email)->first();
        
        if ($admin) {
            $admin->password = Hash::make($request->password);
            $admin->save();
        }

        // ล้าง Session ที่เก็บไว้
        session()->forget('otp_verified_email');

        return redirect()->route('login')->with('status', 'เปลี่ยนรหัสผ่านสำเร็จแล้ว กรุณาเข้าสู่ระบบใหม่');
    }
}