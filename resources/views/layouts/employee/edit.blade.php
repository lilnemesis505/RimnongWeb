
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขข้อมูลพนักงาน</title>
    <!-- AdminLTE CSS via CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <span class="navbar-brand">แก้ไขข้อมูลพนักงาน</span>
    </nav>
     <!-- Sidebar -->
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
    <!-- Content Wrapper -->
    <div class="content-wrapper p-3" style="min-height: 100vh;">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">แก้ไขข้อมูลพนักงาน</h3>
            </div>

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
                <form action="{{ route('employee.update', ['id' => $employee->em_id]) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="em_name" class="form-label">ชื่อ-สกุล</label>
                        <input type="text" name="em_name" id="em_name" class="form-control" value="{{ old('em_name', $employee->em_name) }}" required>
                        @error('em_name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" name="username" id="username" class="form-control" value="{{ old('username', $employee->username) }}" required>
                        @error('username')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="em_tel" class="form-label">เบอร์โทร</label>
                        <input type="text" name="em_tel" id="em_tel" class="form-control" value="{{ old('em_tel', $employee->em_tel) }}" required>
                        @error('em_tel')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="em_email" class="form-label">Email</label>
                        <input type="email" name="em_email" id="em_email" class="form-control" value="{{ old('em_email', $employee->em_email) }}" required>
                        @error('em_email')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">บันทึกการแก้ไข</button>
                    <a href="{{ route('employee.index') }}" class="btn btn-secondary">ยกเลิก</a>
            </div>
        </div>
    </div>
</div>

<!-- AdminLTE JS via CDN -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>