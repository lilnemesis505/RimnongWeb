<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>เพิ่มวัสดุคงคลัง</title>
    <!-- AdminLTE CSS via CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <span class="navbar-brand">เพิ่มข้อมูลโปรโมชั่น</span>
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
                        <a href="{{ route('promotion.index') }}" class="nav-link" style="background: none; color: #fff;">
                            <i class="nav-icon fas fa-ticket"></i> <p>ข้อมูลโปรโมชั่น</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('promotion.add') }}" class="nav-link" style="background-color:#007bff; color: #fff;">
                            <i class="nav-icon fas fa-plus"></i> <p>เพิ่มโปรโมชั่น</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>


    <!-- Content Wrapper -->
    <div class="content-wrapper p-4">
        <h2 class="mb-4">จัดการโปรโมชั่น</h2>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card mb-4">
            <div class="card-header">เพิ่มโปรโมชั่นใหม่</div>
            <div class="card-body">
                <form action="{{ route('promotion.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="promo_name" class="form-label">ชื่อโปรโมชั่น</label>
                        <input type="text" name="promo_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="promo_discount" class="form-label">ราคาที่ลด</label>
                        <input type="number" step="0.01" name="promo_discount" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="promo_start" class="form-label">วันที่เริ่ม</label>
                        <input type="datetime-local" name="promo_start" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="promo_end" class="form-label">วันที่สิ้นสุด</label>
                        <input type="datetime-local" name="promo_end" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">เพิ่มโปรโมชั่น</button>
                </form>
            </div>
        </div>
    </div>


<!-- AdminLTE JS via CDN -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
