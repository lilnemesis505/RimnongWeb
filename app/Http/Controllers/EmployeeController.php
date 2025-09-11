<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;

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
}