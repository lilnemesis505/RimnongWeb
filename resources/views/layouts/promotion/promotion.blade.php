<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Promotion Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    {{-- (Navbar และ Sidebar ... เหมือนเดิม) --}}
    @include('layouts.assets._navbar')
    {{-- Sidebar --}}
    @include('layouts.assets._sidebar')

    <div class="content-wrapper p-3" style="min-height: 100vh;">
        <div class="card">
            <div class="card-header"><h3 class="card-title">ข้อมูลโปรโมชั่น</h3></div>
            <div class="card-body p-0">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ชื่อโปรโมชั่น</th>
                            <th>สินค้าที่ร่วมรายการ</th>
                            <th>ส่วนลด (บาท)</th>
                            <th>วันที่เริ่ม</th>
                            <th>วันที่สิ้นสุด</th>
                            <th>สถานะ</th> 
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($promotions as $promotion)
                        <tr>
                            <td>{{ $loop->iteration + ($promotions->currentPage() - 1) * $promotions->perPage() }}</td>
                            <td>{{ $promotion->promo_name }}</td>
                            <td>{{ $promotion->product->pro_name ?? 'N/A' }}</td>
                            <td>{{ number_format($promotion->promo_discount, 2) }}</td>
                            <td>{{ $promotion->promo_start->format('m/d/Y') }}</td>
                            <td>{{ $promotion->promo_end->format('m/d/Y') }}</td>
                            
                            <td>
                                <span class="badge {{ $promotion->status_class }}">
                                    {{ $promotion->status_text }}
                                </span>
                            </td>
                            
                            {{-- [แก้ไข] 1. เปลี่ยนจาก <a> เป็น <button> --}}
                            <td>
                                <button type="button" class="btn btn-sm btn-warning edit-promo-btn"
                                        {{-- 👈 ใช้ data- attribute เก็บข้อมูล --}}
                                        data-orders-count="{{ $promotion->orders_count }}" 
                                        data-edit-url="{{ route('promotion.edit', $promotion->promo_id) }}">
                                    <i class="fas fa-edit"></i> แก้ไข
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer clearfix">{{ $promotions->links() }}</div>
        </div>
    </div>
</div>

{{-- [เพิ่ม] 2. HTML ของ Modal (หน้าต่างยืนยัน) --}}
<div class="modal fade" id="editConfirmModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">ยืนยันการแก้ไข</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{-- (ข้อความนี้จะถูกเปลี่ยนโดย JavaScript) --}}
                <p id="modal-message">คุณแน่ใจหรือไม่ว่าต้องการแก้ไขโปรโมชั่นนี้?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
                {{-- (ปุ่มนี้จะเป็นลิงก์ไปหน้า Edit) --}}
                <a href="#" id="modal-confirm-edit-btn" class="btn btn-warning">ยืนยันการแก้ไข</a>
            </div>
        </div>
    </div>
</div>

{{-- [เพิ่ม] 3. JavaScript (jQuery, Bootstrap) --}}
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

{{-- [เพิ่ม] 4. JavaScript สำหรับควบคุม Modal --}}
<script>
$(document).ready(function() {
    // 1. ดึง Element ของ Modal
    var modal = $('#editConfirmModal');
    var modalMessage = $('#modal-message');
    var confirmBtn = $('#modal-confirm-edit-btn');

    // 2. เมื่อปุ่ม 'แก้ไข' (คลาส .edit-promo-btn) ถูกคลิก
    $('.edit-promo-btn').on('click', function() {
        var btn = $(this);
        // 3. อ่านค่าจาก data- attribute ที่เราตั้งไว้
        var ordersCount = parseInt(btn.data('orders-count'));
        var editUrl = btn.data('edit-url');

        // 4. ตั้งค่า URL ให้ปุ่ม "ยืนยัน" ใน Modal *ทุกครั้ง*
        confirmBtn.attr('href', editUrl);

        // 5. ตรวจสอบว่ามี Order หรือไม่
        if (ordersCount > 0) {
            // 5a. ถ้ามี: แสดง Modal พร้อมคำเตือน
            modalMessage.html('โปรโมชั่นนี้มีการถูกสั่งซื้อไปแล้ว <strong>' + ordersCount + ' ครั้ง</strong><br>คุณยืนยันที่จะแก้ไขหรือไม่?');
            modal.modal('show');
        } else {
            // 5b. ถ้าไม่มี: ไปหน้า Edit เลย (ไม่ต้องแสดง Modal)
            window.location.href = editUrl;
        }
    });
});
</script>
</body>
</html>