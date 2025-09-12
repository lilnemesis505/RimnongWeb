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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'em_name'  => 'required|string|max:60',
            'username' => 'required|string|max:35|unique:employee,username',
            'password' => 'required|string|min:6',
            'em_tel'   => 'required|string|max:10',
            'em_email' => 'required|email|unique:employee,em_email',
        ]);

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
    $employee = Employee::findOrFail($id);

    $validated = $request->validate([
        'em_name'  => 'required|string|max:60',
        'username' => 'required|string|max:35|unique:employees,username,' . $id . ',em_id',
        'em_tel'   => 'required|string|max:10',
        'em_email' => 'required|email|unique:employees,em_email,' . $id . ',em_id',
    ]);

    $employee->update($validated);

    return redirect()->route('employee.index')->with('success', 'แก้ไขข้อมูลสำเร็จ');
}
public function destroy($id)
{
    $employee = Employee::findOrFail($id);
    $employee->delete();

    return redirect()->route('employee.index')->with('success', 'ลบข้อมูลพนักงานเรียบร้อยแล้ว');

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
                'promotion:promo_id,promo_name',
                'details.product:pro_id,pro_name'
            ])
            ->where('em_id', $emId) // ค้นหาเฉพาะออเดอร์ของพนักงานคนนี้
            ->whereNotNull('receive_date') // ค้นหาเฉพาะออเดอร์ที่เสร็จสิ้นแล้ว
            ->orderBy('order_date', 'desc') // เรียงตามวันที่ล่าสุด
            ->get();

        return response()->json($orders);
    }
}
