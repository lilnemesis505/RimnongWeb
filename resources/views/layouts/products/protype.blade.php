<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ประเภทสินค้า</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <span class="navbar-brand">ประเภทสินค้า</span>
    </nav>

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
                        <a href="{{ route('product.index') }}" class="nav-link active" >
                            <i class="nav-icon fas fa-shopping-cart"></i>
                            <p>ข้อมูลสินค้า</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('product.add') }}" class="nav-link text-white">
                            <i class="nav-icon fas fa-plus"></i>
                            <p>เพิ่มข้อมูลสินค้า</p>
                        </a>
                    </li>

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

    <div class="content-wrapper p-3">
        {{-- ✅ 1. ส่วนฟอร์มสำหรับเพิ่มข้อมูล (กล่องบน) --}}
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-tags"></i> เพิ่มประเภทสินค้า</h3>
                    </div>
                    <form action="{{ route('protype.store') }}" method="POST">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label for="type_name">ชื่อประเภทสินค้า</label>
                                <input type="text" name="type_name" id="type_name" class="form-control @error('type_name') is-invalid @enderror" placeholder="กรอกชื่อประเภทสินค้า" required value="{{ old('type_name') }}">
                                @error('type_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer text-center">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> บันทึก
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ✅ 2. ส่วนตารางแสดงข้อมูล (กล่องล่าง) --}}
        <div class="row justify-content-center mt-4">
            <div class="col-md-8">
                <div class="card card-info">
                     <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-list"></i> รายการประเภทสินค้า</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-bordered table-hover mb-0">
                            <thead class="thead-light text-center">
                                <tr>
                                    <th style="width: 15%;">#</th>
                                    <th>ชื่อประเภทสินค้า</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($protypes as $index => $protype)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>{{ $protype->type_name }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted">ไม่มีข้อมูลประเภทสินค้า</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

</body>
</html>