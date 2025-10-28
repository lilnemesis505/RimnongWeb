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
    </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <span class="navbar-brand">ข้อมูลปรับปรุงล็อตสินค้า</span>
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

        {{-- ✅ [FIX] 1. เพิ่ม enctype="multipart/form-data" --}}
        <form action="{{ route('stock.update', $mat->mat_id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="card-body">

                {{-- ✅ [FIX] 2. เปลี่ยน input ที่ readonly ให้ส่งค่าได้ --}}
                <div class="form-group">
                    <label>ชื่อวัสดุ</label>
                    <input type="text" name="mat_name" class="form-control" value="{{ old('mat_name', $mat->mat_name) }}" required>
                </div>

                <div class="form-group">
                    <label>ประเภทวัสดุ</label>
                    {{-- (Controller ส่ง $types มาให้แล้ว) --}}
                    <select name="type_id" class="form-control" required>
                        @foreach($types as $type)
                            <option value="{{ $type->type_id }}" {{ $mat->type_id == $type->type_id ? 'selected' : '' }}>
                                {{ $type->type_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>วันที่นำเข้า</label>
                    {{-- (เปลี่ยนเป็น input type="date" เพื่อให้ส่งค่าได้) --}}
                    <input type="date" name="import_date" class="form-control" value="{{ old('import_date', \Carbon\Carbon::parse($mat->import_date)->format('Y-m-d')) }}" required>
                </div>

                <div class="form-group">
                    <label>วันหมดอายุ</label>
                     {{-- (เปลี่ยนเป็น input type="date") --}}
                    <input type="date" name="exp_date" class="form-control" value="{{ old('exp_date', $mat->exp_date ? \Carbon\Carbon::parse($mat->exp_date)->format('Y-m-d') : '') }}">
                </div>

                <hr>

                {{-- ✅ [เพิ่ม] 3. ส่วนแสดง/แก้ไขรูปภาพ --}}
                <div class="form-group">
                    <label>รูปภาพปัจจุบัน</label><br>
                    @if($mat->image)
                        <img src="{{ $mat->image }}?tr=w-150,h-150,fo-auto" alt="{{ $mat->mat_name }}" class="current-image mb-2">
                    @else
                        <p class="text-muted">ไม่มีรูปภาพ</p>
                    @endif
                </div>

                <div class="form-group">
                    <label for="image_upload">เปลี่ยนรูปภาพ (รองรับ .jpg, .png)</label>
                    <input type="file" name="image_upload" id="image_upload" class="form-control-file" accept=".jpg, .jpeg, .png">
                    <small class="form-text text-muted">หากไม่ต้องการเปลี่ยนรูปภาพ ให้เว้นว่างไว้</small>
                </div>

                <hr>

                {{-- (ส่วนที่เหลือของคุณ ถูกต้องแล้ว) --}}
                <div class="form-group">
                    <label>จำนวนที่นำเข้า</label>
                    <input type="number" name="quantity" class="form-control" value="{{ old('quantity', $mat->quantity) }}" required>
                </div>

                <div class="form-group">
                    <label>จำนวนคงเหลือ</label>
                    <input type="number" name="remain" class="form-control" value="{{ old('remain', $mat->remain) }}" required>
                </div>

                <div class="form-group">
                    <label>ราคาต่อหน่วย</label>
                    <input type="number" step="0.01" name="unitcost" class="form-control" value="{{ old('unitcost', $mat->unitcost) }}" required>
                </div>

                <div class="form-group">
                    <label>สถานะ</label><br>
                    <div class="form-check">
                        <input type="radio" name="status" value="0" {{ old('status', $mat->status) == 0 ? 'checked' : '' }}>
                        <label class="form-check-label">ปกติ</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" name="status" value="1" {{ old('status', $mat->status) == 1 ? 'checked' : '' }}>
                        <label class="form-check-label">หมด และยังไม่ได้สั่ง</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" name="status" value="2" {{ old('status', $mat->status) == 2 ? 'checked' : '' }}>
                        <label class="form-check-label">หมด และสั่งซื้อแต่ยังไม่ได้รับ</label>
                    </div>
                </div>

            </div>

            <div class="card-footer d-flex justify-content-end gap-2">
                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-save"></i> บันทึกการแก้ไข
                </button>
        </form> {{-- </form> ของ 'update' ต้องปิดที่นี่ --}}

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