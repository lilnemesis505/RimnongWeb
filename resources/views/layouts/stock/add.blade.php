<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>เพิ่มวัสดุคงคลัง</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <span class="navbar-brand">เพิ่มข้อมูลนำเข้า</span>
    </nav>
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
                        <a href="{{ route('stock.add') }}" class="nav-link active">
                            <i class="nav-icon fas fa-plus"></i> <p>เพิ่มข้อมูลนำเข้า</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <div class="content-wrapper p-3">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-box"></i> เพิ่มวัสดุคงคลัง</h3>
            </div>

            <form action="{{ route('stock.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">

                    {{-- ✅ [FIX] เพิ่มส่วนแสดงผล Error --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <h5 class="font-weight-bold">เกิดข้อผิดพลาด!</h5>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    {{-- จบส่วนแสดงผล Error --}}


                    <div class="form-group">
                        <label>ชื่อวัสดุ</label>
                        <input type="text" name="mat_name" class="form-control" placeholder="กรอกชื่อวัสดุ" value="{{ old('mat_name') }}" required>
                    </div>

                    <div class="form-group">
                        <label>ประเภทวัสดุ</label>
                        <select name="type_id" class="form-control" required>
                            <option value="">-- เลือกประเภท --</option>
                            @foreach($types as $type)
                                <option value="{{ $type->type_id }}" {{ old('type_id') == $type->type_id ? 'selected' : '' }}>
                                    {{ $type->type_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>วันที่นำเข้า</label>
                        <input type="date" name="import_date" class="form-control" value="{{ old('import_date') }}" required>
                    </div>

                    <div class="form-group">
                        <label>จำนวนที่นำเข้า</label>
                        <input type="number" name="quantity" class="form-control" value="{{ old('quantity') }}" required>
                    </div>

                    <div class="form-group">
                        <label>วันหมดอายุ</label>
                        <input type="date" name="exp_date" class="form-control" value="{{ old('exp_date') }}">
                    </div>

                    <div class="form-group">
                        <label>จำนวนคงเหลือ</label>
                        <input type="number" name="remain" class="form-control" value="{{ old('remain') }}" required>
                    </div>

                    <div class="form-group">
                        <label>ราคาต่อหน่วย</label>
                        <input type="number" step="0.01" name="unitcost" class="form-control" value="{{ old('unitcost') }}" required>
                    </div>

                    <div class="form-group">
                        <label>สถานะ</label>
                        <select name="status" class="form-control" required>
                            <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>ปกติ</option>
                            <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>หมด และยังไม่ได้สั่ง</option>
                            <option value="2" {{ old('status') == '2' ? 'selected' : '' }}>หมด และสั่งซื้อแต่ยังไม่ได้รับ</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="image_upload">รูปภาพวัสดุ (รองรับ .jpg, .png)</label>
                        <input type="file" name="image_upload" id="image_upload" class="form-control-file" accept=".jpg, .jpeg, .png">
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

<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>