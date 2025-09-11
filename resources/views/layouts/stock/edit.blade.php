<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขวัสดุคงคลัง</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <span class="navbar-brand">ข้อมูลปรับปรุงล็อตสินค้า</span>
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
                        <a href="{{ route('stock.index') }}" class="nav-link">
                            <i class="nav-icon fas fa-box"></i> <p>ข้อมูลล็อตสินค้า</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('stock.add') }}" class="nav-link">
                            <i class="nav-icon fas fa-plus"></i> <p>เพิ่มข้อมูลนำเข้า</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link active text-white">
                            <i class="nav-icon fas fa-gear"></i> <p>ข้อมูลปรับปรุงล็อตสินค้า</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <!-- Content Wrapper -->
<!-- Content Wrapper -->
<div class="content-wrapper p-3">
    <div class="card card-warning">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-edit"></i> แก้ไขข้อมูลล็อตวัตถุดิบ</h3>
        </div>

        <form action="{{ route('stock.update', $mat->mat_id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card-body">

                <div class="form-group">
                    <label>ชื่อวัสดุ</label>
                    <input type="text" class="form-control" value="{{ $mat->mat_name }}" readonly>
                </div>

                <div class="form-group">
                    <label>ประเภทวัสดุ</label>
                    <input type="text" class="form-control" value="{{ $mat->type->type_name ?? '-' }}" readonly>
                </div>

                <div class="form-group">
                    <label>วันที่นำเข้า</label>
                    <p class="form-control-plaintext">
                        {{ \Carbon\Carbon::parse($mat->import_date)->format('d/m/Y') }}
                    </p>
                </div>

                <div class="form-group">
                    <label>วันหมดอายุ</label>
                    <p class="form-control-plaintext">
                        {{ \Carbon\Carbon::parse($mat->exp_date)->format('d/m/Y') }}
                    </p>
                </div>

                <hr>

                <div class="form-group">
                    <label>จำนวนที่นำเข้า</label>
                    <input type="number" name="quantity" class="form-control" value="{{ $mat->quantity }}" required>
                </div>

                <div class="form-group">
                    <label>จำนวนคงเหลือ</label>
                    <input type="number" name="remain" class="form-control" value="{{ $mat->remain }}" required>
                </div>

                <div class="form-group">
                    <label>ราคาต่อหน่วย</label>
                    <input type="number" step="0.01" name="unitcost" class="form-control" value="{{ $mat->unitcost }}" required>
                </div>

                <div class="form-group">
                    <label>สถานะ</label><br>

                    <div class="form-check">
                        <input type="radio" name="status" value="0" {{ $mat->status == 0 ? 'checked' : '' }}>
                        <label class="form-check-label">ปกติ</label>
                    </div>

                    <div class="form-check">
                        <input type="radio" name="status" value="1" {{ $mat->status == 1 ? 'checked' : '' }}>
                        <label class="form-check-label">หมด และยังไม่ได้สั่ง</label>
                    </div>

                    <div class="form-check">
                        <input type="radio" name="status" value="2" {{ $mat->status == 2 ? 'checked' : '' }}>
                        <label class="form-check-label">หมด และสั่งซื้อแต่ยังไม่ได้รับ</label>
                    </div>
                </div>

            </div>

            <!-- ปุ่มอยู่ข้างกัน -->
            <div class="card-footer d-flex justify-content-end gap-2">
                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-save"></i> บันทึกการแก้ไข
                </button>
        </form>

        <form action="{{ route('stock.destroy', $mat->mat_id) }}" method="POST"
              onsubmit="return confirm('คุณแน่ใจว่าต้องการลบข้อมูลนี้หรือไม่?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash-alt"></i> ลบข้อมูล
            </button>
        </form>
            </div>

    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
