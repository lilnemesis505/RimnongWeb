<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขข้อมูลพนักงาน</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
    {{-- Bootstrap 4 (จำเป็นสำหรับ Modal) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <span class="navbar-brand">แก้ไขข้อมูลพนักงาน</span>
    </nav>
     <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="#" class="brand-link">
            <span class="brand-text font-weight-light">{{ session('admin_fullname') }}</span>
        </a>
        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    <li class="nav-item">
                        <a href="{{ route('welcome') }}" class="nav-link" style="background: none; color: #fff;">
                            <i class="nav-icon fas fa-home-alt"></i> <p>หน้าหลัก</p>
                        </a>
                    </li>
                </ul>
                <hr style="border-top: 1px solid #fff;">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    <li class="nav-item">
                         <a href="{{ route('employee.index') }}" class="nav-link" style="background: none; color: #fff;">
                    <i class="nav-icon fas fa-user"></i> <p>ข้อมูลพนักงาน</p>
                         </a>
                     </li>
                    <li class="nav-item">
                        <a href="" class="nav-link" style="background-color:#007bff; color: #fff;">
                     <i class="nav-icon fas fa-gear"></i> <p>แก้ไขข้อมูลพนักงาน</p>
                        </a>
                     </li>
                </ul>
            </nav>
        </div>
    </aside>
    <div class="content-wrapper p-3" style="min-height: 100vh;">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">แก้ไขข้อมูลพนักงาน</h3>
            </div>

            {{-- ... (โค้ดส่วน Alert session('success') เหมือนเดิม) ... --}}
            @if(session('success'))
            <div id="success-alert" class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <script>
                setTimeout(function() {
                    var alertEl = document.getElementById('success-alert');
                    if (alertEl) {
                        alertEl.classList.remove('show');
                        alertEl.classList.add('hide');
                    }
                }, 2000);
            </script>
            @endif

            <div class="card-body">
                <form id="employee-form" action="{{ route('employee.update', ['id' => $employee->em_id]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="confirm_update" id="confirm_update_flag" value="">
                    
                    {{-- ช่องที่ 1: ชื่อ-สกุล --}}
                    <div class="mb-3">
                        <label for="em_name" class="form-label">ชื่อ-สกุล</label>
                        <input type="text" name="em_name" id="em_name" class="form-control" value="{{ old('em_name', $employee->em_name) }}" required>
                        @error('em_name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                   {{-- 
                     จุดแก้ไข: 
                     เปลี่ยนจาก 'em_name' (ซ้ำ) เป็น 'username' 
                   --}}
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" name="username" id="username" class="form-control" value="{{ old('username', $employee->username) }}" required>
                        @error('username')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- ช่องที่ 3: เบอร์โทร --}}
                    <div class="mb-3">
                        <label for="em_tel" class="form-label">เบอร์โทร</label>
                        <input type="text" name="em_tel" id="em_tel" class="form-control" value="{{ old('em_tel', $employee->em_tel) }}" required>
                        @error('em_tel')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- ช่องที่ 4: Email --}}
                    <div class="mb-3">
                        <label for="em_email" class="form-label">Email</label>
                        <input type="email" name="em_email" id="em_email" class="form-control" value="{{ old('em_email', $employee->em_email) }}" required>
                        @error('em_email')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                   <button type="submit" class="btn btn-primary">บันทึกการแก้ไข</button>
                    <a href="{{ route('employee.index') }}" class="btn btn-secondary">ยกเลิก</a>
                </form> 
            </div>
        </div>
    </div>
</div>

{{-- ... (ส่วน Modal และ JavaScripts เหมือนเดิม) ... --}}
<div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-warning">
        <h5 class="modal-title">ยืนยันการแก้ไขข้อมูล</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p id="confirmationMessage"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
        <button type="button" id="confirm-btn" class="btn btn-warning">ยืนยันการแก้ไข</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script>
    $(document).ready(function() {
        // ตรวจสอบว่ามี session 'confirm_duplicate_name_edit' ส่งมาหรือไม่
        @if(session('confirm_duplicate_name_edit'))
            // ดึงชื่อที่ซ้ำจาก session
            const duplicateName = "{{ session('confirm_duplicate_name_edit') }}";
            
            // ตั้งค่าข้อความใน modal
            $('#confirmationMessage').text(`พนักงานชื่อ "${duplicateName}" มีอยู่ในระบบแล้ว คุณยืนยันที่จะแก้ไขข้อมูลซ้ำหรือไม่?`);
            
            // แสดง modal
            $('#confirmationModal').modal('show');
        @endif

        // เมื่อกดปุ่ม 'ยืนยันการแก้ไข' ใน modal
        $('#confirm-btn').on('click', function() {
            // 1. ตั้งค่า hidden field 'confirm_update' เป็น true
            $('#confirm_update_flag').val('true');
            
            // 2. submit ฟอร์มหลัก
            $('#employee-form').submit();
        });
    });
</script>
</body>
</html>