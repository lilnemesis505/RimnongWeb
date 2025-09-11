<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>เพิ่มข้อมูลสินค้า</title>
    <!-- AdminLTE CSS via CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <!-- Navbar -->
    <!-- <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <span class="navbar-brand">เพิ่มข้อมูลสินค้า</span>
    </nav> -->

   <!-- Sidebar -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="#" class="brand-link">
            <span class="brand-text font-weight-light">{{ session('admin_fullname') }}</span>
        </a>
        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column">
                    <li class="nav-item">
                        <a href="{{ route('welcome') }}" class="nav-link text-white">
                            <i class="nav-icon fas fa-home-alt"></i>
                            <p>หน้าหลัก</p>
                        </a>
                    </li>
                </ul>
                <hr style="border-top: 1px solid #fff;">
                <ul class="nav nav-pills nav-sidebar flex-column">
                    <li class="nav-item">
                        <a href="{{ route('product.index') }}" class="nav-link text-white" >
                            <i class="nav-icon fas fa-shopping-cart"></i>
                            <p>ข้อมูลสินค้า</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('product.add') }}" class="nav-link active">
                            <i class="nav-icon fas fa-plus"></i>
                            <p>เพิ่มข้อมูลสินค้า</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('protype.add') }}" class="nav-link text-white" >
                            <i class="nav-icon fas fa-chart-bar"></i>
                            <p>ประเภทสินค้า</p>
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
                <h3 class="card-title"><i class="fas fa-cart-plus"></i> เพิ่มข้อมูลสินค้า</h3>
            </div>

<form action="{{ route('product.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="card-body">
        <div class="form-group">
            <label>ชื่อสินค้า</label>
            <input type="text" name="pro_name" class="form-control" placeholder="กรอกชื่อสินค้า" required>
        </div>

        <div class="form-group">
            <label>ประเภทสินค้า</label><br>
            <br>
            @foreach($types as $type)
            <div class="form-check form-check-inline">
            <input type="radio" name="type_id" value="{{ $type->type_id }}" required>
            <label>{{ $type->type_name }}</label>
            </div>
            @endforeach
        </div>

        <div class="form-group">
            <label>ราคา</label>
            <input type="number" name="price" class="form-control" placeholder="ราคาสินค้า" required>
        </div>

        <div class="form-group">
            <label>รูปสินค้า</label>
            <input type="file" name="image" class="form-control" accept="image/*">
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
<!-- Footer -->
<footer class="main-footer bg-secondary"> text-center py-2">
    <strong>&copy; {{ date('Y') }} .ร้านริมหนอง คาเฟ่</strong> กินกาแฟให้อร่อย
</footer>


<!-- AdminLTE JS via CDN -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
