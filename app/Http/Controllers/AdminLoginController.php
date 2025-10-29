<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session; 
use Illuminate\Support\Facades\Auth; 

class AdminLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.login'); 
    }

    // ----------------------------------------------------------------
    // [แก้ไข] ฟังก์ชัน login
    // ----------------------------------------------------------------
    public function login(Request $request)
    {
        // 1. [แก้ไข] อัปเดต Validation rule
        $request->validate([
            'login_identity' => 'required|string', // เปลี่ยนจาก 'username'
            'password'       => 'required',
        ], [
            // [เพิ่ม] ข้อความแจ้งเตือนสำหรับ field ใหม่
            'login_identity.required' => 'กรุณากรอกชื่อผู้ใช้ หรือ อีเมล',
            'password.required'       => 'กรุณากรอกรหัสผ่าน',
        ]);
        
        // 2. [แก้ไข] ตรรกะการค้นหา (Username หรือ Email)
        $loginIdentity = $request->input('login_identity');
        
        // ตรวจสอบว่าค่าที่กรอกมาเป็น Email หรือ Username
        $field = filter_var($loginIdentity, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // ค้นหา Admin ด้วย field ที่ถูกต้อง
        $admin = Admin::where($field, $loginIdentity)->first();

        // 3. [คงเดิม] ตรวจสอบรหัสผ่าน
        if ($admin && Hash::check($request->password, $admin->password)) {

            session(['admin_id' => $admin->admin_id, 'admin_fullname' => $admin->fullname]); 
            return redirect()->route('welcome');
        }
        
        // 4. [แก้ไข] ส่ง Error กลับไป พร้อมข้อมูลที่กรอกไว้ (ยกเว้นรหัสผ่าน)
        return back()->withErrors(['login' => 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง'])
                     ->withInput($request->only('login_identity')); 
    }


    public function logout(Request $request)
    {
        session()->forget('admin_id');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function showRegistrationForm()
    {
        return view('admin.register');
    }

 
    public function register(Request $request)
    {
        // ... (ส่วนของ register เหมือนเดิม ไม่มีการเปลี่ยนแปลง) ...
        // 1. ตรวจสอบข้อมูล
        $rules = [
            'fullname'  => 'required|string|max:255',
            'username'  => 'required|string|max:255|unique:admin,username',
            'email'     => 'required|string|email|max:255|unique:admin,email',
            'admin_tel' => 'required|string|digits:10|unique:admin,admin_tel',
            'password'  => 'required|string|min:8|confirmed', 
        ];

        $messages = [
            'fullname.required' => 'กรุณากรอกชื่อ-สกุล',
            'username.required' => 'กรุณากรอก Username',
            'username.unique'   => 'Username นี้มีผู้ใช้งานแล้ว',
            'email.required'    => 'กรุณากรอกอีเมล',
            'email.email'       => 'รูปแบบอีเมลไม่ถูกต้อง',
            'email.unique'      => 'อีเมลนี้มีผู้ใช้งานแล้ว',
            'admin_tel.required' => 'กรุณากรอกเบอร์โทร',
            'admin_tel.digits'  => 'เบอร์โทรต้องมี 10 หลัก',
            'admin_tel.unique'  => 'เบอร์โทรนี้มีผู้ใช้งานแล้ว',
            'password.required' => 'กรุณากรอกรหัสผ่าน',
            'password.min'      => 'รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร',
            'password.confirmed' => 'การยืนยันรหัสผ่านไม่ตรงกัน',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->route('admin.register.form')
                        ->withErrors($validator)
                        ->withInput(); 
        }

        // 2. สร้าง Admin
        try {
            Admin::create([
                'fullname'  => $request->fullname,
                'username'  => $request->username,
                'email'     => $request->email,
                'admin_tel' => $request->admin_tel,
                'password'  => Hash::make($request->password), 
            ]);
        } catch (\Exception $e) {
            return redirect()->route('admin.register.form')
                        ->withErrors(['register' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล'])
                        ->withInput();
        }

        // 3. กลับไปหน้า Login พร้อมข้อความสำเร็จ
        return redirect()->route('login')->with('success', 'สมัครสมาชิกสำเร็จ กรุณาเข้าสู่ระบบ');
    }
}