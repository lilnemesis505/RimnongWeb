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
   @include('layouts.assets._navbar')
    <!-- Sidebar -->
    @include('layouts.assets._sidebar')
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