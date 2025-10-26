<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Order;

class EmployeeController extends Controller
{
    // แสดงรายการพนักงาน
    public function index()
    {
        $employees = Employee::paginate(50);
        return view('layouts.employee.employee', compact('employees'));
    }

    // แสดงหน้าเพิ่มพนักงาน
    public function create()
    {
        return view('layouts.employee.add');
    }

    // บันทึกพนักงานใหม่
// In EmployeeController.php

public function store(Request $request)
{
    // 1. Define your validation rules
    $rules = [
        'em_name'  => 'required|string|max:60',
        'username' => 'required|string|max:35|unique:employee,username',
        // ✅ [FIX] เปลี่ยน min:6 เป็น min:8 และเพิ่ม 'confirmed'
        'password' => 'required|string|min:8|confirmed',
        'em_tel'   => 'required|string|max:10',
        'em_email' => 'required|email|unique:employee,em_email',
    ];

    // 2. Define your custom Thai messages
    $messages = [
        'em_name.required' => 'กรุณากรอกชื่อ-สกุล',
        'username.required' => 'กรุณากรอก Username',
        'username.unique'   => 'Username นี้มีผู้ใช้งานแล้ว',
        'password.required' => 'กรุณากรอกรหัสผ่าน',
        'password.min'      => 'รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร',
        'password.confirmed' => 'การยืนยันรหัสผ่านไม่ตรงกัน',
        'em_tel.required'   => 'กรุณากรอกเบอร์โทร',
        'em_tel.digits:10'        => 'เบอร์โทรต้องมี 10 หลัก',
        'em_email.required' => 'กรุณากรอกอีเมล',
        'em_email.email'    => 'รูปแบบอีเมลไม่ถูกต้อง',
        'em_email.unique'   => 'อีเมลนี้มีผู้ใช้งานแล้ว',
    ];

    // --- Part 1: Handling the confirmation from the modal ---
    if ($request->input('confirm_creation') === 'true') {
        
        // ลบกฎการ validate รหัสผ่านออก (เหมือนเดิม)
        unset($rules['password']);

        // Validate ข้อมูลอื่นๆ ที่เหลือ (เหมือนเดิม)
        $validated = $request->validate($rules, $messages);

        if (!session('temp_hashed_password')) {
            return redirect()->back()
                ->withInput($request->except('password'))
                ->withErrors(['password' => 'Session หมดอายุ กรุณากรอกรหัสผ่านใหม่อีกครั้ง']);
        }

        $validated['password'] = session('temp_hashed_password');
        Employee::create($validated);
        session()->forget('temp_hashed_password');

        return redirect()->route('employee.index')->with('success', 'เพิ่มข้อมูลพนักงาน (ที่ชื่อซ้ำ) เรียบร้อยแล้ว');
    }

    // --- Part 2: Handling the initial form submission ---

    // Validate ข้อมูล "ทั้งหมด" ตั้งแต่ครั้งแรกเลย (เหมือนเดิม)
    // (Validation ใหม่ min:8|confirmed จะทำงานที่นี่)
    $validated = $request->validate($rules, $messages);
    
    // ตรวจสอบชื่อซ้ำ (เหมือนเดิม)
    $existingEmployee = Employee::where('em_name', $request->em_name)->first();
    
    if ($existingEmployee) {
        $hashedPassword = bcrypt($validated['password']);
        session()->put('temp_hashed_password', $hashedPassword);

        return redirect()->back()
            ->withInput($request->except('password'))
            ->with('confirm_duplicate_name', $existingEmployee->em_name);
    }

    // 2.3 ถ้าไม่ซ้ำ (ผ่านทั้งหมด)
    $validated['password'] = bcrypt($validated['password']);
    Employee::create($validated);

    return redirect()->route('employee.index')->with('success', 'เพิ่มข้อมูลพนักงานเรียบร้อยแล้ว');
}
    // แสดงหน้าแก้ไขพนักงาน
    public function edit($id)
{
    $employee = Employee::findOrFail($id);
    return view('layouts.employee.edit', compact('employee'));
}

    // อัพเดทพนักงาน
 public function update(Request $request, $id)
{
    // 1. กำหนดกฎการตรวจสอบข้อมูล (Validation Rules)
    //    (เราจะจัดการ em_name.unique แยกต่างหาก)
    $rules = [
        'em_name'  => 'required|string|max:60', // กฎ unique จะถูกเพิ่ม/ลบ แบบ dynamic
        'username' => 'required|string|max:35|unique:employee,username,' . $id . ',em_id',
        'em_tel'   => 'required|string|max:10',
        'em_email' => 'required|email|unique:employee,em_email,' . $id . ',em_id',
    ];

    // 2. กำหนดข้อความแจ้งเตือน (Error Messages) เป็นภาษาไทย
    $messages = [
        'em_name.required' => 'กรุณากรอกชื่อ-สกุล',
        'em_name.unique'   => 'ชื่อ-สกุลนี้มีในระบบแล้ว', // <-- ข้อความสำหรับชื่อซ้ำ
        'username.required' => 'กรุณากรอก Username',
        'username.unique'   => 'Username นี้มีผู้ใช้งานแล้ว',
        'em_tel.required'   => 'กรุณากรอกเบอร์โทร',
        'em_email.required' => 'กรุณากรอกอีเมล',
        'em_email.email'    => 'รูปแบบอีเมลไม่ถูกต้อง',
        'em_email.unique'   => 'อีเมลนี้มีผู้ใช้งานแล้ว',
    ];

    // --- Part 1: Handling the confirmation from the modal ---
    // (ใช้ 'confirm_update' เป็น flag ใหม่)
    if ($request->input('confirm_update') === 'true') {
        
        // เรายอมรับชื่อซ้ำ กฎ em_name จึงไม่ต้องมี unique
        // แต่กฎของ username/email ยังต้อง unique
        
        // Validate ข้อมูล (โดยไม่มี em_name.unique)
        $validated = $request->validate($rules, $messages);

        // ค้นหาและอัปเดต
        $employee = Employee::findOrFail($id);
        $employee->update($validated);

        return redirect()->route('employee.index')->with('success', 'แก้ไขข้อมูล (ที่ชื่อซ้ำ) สำเร็จ');
    }

    // --- Part 2: Handling the initial form submission ---

    // 2.1 ตรวจสอบชื่อซ้ำ (กับคนอื่น) ด้วยตัวเองก่อน
    $existingEmployee = Employee::where('em_name', $request->em_name)
                                ->where('em_id', '!=', $id) // ต้องไม่ใช่ ID ของตัวเอง
                                ->first();

    if ($existingEmployee) {
        // 2.2 ถ้าเจอซ้ำ: ให้ส่งกลับไปถาม (เหมือนตอน Add)
        // (ใช้ 'confirm_duplicate_name_edit' เป็น session key ใหม่)
        return redirect()->back()
            ->withInput() // ส่งข้อมูลเก่ากลับไป
            ->with('confirm_duplicate_name_edit', $existingEmployee->em_name);
    }

    // 2.3 ถ้าไม่ซ้ำ: ให้ทำการ Validate แบบ "เข้มงวด" (เพิ่มกฎ unique ให้ em_name)
    $rules['em_name'] = 'required|string|max:60|unique:employee,em_name,' . $id . ',em_id';
    
    $validated = $request->validate($rules, $messages);

    // ค้นหาและอัปเดตตามปกติ
    $employee = Employee::findOrFail($id);
    $employee->update($validated);

    return redirect()->route('employee.index')->with('success', 'แก้ไขข้อมูลสำเร็จ');
}
 public function showApi($id)
    {
        // ใช้ findOrFail เพื่อค้นหาพนักงานจาก em_id
        // ถ้าไม่เจอ จะส่ง 404 Not Found กลับไปโดยอัตโนมัติ
        $employee = Employee::select('em_name', 'em_email')->findOrFail($id);
        
        // Laravel จะแปลงข้อมูลเป็น JSON ให้โดยอัตโนมัติ
        return response()->json([
            'status' => 'success',
            'em_name' => $employee->em_name,
            'em_email' => $employee->em_email
        ]);
    }
        public function getOrderHistory($emId)
    {
        // 1. ตรวจสอบก่อนว่ามีพนักงาน ID นี้จริงหรือไม่
        Employee::findOrFail($emId);

        // 2. ใช้ Eager Loading ดึงข้อมูลออเดอร์ที่เกี่ยวข้อง
        $orders = Order::with([
                'customer:cus_id,fullname',
                'promotions:promo_id,promo_name',
                'details.product:pro_id,pro_name'
            ])
            ->where('em_id', $emId) // ค้นหาเฉพาะออเดอร์ของพนักงานคนนี้
            ->whereNotNull('receive_date') // ค้นหาเฉพาะออเดอร์ที่เสร็จสิ้นแล้ว
            ->orderBy('order_date', 'desc') // เรียงตามวันที่ล่าสุด
            ->get();

        return response()->json($orders);
    }
}
