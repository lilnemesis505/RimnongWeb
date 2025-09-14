<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>เพิ่มข้อมูลพนักงาน</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <span class="navbar-brand">เพิ่มข้อมูลพนักงาน</span>
    </nav>

    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="#" class="brand-link">
            <span class="brand-text font-weight-light">{{ session('admin_fullname') }}</span>
        </a>
        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column">
                    <li class="nav-item">
                        <a href="{{ route('welcome') }}" class="nav-link">
                            <i class="nav-icon fas fa-home-alt"></i> <p>หน้าหลัก</p>
                        </a>
                    </li>
                </ul>
                <hr style="border-top: 1px solid #fff;">
                  <ul class="nav nav-pills nav-sidebar flex-column">
                    <li class="nav-item">
                        <a href="{{ route('employee.index') }}" class="nav-link">
                            <i class="nav-icon fas fa-user"></i> <p>ข้อมูลพนักงาน</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('employee.add') }}" class="nav-link active">
                            <i class="nav-icon fas fa-plus"></i> <p>เพิ่มข้อมูลพนักงาน</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <div class="content-wrapper p-3">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-user-plus"></i> เพิ่มข้อมูลพนักงาน</h3>
            </div>

            {{-- ให้ Form มี ID เพื่อง่ายต่อการอ้างอิงใน JS --}}
            <form id="employee-form" action="{{ route('employee.store') }}" method="POST">
                @csrf
                {{-- เพิ่ม Hidden field สำหรับส่ง Flag ยืนยัน --}}
                <input type="hidden" name="confirm_creation" id="confirm_creation_flag" value="">

                <div class="card-body">
                     {{-- แสดงข้อความ error (ถ้ามี) --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="form-group">
                        <label>ชื่อ-สกุล</label>
                        {{-- old('em_name') ช่วยให้ข้อมูลไม่หายไปเมื่อเกิด error หรือต้องยืนยัน --}}
                        <input type="text" name="em_name" class="form-control" placeholder="กรอกชื่อ-สกุล" value="{{ old('em_name') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" placeholder="ตั้งค่า Username" value="{{ old('username') }}" required>
                    </div>
                    <div class="form-group">
                        <label>รหัสผ่าน</label>
                        <input type="password" name="password" class="form-control" placeholder="ตั้งค่ารหัสผ่าน" required>
                    </div>
                    <div class="form-group">
                        <label>เบอร์โทร</label>
                        <input type="text" name="em_tel" class="form-control" placeholder="เช่น 0812345678" value="{{ old('em_tel') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="em_email" class="form-control" placeholder="example@mail.com" value="{{ old('em_email') }}" required>
                    </div>
                </div>

                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> บันทึก
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- === ส่วนของ Modal ที่จะแสดงเมื่อชื่อซ้ำ === --}}
<div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-warning">
        <h5 class="modal-title">ยืนยันการสร้างข้อมูล</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p id="confirmationMessage"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
        <button type="button" id="confirm-create-btn" class="btn btn-warning">ยืนยันการสร้าง</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

{{-- === JavaScript สำหรับควบคุม Modal === --}}
<script>
    $(document).ready(function() {
        // ตรวจสอบว่ามี session 'confirm_duplicate_name' ส่งมาหรือไม่
        @if(session('confirm_duplicate_name'))
            // ดึงชื่อที่ซ้ำจาก session
            const duplicateName = "{{ session('confirm_duplicate_name') }}";
            
            // ตั้งค่าข้อความใน modal
            $('#confirmationMessage').text(`พนักงานชื่อ "${duplicateName}" มีอยู่ในระบบแล้ว คุณยืนยันที่จะสร้างพนักงานซ้ำหรือไม่?`);
            
            // แสดง modal
            $('#confirmationModal').modal('show');
        @endif

        // เมื่อกดปุ่ม 'ยืนยันการสร้าง' ใน modal
        $('#confirm-create-btn').on('click', function() {
            // 1. ตั้งค่า hidden field 'confirm_creation' เป็น true
            $('#confirm_creation_flag').val('true');
            
            // 2. submit ฟอร์มหลัก
            $('#employee-form').submit();
        });
    });
</script>
</body>
</html>