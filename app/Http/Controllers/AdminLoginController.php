<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.login'); // สร้าง view นี้ด้านล่าง
    }

    public function login(Request $request)
{
    $request->validate([
        'username' => 'required',
        'password' => 'required',
    ]);

    $admin = Admin::where('username', $request->username)->first();

    if ($admin && Hash::check($request->password, $admin->password)) {
        // Store the admin's ID and full name in the session
        session(['admin_id' => $admin->admin_id, 'admin_fullname' => $admin->fullname]); 
        return redirect()->route('welcome');
    }
    return back()->withErrors(['login' => 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง']);
}


    public function logout(Request $request)
{
    session()->forget('admin_id');
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
}

}



