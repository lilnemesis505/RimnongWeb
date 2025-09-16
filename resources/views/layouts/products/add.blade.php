<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>เพิ่มข้อมูลสินค้า</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
    <style>
        /* CSS เพิ่มเติมเพื่อให้รูป preview สวยงาม */
        #image-preview-container {
            margin-top: 15px;
            display: none; /* ซ่อนไว้ก่อนจนกว่าจะเลือกรูป */
        }
        #image-preview {
            max-width: 100%;
            max-height: 250px;
            border: 1px solid #ddd;
            padding: 5px;
            border-radius: 5px;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

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
                        <a href="{{ route('product.index') }}" class="nav-link text-white">
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
                        <a href="{{ route('protype.add') }}" class="nav-link text-white">
                            <i class="nav-icon fas fa-chart-bar"></i>
                            <p>ประเภทสินค้า</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <div class="content-wrapper p-3">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-cart-plus"></i> เพิ่มข้อมูลสินค้า</h3>
            </div>

            <form action="{{ route('product.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">

                    {{--  ✅ 1. จัดกลุ่ม Input ชื่อสินค้า --}}
                    <div class="form-group">
                        <label for="pro_name">ชื่อสินค้า</label>
                        <input type="text" id="pro_name" name="pro_name" class="form-control @error('pro_name') is-invalid @enderror" placeholder="กรอกชื่อสินค้า" required value="{{ old('pro_name') }}">
                        @error('pro_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{--  ✅ 2. จัดกลุ่ม Radio Button ประเภทสินค้า (ลบ <br> ออก) --}}
                    <div class="form-group">
                        <label>ประเภทสินค้า</label>
                        <div class="mt-2">
                            @foreach($types as $type)
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="type_id" id="type_{{ $type->type_id }}" value="{{ $type->type_id }}" required>
                                <label class="form-check-label" for="type_{{ $type->type_id }}">{{ $type->type_name }}</label>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{--  ✅ 3. จัดกลุ่ม Input ราคา --}}
                    <div class="form-group">
                        <label for="price">ราคา</label>
                        <input type="number" id="price" name="price" class="form-control" placeholder="ราคาสินค้า" required>
                    </div>

                    {{-- ✅ 4. ปรับปรุง Input รูปภาพให้สวยงาม --}}
                    <div class="form-group">
                        <label for="image">รูปสินค้า</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="image" name="image" accept="image/*">
                            <label class="custom-file-label" for="image">เลือกไฟล์...</label>
                        </div>
                    </div>
                    
                    {{-- ✅ 5. เพิ่มส่วนแสดงตัวอย่างรูปภาพ --}}
                    <div id="image-preview-container" class="text-center">
                        <img id="image-preview" src="#" alt="Image Preview"/>
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

    <footer class="main-footer bg-secondary text-center py-2">
        <strong>&copy; {{ date('Y') }} ร้านริมหนอง คาเฟ่.</strong> กินกาแฟให้อร่อย
    </footer>

</div>

<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    // ✅ 6. Script สำหรับ Custom File Input และ Image Preview
    bsCustomFileInput.init(); // ทำให้ custom file input แสดงชื่อไฟล์

    $('#image').change(function(){
        // แสดง Container ของรูปภาพ
        $('#image-preview-container').show();

        // อ่านไฟล์และแสดงผล
        const reader = new FileReader();
        reader.onload = function (e) {
            $('#image-preview').attr('src', e.target.result);
        }
        reader.readAsDataURL(this.files[0]);
    });
});
</script>
{{-- AdminLTE ต้องการ plugin นี้สำหรับ custom file input --}}
<script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>

</body>
</html>