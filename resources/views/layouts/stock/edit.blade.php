<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขวัสดุคงคลัง</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
    <style>
        .current-image {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .readonly-field {
            background-color: #e9ecef;
            font-weight: bold;
            color: #495057;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

    {{-- (Navbar และ Sidebar ... เหมือนเดิม) --}}
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <span class="navbar-brand">ข้อมูลปรับปรุงล็อตสินค้า</span>
    </nav>
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="#" class="brand-link">
            <span class="brand-text font-weight-light">{{ session('admin_fullname') }}</span>
        </a>
        <div class="sidebar">
            <nav class="mt="2">
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

    <div class="content-wrapper p-3">
    <div class="card card-warning">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-edit"></i> แก้ไขข้อมูลล็อตวัตถุดิบ</h3>
        </div>

        <form action="{{ route('stock.update', $mat->mat_id) }}" method="POST" enctype="multipart/form-data" id="stock-edit-form">
            @csrf
            @method('PUT')

            <div class="card-body">
                
                {{-- (ส่วน Error ... เหมือนเดิม) --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
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
                
                {{-- (ข้อมูลหลัก ... เหมือนเดิม) --}}
                <div class="form-group">
                    <label>ชื่อวัสดุ</label>
                    <input type="text" name="mat_name" class="form-control" value="{{ old('mat_name', $mat->mat_name) }}" required>
                </div>
                <div class="form-group">
                    <label>ประเภทวัสดุ</label>
                    <select name="type_id" class="form-control" required>
                        @foreach($types as $type)
                            <option value="{{ $type->type_id }}" {{ (old('type_id', $mat->type_id) == $type->type_id) ? 'selected' : '' }}>
                                {{ $type->type_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>วันหมดอายุ</label>
                    <input type="date" name="exp_date" class="form-control" value="{{ old('exp_date', $mat->exp_date ? \Carbon\Carbon::parse($mat->exp_date)->format('Y-m-d') : '') }}">
                </div>
                <hr>
                <div class="form-group">
                    <label>รูปภาพปัจจุบัน</label><br>
                    @if($mat->image)
                        <img src="{{ $mat->image }}?tr=w-150,h-150,fo-auto" alt="{{ $mat->mat_name }}" class="current-image mb-2">
                    @else
                        <p class="text-muted">ไม่มีรูปภาพ</p>
                    @endif
                </div>
                <div class="form-group">
                    {{-- [แก้ไข] 2. เพิ่มข้อความ (ไม่เกิน 3MB) --}}
                    <label for="image_upload">เปลี่ยนรูปภาพ (รองรับ .jpg, .png, ไม่เกิน 3MB)</label>
                    <input type="file" name="image_upload" id="image_upload" class="form-control-file" accept=".jpg, .jpeg, .png">
                </div>
                <hr>
                
                <h4><i class="fas fa-boxes"></i> จัดการสต็อก</h4>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>จำนวนคงเหลือ (ปัจจุบัน)</label>
                            <input type="number" class="form-control readonly-field" 
                                   value="{{ $mat->remain }}" readonly id="current_remain">
                            <small class="form-text text-muted">ยอดในระบบ (อัตโนมัติ)</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>รับเข้าสต็อกเพิ่ม (นำเข้า)</label>
                            {{-- [แก้ไข] 1. เพิ่ม max="999999" --}}
                            <input type="number" name="add_stock" class="form-control" 
                                   value="{{ old('add_stock', 0) }}" min="0" max="999999"> 
                            <small class="form-text text-success">กรอกยอดที่สั่งซื้อมาใหม่ (ไม่บังคับกรอก)</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>ปรับยอด (แก้ไข)</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <select name="adjustment_type" id="adjustment_type" class="form-control" style="border-radius: 0.25rem 0 0 0.25rem;">
                                        <option value="add" {{ old('adjustment_type') == 'add' ? 'selected' : '' }}>ปรับขึ้น (+)</option>
                                        <option value="subtract" {{ old('adjustment_type') == 'subtract' ? 'selected' : '' }}>ปรับลด (-)</option>
                                    </select>
                                </div>
                                <input type="number" name="adjustment_amount" id="adjustment_amount" class="form-control" 
                                       value="{{ old('adjustment_amount', 0) }}" min="0" required>
                            </div>
                            <small class="form-text text-info">ปรับยอดที่นับได้ถ้าไม่ตรงกับหน้าร้าน</small>
                            {{-- (ช่องแจ้งเตือน JavaScript) --}}
                            <small id="adjustment-error" class="text-danger" style="display: none;"></small>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <div class="form-group">
                    <label>ราคาต่อหน่วย</label>
                    <input type="number" step="0.01" name="unitcost" class="form-control" value="{{ old('unitcost', $mat->unitcost) }}" required>
                </div>

                {{-- (ส่วน Status ... เหมือนเดิม) --}}
                <div class="form-group">
                    <label>สถานะปัจจุบัน</label><br>
                    @if($mat->status == 2)
                        <span class="badge badge-warning" style="font-size: 1rem;">รอของเข้า</span>
                    @elseif($mat->remain > 0)
                        <span class="badge badge-success" style="font-size: 1rem;">ปกติ</span>
                    @else
                        <span class="badge badge-danger" style="font-size: 1rem;">หมด</span>
                    @endif
                </div>
                <div class="form-group">
                    <label>จัดการสถานะ</label><br>
                    <input type="hidden" name="status" value="0"> 
                    <div class="form-check">
                        <input type="checkbox" name="status" id="status_2" value="2" 
                               {{ old('status', $mat->status) == 2 ? 'checked' : '' }} 
                               @if($mat->remain >= 3 && $mat->status != 2) disabled @endif
                               class="form-check-input">
                        <label class="form-check-label" for="status_2">
                            สั่งสินค้าแล้ว (รอของเข้า)
                        </label>
                        
                        @if($mat->remain >= 3 && $mat->status != 2)
                            <small class="form-text text-danger">
                                (ไม่สามารถสั่งได้เนื่องจากสินค้ายังมี 3 ชิ้นขึ้นไป)
                            </small>
                        @else
                             <small class="form-text text-muted">
                                (ติ๊กช่องนี้เมื่อของหมด/เหลือน้อย และสั่งไปแล้ว / นำติ๊กออกเมื่อของมาส่ง)
                            </small>
                        @endif
                    </div>
                </div>
            </div> {{-- ปิด card-body --}}

            <div class="card-footer d-flex justify-content-between">
                <div>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save"></i> บันทึกการแก้ไข
                    </button>
                </div>
        </form> {{-- (ปิดฟอร์มหลัก) --}}
        
        {{-- (ฟอร์มลบ ... เหมือนเดิม) --}}
        <form action="{{ route('stock.destroy', $mat->mat_id) }}" method="POST"
              onsubmit="return confirm('คุณแน่ใจว่าต้องการลบข้อมูลนี้หรือไม่?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash-alt"></i> ลบข้อมูล
            </button>
        </form>
            </div> {{-- ปิด card-footer --}}

    </div> {{-- ปิด card --}}
</div> {{-- ปิด content-wrapper --}}
</div> {{-- ปิด wrapper --}}

<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

{{-- [เพิ่ม] 3. JavaScript สำหรับเช็ครูปภาพ และเช็คสต็อกติดลบ --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // --- 1. ตรวจสอบขนาดไฟล์รูปภาพ ---
        document.getElementById('image_upload').addEventListener('change', function(event) {
            var file = event.target.files[0];
            var maxSize = 3 * 1024 * 1024; // 3MB (คิดเป็น bytes)

            if (file && file.size > maxSize) {
                alert('ขนาดรูปภาพต้องไม่เกิน 3MB ครับ');
                event.target.value = ''; // 👈 ล้างไฟล์ที่เลือก
            }
        });

        // --- 2. ตรวจสอบสต็อกติดลบ (ฝั่ง Client) ---
        var form = document.getElementById('stock-edit-form');
        var adjustmentType = document.getElementById('adjustment_type');
        var adjustmentAmount = document.getElementById('adjustment_amount');
        var adjustmentError = document.getElementById('adjustment-error');
        // 👈 ดึงยอดคงเหลือปัจจุบันจากช่อง readonly
        var currentRemain = parseInt(document.getElementById('current_remain').value); 

        form.addEventListener('submit', function(event) {
            var amountToAdjust = parseInt(adjustmentAmount.value);
            
            // ตรวจสอบเฉพาะเมื่อเลือก "ปรับลด"
            if (adjustmentType.value === 'subtract') {
                if (amountToAdjust > currentRemain) {
                    event.preventDefault(); // 👈 หยุดการส่งฟอร์ม
                    adjustmentError.textContent = 'ไม่สามารถปรับลดได้ ยอดคงเหลือปัจจุบันคือ ' + currentRemain;
                    adjustmentError.style.display = 'block';
                } else {
                    adjustmentError.style.display = 'none';
                }
            } else {
                adjustmentError.style.display = 'none';
            }
        });
    });
</script>
</body>
</html>