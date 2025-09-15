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
        'password' => 'required|string|min:6',
        'em_tel'   => 'required|string|max:10',
        'em_email' => 'required|email|unique:employee,em_email',
    ];

    // 2. Define your custom Thai messages
    $messages = [
        'em_name.required' => 'กรุณากรอกชื่อ-สกุล',
        'username.required' => 'กรุณากรอก Username',
        'username.unique'   => 'Username นี้มีผู้ใช้งานแล้ว', // <-- This is the one you asked about
        'password.required' => 'กรุณากรอกรหัสผ่าน',
        'password.min'      => 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร',
        'em_tel.required'   => 'กรุณากรอกเบอร์โทร',
        'em_tel.max'        => 'เบอร์โทรต้องมี 10 หลัก',
        'em_email.required' => 'กรุณากรอกอีเมล',
        'em_email.email'    => 'รูปแบบอีเมลไม่ถูกต้อง',
        'em_email.unique'   => 'อีเมลนี้มีผู้ใช้งานแล้ว',
    ];

    // --- Part 1: Handling the confirmation from the modal ---
    if ($request->input('confirm_creation') === 'true') {
        // Pass the rules and custom messages to the validate method
        $validated = $request->validate($rules, $messages);
        $validated['password'] = bcrypt($validated['password']);
        Employee::create($validated);

        return redirect()->route('employee.index')->with('success', 'เพิ่มข้อมูลพนักงานเรียบร้อยแล้ว');
    }

    // --- Part 2: Handling the initial form submission ---

    // 2.1 Check for unique username and email first
    $request->validate([
        'username' => 'unique:employee,username',
        'em_email' => 'unique:employee,em_email',
    ], $messages); // Pass messages here as well
    
    // 2.2 If username/email are OK, check for the duplicate name
    $existingEmployee = Employee::where('em_name', $request->em_name)->first();
    if ($existingEmployee) {
        return redirect()->back()
            ->withInput()
            ->with('confirm_duplicate_name', $existingEmployee->em_name);
    }

    // 2.3 If no duplicates, validate all rules and create
    $validated = $request->validate($rules, $messages);
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
    $rules = [
        // เพิ่ม unique rule สำหรับ em_name โดยไม่เช็คกับ ID ของตัวเอง
        'em_name'  => 'required|string|max:60|unique:employee,em_name,' . $id . ',em_id',
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

    // 3. ทำการ Validate ข้อมูล
    $validated = $request->validate($rules, $messages);

    // 4. ค้นหาพนักงานและอัปเดตข้อมูล
    $employee = Employee::findOrFail($id);
    $employee->update($validated);

    // 5. ส่งกลับไปที่หน้ารายการพร้อมข้อความแจ้งเตือน
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
