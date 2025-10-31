<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>เพิ่มโปรโมชั่น</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    {{-- Navbar and Sidebar --}}
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <span class="navbar-brand">เพิ่มข้อมูลโปรโมชั่น</span>
    </nav>
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="#" class="brand-link">
            <span class="brand-text font-weight-light">{{ session('admin_fullname') }}</span>
        </a>
        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column">
                    <li class="nav-item">
                        <a href="{{ route('welcome') }}" class="nav-link text-white"><i class="nav-icon fas fa-home-alt"></i> <p>หน้าหลัก</p></a>
                    </li>
                </ul>
                <hr class="bg-white">
                <ul class="nav nav-pills nav-sidebar flex-column">
                    <li class="nav-item">
                        <a href="{{ route('promotion.index') }}" class="nav-link text-white"><i class="nav-icon fas fa-ticket"></i> <p>ข้อมูลโปรโมชั่น</p></a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('promotion.add') }}" class="nav-link active"><i class="nav-icon fas fa-plus"></i> <p>เพิ่มโปรโมชั่น</p></a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <div class="content-wrapper p-4">
        <h2 class="mb-4">จัดการโปรโมชั่น</h2>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="card mb-4">
            <div class="card-header">เพิ่มโปรโมชั่นใหม่</div>
            <div class="card-body">
                <form action="{{ route('promotion.store') }}" method="POST" id="promotion-form" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label for="promo_name" class="form-label">ชื่อโปรโมชั่น</label>
                        <input type="text" name="promo_name" class="form-control" required value="{{ old('promo_name') }}">
                    </div>
                    
                    <div class="mb-3">
                        <label for="pro_id" class="form-label">สินค้าที่ร่วมรายการ</label>
                        <select name="pro_id" id="product_select" class="form-control" required>
                            <option value="">-- กรุณาเลือกสินค้า --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->pro_id }}" {{ old('pro_id') == $product->pro_id ? 'selected' : '' }}>
                                    {{ $product->pro_name }}
                                </option>
                            @endforeach
                        </select>
                        <small id="product_price_display" class="form-text text-info" style="display: none;"></small>
                    </div>

                    <div class="mb-3">
                        <label for="promo_discount" class="form-label">ราคาที่ลด (บาท)</label>
                        <input type="number" step="0.01" name="promo_discount" id="promo_discount" class="form-control" required value="{{ old('promo_discount', 0) }}" min="0">
                        <small id="discount-error" class="text-danger" style="display: none;"></small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="promo_start" class="form-label">วันที่เริ่ม</label>
                        {{-- [แก้ไข] 1. เพิ่ม ID ให้ช่องวันที่ --}}
                        <input type="date" name="promo_start" id="promo_start" class="form-control" required value="{{ old('promo_start') }}">
                    </div>
                    <div class="mb-3">
                        <label for="promo_end" class="form-label">วันที่สิ้นสุด</label>
                         {{-- [แก้ไข] 2. เพิ่ม ID ให้ช่องวันที่ --}}
                        <input type="date" name="promo_end" id="promo_end" class="form-control" required value="{{ old('promo_end') }}">
                    </div>
                    <button type="submit" class="btn btn-primary">เพิ่มโปรโมชั่น</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

<?php
    $productsForJs = $products->keyBy('pro_id');
?>

{{-- [แก้ไข] 3. เพิ่ม Logic คุมวันที่ (validateDates) เข้าไปใน Script --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // (ดึง Element ... เหมือนเดิม)
        const form = document.getElementById('promotion-form');
        const productSelect = document.getElementById('product_select');
        const discountInput = document.getElementById('promo_discount');
        const priceDisplay = document.getElementById('product_price_display');
        const discountError = document.getElementById('discount-error'); 
        const productsData = @json($productsForJs);
        let currentMaxPrice = 0; 
        
        // [เพิ่ม] 3.1 ดึง Element วันที่
        const startDateInput = document.getElementById('promo_start');
        const endDateInput = document.getElementById('promo_end');

        // (ฟังก์ชันอัปเดตราคา ... เหมือนเดิม)
        function updateProductInfo() {
            const selectedId = productSelect.value;
            discountError.style.display = 'none'; 

            if (selectedId && productsData[selectedId]) {
                const product = productsData[selectedId];
                const price = parseFloat(product.price);
                currentMaxPrice = price; 
                priceDisplay.textContent = 'ราคาสินค้า: ' + price.toFixed(2) + ' บาท';
                priceDisplay.style.display = 'block';
                discountInput.max = price; 
                
                if (parseFloat(discountInput.value) > price) {
                    discountInput.value = price; 
                }
            } else {
                priceDisplay.style.display = 'none';
                discountInput.max = null;
                currentMaxPrice = 0;
            }
        }
        productSelect.addEventListener('change', updateProductInfo);
        updateProductInfo();

        // [เพิ่ม] 3.2 ฟังก์ชันคุมวันที่
        function validateDates() {
            const startDate = startDateInput.value;
            const endDate = endDateInput.value;

            if (startDate) {
                // วันที่สิ้นสุด (endDateInput) ห้ามเริ่ม "ก่อน" วันที่เริ่ม
                endDateInput.min = startDate; 
            }
            if (endDate) {
                // วันที่เริ่ม (startDateInput) ห้ามเริ่ม "หลัง" วันที่สิ้นสุด
                startDateInput.max = endDate; 
            }
        }
        // [เพิ่ม] 3.3 สั่งให้ฟังก์ชันคุมวันที่ทำงาน
        startDateInput.addEventListener('change', validateDates);
        endDateInput.addEventListener('change', validateDates);
        validateDates(); // 👈 สั่งทำงาน 1 ครั้งตอนโหลดหน้า

        // (ฟังก์ชันเช็ค Error ตอน Submit ... เหมือนเดิม)
        form.addEventListener('submit', function(event) {
            const discountValue = parseFloat(discountInput.value);

            if (productSelect.value === "") {
                event.preventDefault(); 
                alert('กรุณาเลือกสินค้าที่ร่วมรายการก่อนครับ');
                return;
            }

            if (discountInput.value === "") {
                 event.preventDefault(); 
                 discountError.textContent = 'กรุณากรอกส่วนลด';
                 discountError.style.display = 'block';
                 return;
            }

            if (currentMaxPrice > 0 && discountValue > currentMaxPrice) {
                event.preventDefault(); 
                discountError.textContent = 'ส่วนลดต้องไม่เกินราคาสินค้า (' + currentMaxPrice.toFixed(2) + ' บาท)';
                discountError.style.display = 'block';
            
            } else if (discountValue < 0) {
                event.preventDefault(); 
                discountError.textContent = 'ส่วนลดต้องไม่ต่ำกว่า 0 บาท';
                discountError.style.display = 'block';
            
            } else {
                discountError.style.display = 'none';
            }
        });
    });
</script>
</body>
</html>