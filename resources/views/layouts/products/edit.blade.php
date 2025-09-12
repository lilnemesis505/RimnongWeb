<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขข้อมูลสินค้า</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <span class="navbar-brand">แก้ไขข้อมูลสินค้า</span>
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
                        <a href="{{ route('product.index') }}" class="nav-link text-white" >
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
                    <li class="nav-item">
                        <a href="#" class="nav-link active">
                        <i class="nav-icon fas fa-gear"></i>
                        <p>แก้ไขข้อมูลสินค้า</p>
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

    <div class="content-wrapper p-3">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8"> <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-gear"></i> แก้ไขข้อมูลสินค้า</h3>
                    </div>


<form action="{{ route('product.update', $product->pro_id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="card-body">
        <div class="form-group">
            <label>ชื่อสินค้า</label>
            <input type="text" name="pro_name" class="form-control" value="{{ $product->pro_name }}" required>
        </div>

        <div class="form-group">
            <label>ประเภทสินค้า</label><br><br>
            @foreach($types as $type)
            <div class="form-check form-check-inline">
                <input type="radio" name="type_id" value="{{ $type->type_id }}"
                    {{ $product->type_id == $type->type_id ? 'checked' : '' }} required>
                <label>{{ $type->type_name }}</label>
            </div>
            @endforeach
        </div>

        <div class="form-group">
            <label>ราคา</label>
            <input type="number" name="price" class="form-control" value="{{ $product->price }}" required>
        </div>
        <div class="form-group mt-3">
    <label for="image">เลือกรูปใหม่</label>
    <input type="file" name="image" id="image" class="form-control-file" accept="image/*">

</div>

            {{-- 🔥🔥 ส่วนที่ถูกแก้ไข 🔥🔥 --}}
            <div class="mt-3 text-center">
                @if($product->image)
                    <label class="d-block">รูปปัจจุบัน:</label>
                    <img src="{{ $product->image }}" class="img-fluid border" style="max-height: 200px;">
                @else
                    <p class="text-muted">ไม่มีรูปสินค้าเดิม</p>
                @endif
            </div>

        </div>
    </div>

    <div class="card-footer d-flex justify-content-end gap-2">
    <button type="submit" class="btn btn-warning">
        <i class="fas fa-save"></i> บันทึกการแก้ไข
    </button>
</form>
    <form action="{{ route('product.destroy', $product->pro_id) }}" method="POST" onsubmit="return confirm('คุณแน่ใจว่าต้องการลบสินค้านี้?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">
            <i class="fas fa-trash-alt"></i> ลบข้อมูล
        </button>
    </form>
</div>

</form>

        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>