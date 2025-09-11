<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Promotion Management</title>
    <!-- AdminLTE CSS via CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <span class="navbar-brand">ข้อมูลโปรโมชั่น</span>
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
                        <a href="{{ route('promotion.index') }}" class="nav-link" style="background-color:#007bff; color: #fff;">
                            <i class="nav-icon fas fa-ticket"></i> <p>ข้อมูลโปรโมชั่น</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('promotion.add') }}" class="nav-link" style="background: none; color: #fff;">
                            <i class="nav-icon fas fa-plus"></i> <p>เพิ่มโปรโมชั่น</p>
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
                <h3 class="card-title">ข้อมูลโปรโมชั่น</h3>
            </div>

            <!-- Success Alert -->
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

            <div class="card-body p-0">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ชื่อโปรโมชั่น</th>
                            <th>ส่วนลด (บาท)</th>
                            <th>วันที่เริ่ม</th>
                            <th>วันที่สิ้นสุด</th>
                            <th>ลบโปรโมชั่น</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($promotions as $promotion)
                        <tr>
                            <td>{{ $loop->iteration + ($promotions->currentPage() - 1) * $promotions->perPage() }}</td>
                            <td>{{ $promotion->promo_name }}</td>
                            <td>{{ $promotion->promo_discount }}</td>
                            <td>{{ $promotion->promo_start }}</td>
                            <td>{{ $promotion->promo_end }}</td>
                            <td>
                                <form action="{{ route('promotion.delete', $promotion->promo_id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('คุณแน่ใจว่าจะลบโปรโมชั่นนี้?')">
                                        ลบ
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card-footer clearfix">
                {{ $promotions->links() }}
            </div>
        </div>
    </div>
</div>

<!-- AdminLTE JS via CDN -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
