<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>เพิ่มวัสดุคงคลัง</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
    
    <style>
        .image-preview {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border-radius: 5px;
            border: 1px solid #ddd;
            margin-top: 15px;
            display: none; 
        }
    </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  
  @include('layouts.assets._navbar')
   @include('layouts.assets._sidebar')

    <div class="content-wrapper p-3">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-box"></i> เพิ่มวัสดุคงคลัง</h3>
            </div>

            <form action="{{ route('stock.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">

                    {{-- (ส่วนแสดงผล Error ... เหมือนเดิม) --}}
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
                        <label>จำนวนที่นำเข้า</label>
                        {{-- [แก้ไข] 1. ลบ required, เพิ่ม max, min และค่าเริ่มต้น 0 --}}
                        <input type="number" name="quantity" class="form-control" value="{{ old('quantity', 0) }}" min="0" max="999999">
                    </div>

                    <div class="form-group">
                        <label>วันหมดอายุ</label>
                        <input type="date" name="exp_date" class="form-control" value="{{ old('exp_date') }}">
                    </div>

                    <div class="form-group">
                        <label>ราคาต่อหน่วย</label>
                        <input type="number" step="0.01" name="unitcost" class="form-control" value="{{ old('unitcost') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="image_upload">รูปภาพวัสดุ (รองรับ .jpg, .png, ไม่เกิน 3MB)</label>
                        <input type="file" name="image_upload" id="image_upload" class="form-control-file" accept=".jpg, .jpeg, .png">
                        <img id="image_preview" src="#" alt="Image Preview" class="image-preview" />
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

{{-- [แก้ไข] 2. JavaScript สำหรับ Preview รูป (เพิ่มเช็คขนาดไฟล์) --}}
<script>
    document.getElementById('image_upload').addEventListener('change', function(event) {
        var preview = document.getElementById('image_preview');
        var file = event.target.files[0];
        var maxSize = 3 * 1024 * 1024; // 3MB (คิดเป็น bytes)

        if (file) {
            // 1. ตรวจสอบขนาดไฟล์
            if (file.size > maxSize) {
                // ถ้าไฟล์ใหญ่เกินไป:
                alert('ขนาดรูปภาพต้องไม่เกิน 3MB ครับ');
                event.target.value = ''; // 👈 ล้างไฟล์ที่เลือก
                preview.src = '#';
                preview.style.display = 'none'; // 👈 ซ่อนรูป
                return; // 👈 หยุดทำงาน
            }

            // 2. ถ้าขนาดไฟล์โอเค:
            var reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block'; 
            }
            
            reader.readAsDataURL(file);
        } else {
            preview.src = '#';
            preview.style.display = 'none';
        }
    });
</script>

</body>
</html>