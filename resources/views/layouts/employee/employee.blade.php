<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AdminLTE Template</title>
    <!-- AdminLTE CSS via CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <span class="navbar-brand">พนักงาน</span>
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
                         <a href="{{ route('employee.index') }}" class="nav-link" style="background-color:#007bff; color: #fff;">
                    <i class="nav-icon fas fa-user"></i> <p>ข้อมูลพนักงาน</p>
                         </a>
                     </li>
                     <li class="nav-item">
                        <a href="{{ route('employee.add') }}" class="nav-link" style="background: none; color: #fff;">
                     <i class="nav-icon fas fa-plus"></i> <p>เพิ่มข้อมูลพนักงาน</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" style="background: none; color: #fff;">
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
                <h3 class="card-title">ข้อมูลพนักงาน</h3>
            </div>
@if(session('success'))
<div id="success-alert" class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<script>
    // ซ่อน alert หลัง 2 วินาที
    setTimeout(function() {
        var alertEl = document.getElementById('success-alert');
        if (alertEl) {
            alertEl.classList.remove('show');
            alertEl.classList.add('hide');
        }
    }, 2000);
</script>
@endif
            <div class="card-body p-0">
           <table class="table table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>ชื่อ-สกุล</th>
            <th>Username</th>
            <th>เบอร์โทร</th>
            <th>Email</th>
            <th>แก้ใข</th>

        </tr>
    </thead>
    <tbody>
        @foreach($employees as $employee)
        <tr>
            <td>{{ $loop->iteration + ($employees->currentPage() - 1) * $employees->perPage() }}</td>
            <td>{{ $employee->em_name }}</td>
            <td>{{ $employee->username }}</td>
            <td>{{ $employee->em_tel }}</td>
            <td>{{ $employee->em_email }}</td>
            <td>
                 <a href="{{ route('employee.edit', ['id' => $employee->em_id]) }}" class="btn btn-sm btn-warning">
    แก้ไข
</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

                </table>
            </div>
            <div class="card-footer clearfix">
                {{ $employees->links() }}
            </div>
        </div>
    </div>
</div>
<!-- แจ้งเตือนเมื่อบันทึกข้อมูลเสร็จ -->

<!-- AdminLTE JS via CDN -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>