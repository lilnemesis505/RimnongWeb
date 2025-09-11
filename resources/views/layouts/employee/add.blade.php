<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>เพิ่มข้อมูลพนักงาน</title>
    <!-- AdminLTE CSS via CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <span class="navbar-brand">เพิ่มข้อมูลพนักงาน</span>
    </nav>

    <!-- Sidebar -->
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

    <!-- Content Wrapper -->
    <div class="content-wrapper p-3">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-user-plus"></i> เพิ่มข้อมูลพนักงาน</h3>
            </div>

            <form action="{{ route('employee.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label>ชื่อ-สกุล</label>
                        <input type="text" name="em_name" class="form-control" placeholder="กรอกชื่อ-สกุล" required>
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" placeholder="ตั้งค่า Username" required>
                    </div>
                    <div class="form-group">
                        <label>รหัสผ่าน</label>
                        <input type="password" name="password" class="form-control" placeholder="ตั้งค่ารหัสผ่าน" required>
                    </div>
                    <div class="form-group">
                        <label>เบอร์โทร</label>
                        <input type="text" name="em_tel" class="form-control" placeholder="เช่น 0812345678" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="em_email" class="form-control" placeholder="example@mail.com" required>
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


<!-- AdminLTE JS via CDN -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
