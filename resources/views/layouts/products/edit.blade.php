<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขข้อมูลสินค้า</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
    <style>
        /* CSS เพิ่มเติมเพื่อให้รูป preview สวยงาม */
        #image-preview-container {
            margin-top: 15px;
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
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-edit"></i> แก้ไขข้อมูลสินค้า</h3>
            </div>

            <form action="{{ route('product.update', $product->pro_id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-body">

                    <div class="form-group">
                        <label for="pro_name">ชื่อสินค้า</label>
                        <input type="text" id="pro_name" name="pro_name" class="form-control @error('pro_name') is-invalid @enderror" value="{{ $product->pro_name }}" required>
                        @error('pro_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>ประเภทสินค้า</label>
                        <div class="mt-2">
                            @foreach($types as $type)
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="type_id" id="type_{{ $type->type_id }}" value="{{ $type->type_id }}"
                                    {{ $product->type_id == $type->type_id ? 'checked' : '' }} required>
                                <label class="form-check-label" for="type_{{ $type->type_id }}">{{ $type->type_name }}</label>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="price">ราคา</label>
                        <input type="number" id="price" name="price" class="form-control" value="{{ $product->price }}" required>
                    </div>

                    <div class="form-group">
                        <label for="image">เลือกรูปใหม่ (ถ้าต้องการเปลี่ยน)</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="image" name="image" accept="image/*">
                            <label class="custom-file-label" for="image">เลือกไฟล์...</label>
                        </div>
                    </div>
                    
                    <div id="image-preview-container" class="text-center">
                        <label class="d-block">รูปปัจจุบัน:</label>
                        {{-- แสดงรูปปัจจุบัน ถ้ามี --}}
                        <img id="image-preview" 
                             src="{{ $product->image ? $product->image : 'https://via.placeholder.com/250x250.png?text=No+Image' }}" 
                             alt="Image Preview"/>
                    </div>

                </div>

                <div class="card-footer d-flex justify-content-between">
                    <div>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save"></i> บันทึกการแก้ไข
                        </button>
                    </div>
                    <div>
                        {{-- แยกปุ่มลบออกมาอยู่คนละ form เพื่อความปลอดภัย --}}
                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal">
                            <i class="fas fa-trash-alt"></i> ลบข้อมูล
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">ยืนยันการลบสินค้า</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                คุณแน่ใจหรือไม่ว่าต้องการลบสินค้า **"{{ $product->pro_name }}"** ?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
                <form action="{{ route('product.destroy', $product->pro_id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">ยืนยันการลบ</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap/dist/js/bootstrap.bundle.min.js"></script> {{-- Bootstrap JS สำหรับ Modal --}}
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>
<script>
$(document).ready(function () {
    bsCustomFileInput.init();

    $('#image').change(function(){
        const reader = new FileReader();
        reader.onload = function (e) {
            $('#image-preview').attr('src', e.target.result);
        }
        reader.readAsDataURL(this.files[0]);
    });
});
</script>

</body>
</html>