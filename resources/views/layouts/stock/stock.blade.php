<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ข้อมูลล็อตสินค้า </title>
    <!-- AdminLTE CSS via CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <span class="navbar-brand">ข้อมูลล็อตสินค้า</span>
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
                        <a href="{{ route('welcome') }}" class="nav-link text-white">
                            <i class="nav-icon fas fa-home-alt"></i> <p>หน้าหลัก</p>
                        </a>
                    </li>
                </ul>
                <hr style="border-top: 1px solid #fff;">
                <ul class="nav nav-pills nav-sidebar flex-column">
                    <li class="nav-item">
                        <a href="#" class="nav-link active" style="background-color: #007bff;">
                            <i class="nav-icon fas fa-box"></i> <p>ข้อมูลล็อตสินค้า</p>
                        </a>
                    </li>
                     <li class="nav-item">
                        <a href="{{ route('stock.add') }}" class="nav-link text-white">
                            <i class="nav-icon fas fa-plus"></i> <p>เพิ่มข้อมูลนำเข้า</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <!-- Content Wrapper -->
    <div class="content-wrapper p-3" style="min-height: 100vh;">
        <div class="card">
            <div class="card-body p-0">
                <table class="table table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>ชื่อวัสดุ</th>
                            <th>ประเภท</th>
                            <th>วันที่นำเข้า</th>
                            <th>จำนวน</th>
                            <th>วันหมดอายุ</th>
                            <th>คงเหลือ</th>
                            <th>ราคาต่อหน่วย</th>
                            <th>สถานะ</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stock_mats as $i => $mat)
                        <tr>
                            <td>{{ $stock_mats->firstItem() + $i }}</td>
                            <td>{{ $mat->mat_name }}</td>
                            <td>{{ $mat->type->type_name ?? '-' }}</td>
                            <td>{{ $mat->import_date ?? '-' }}</td>
                            <td>{{ $mat->quantity }}</td>
                            <td>{{ $mat->exp_date ?? '-' }}</td>
                            <td>{{ $mat->remain }}</td>
                            <td>{{ number_format($mat->unitcost, 2) }}</td>
                            <td>
                            @switch($mat->status)
                            @case(0)
                            ปกติ
                            @break
                            @case(1)
                            หมด และยังไม่ได้สั่ง
                            @break
                            @case(2)
                            หมด และสั่งซื้อแต่ยังไม่ได้รับ
                            @break
                            @default
                            ไม่ทราบสถานะ
                            @endswitch
                            </td>
                            <td>
                            <a href="{{ route('stock.edit', $mat->mat_id) }}" class="btn btn-sm"><i class="fas fa-edit"></i> แก้ไข</a>
                            </a>
                            </td>


                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer clearfix">
                {{ $stock_mats->links() }}
            </div>
        </div>
    </div>
</div>

<!-- AdminLTE JS via CDN -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
