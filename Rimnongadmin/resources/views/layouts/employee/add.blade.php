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
    @include('layouts.assets._navbar')

    @include('layouts.assets._sidebar')

    <div class="content-wrapper p-3">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-user-plus"></i> เพิ่มข้อมูลพนักงาน</h3>
            </div>

            <form id="employee-form" action="{{ route('employee.store') }}" method="POST">
                @csrf
                <input type="hidden" name="confirm_creation" id="confirm_creation_flag" value="">

                <div class="card-body">
                     {{-- ... (ส่วนแสดง Error เหมือนเดิม) ... --}}
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
                        <input type="text" name="em_name" class="form-control" placeholder="กรอกชื่อ-สกุล" value="{{ old('em_name') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" placeholder="ตั้งค่า Username" value="{{ old('username') }}" required>
                    </div>

                    {{-- ✅ [FIX] แก้ไข Placeholder เป็น 8 ตัว --}}
                    <div class="form-group">
                        <label>รหัสผ่าน</label>
                        <input type="password" name="password" class="form-control" placeholder="ตั้งค่ารหัสผ่าน (ขั้นต่ำ 8 ตัว)" required>
                    </div>
                    
                    {{-- ✅ [FIX] เพิ่มช่องยืนยันรหัสผ่าน --}}
                    <div class="form-group">
                        <label>ยืนยันรหัสผ่าน</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="ยืนยันรหัสผ่านอีกครั้ง" required>
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

{{-- ... (Modal และ Scripts เหมือนเดิม) ... --}}
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

<script>
    $(document).ready(function() {
        @if(session('confirm_duplicate_name'))
            const duplicateName = "{{ session('confirm_duplicate_name') }}";
            $('#confirmationMessage').text(`พนักงานชื่อ "${duplicateName}" มีอยู่ในระบบแล้ว คุณยืนยันที่จะสร้างพนักงานซ้ำหรือไม่?`);
            $('#confirmationModal').modal('show');
        @endif

        $('#confirm-create-btn').on('click', function() {
            $('#confirm_creation_flag').val('true');
            $('#employee-form').submit();
        });
    });
</script>
</body>
</html>