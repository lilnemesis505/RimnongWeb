<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขโปรโมชั่น</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    {{-- (Navbar และ Sidebar ... เหมือนเดิม) --}}
   @include('layouts.assets._navbar')
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
                        <a href="" class="nav-link  active" style="background-color:#007bff; color: #fff;">
                     <i class="nav-icon fas fa-gear"></i> <p>แก้ไขข้อมูลโปรโมชั่น</p>
                        </a>
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
            <div class="card-header">แก้ไขโปรโมชั่น</div>
            <div class="card-body">
                
                <form action="{{ route('promotion.update', $promotion->promo_id) }}" method="POST" id="promotion-form" novalidate>
                    @csrf
                    @method('PUT') 
                    
                    <div class="mb-3">
                        <label for="promo_name" class="form-label">ชื่อโปรโมชั่น</label>
                        <input type="text" name="promo_name" class="form-control" required value="{{ old('promo_name', $promotion->promo_name) }}">
                    </div>
                    
                    {{-- [แก้ไข] 👈 ลบ Logic 'disabled' ออก --}}
                    <div class="mb-3">
                        <label for="pro_id" class="form-label">สินค้าที่ร่วมรายการ</label>
                        <select name="pro_id" id="product_select" class="form-control" required 
                                @if($promotion->orders_count > 0) readonly disabled @endif>
                            
                            <option value="">-- กรุณาเลือกสินค้า --</option>
                            @foreach($products as $product)
                                {{-- 1. [ลบ] ลบ @php และ $isPromoted ออก --}}
                                
                                <option value="{{ $product->pro_id }}" 
                                    {{ old('pro_id', $promotion->pro_id) == $product->pro_id ? 'selected' : '' }}
                                    {{-- 2. [ลบ] ลบ 'disabled' และข้อความ '(มีโปรโมชั่นแล้ว)' ออก --}}
                                    >
                                    
                                    {{ $product->pro_name }}
                                </option>
                            @endforeach
                        </select>
                        <small id="product_price_display" class="form-text text-info" style="display: none;"></small>
                        
                        @if($promotion->orders_count > 0)
                            <small class="form-text text-danger">
                                <i class="fas fa-lock"></i> ไม่สามารถเปลี่ยนสินค้าได้ เนื่องจากโปรโมชั่นนี้มีการซื้อขายแล้ว
                            </small>
                        @endif
                    </div>

                    {{-- (Logic ล็อคช่องส่วนลด ... เหมือนเดิม) --}}
                    <div class="mb-3">
                        <label for="promo_discount" class="form-label">ราคาที่ลด (บาท)</label>
                        
                        @if($promotion->orders_count > 0)
                            <input type="number" step="0.01" name="promo_discount" id="promo_discount" class="form-control" 
                                   value="{{ $promotion->promo_discount }}" readonly>
                            <small class="form-text text-danger">
                                <i class="fas fa-lock"></i> โปรโมชั่นนี้มีการซื้อขายแล้ว จึงไม่สามารถแก้ไขราคาส่วนลดได้
                            </small>
                        @else
                            <input type="number" step="0.01" name="promo_discount" id="promo_discount" class="form-control" 
                                   required value="{{ old('promo_discount', $promotion->promo_discount) }}" min="0">
                            <small id="discount-error" class="text-danger" style="display: none;"></small>
                        @endif
                    </div>
                    
                    {{-- (ช่องวันที่ ... เหมือนเดิม) --}}
                    <div class="mb-3">
                        <label for="promo_start" class="form-label">วันที่เริ่ม</label>
                        <input type="date" name="promo_start" id="promo_start" class="form-control" required value="{{ old('promo_start', $promotion->promo_start) }}">
                    </div>
                    <div class="mb-3">
                        <label for="promo_end" class="form-label">วันที่สิ้นสุด</label>
                        <input type="date" name="promo_end" id="promo_end" class="form-control" required value="{{ old('promo_end', $promotion->promo_end) }}">
                    </div>
                    
                    <button type="submit" class="btn btn-warning">บันทึกการแก้ไข</button>
                    <a href="{{ route('promotion.index') }}" class="btn btn-secondary">ยกเลิก</a>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

<?php $productsForJs = $products->keyBy('pro_id'); ?>

{{-- (JavaScript ทั้งหมด ... เหมือนเดิม) --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // (โค้ด JS ทั้งหมดของคุณถูกต้องอยู่แล้ว)
        // ... (function updateProductInfo)
        // ... (function validateDates)
        // ... (form.addEventListener('submit'))
        // ...
        const form = document.getElementById('promotion-form');
        const productSelect = document.getElementById('product_select');
        const discountInput = document.getElementById('promo_discount');
        const priceDisplay = document.getElementById('product_price_display');
        const discountError = document.getElementById('discount-error');
        const productsData = @json($productsForJs);
        let currentMaxPrice = 0; 
        
        const startDateInput = document.getElementById('promo_start');
        const endDateInput = document.getElementById('promo_end');

        // [ฟังก์ชัน 1: อัปเดตราคา]
        function updateProductInfo() {
            const selectedId = productSelect.value;
            if(discountError) { 
                discountError.style.display = 'none'; 
            }

            if (selectedId && productsData[selectedId]) {
                const product = productsData[selectedId];
                const price = parseFloat(product.price);
                currentMaxPrice = price; 
                priceDisplay.textContent = 'ราคาสินค้า: ' + price.toFixed(2) + ' บาท';
                priceDisplay.style.display = 'block';
                
                if (discountInput) { 
                    discountInput.max = price; 
                    if (parseFloat(discountInput.value) > price) {
                        discountInput.value = price; 
                    }
                }
            } else {
                priceDisplay.style.display = 'none';
                if (discountInput) {
                    discountInput.max = null;
                }
                currentMaxPrice = 0;
            }
        }
        productSelect.addEventListener('change', updateProductInfo);
        updateProductInfo(); 
        
        // [ฟังก์ชัน 2: คุมวันที่]
        function validateDates() {
            const startDate = startDateInput.value;
            const endDate = endDateInput.value;

            if (startDate) {
                endDateInput.min = startDate; 
            }
            if (endDate) {
                startDateInput.max = endDate; 
            }
        }
        startDateInput.addEventListener('change', validateDates);
        endDateInput.addEventListener('change', validateDates);
        validateDates(); 
        
        // [ฟังก์ชัน 3: เช็ค Error ภาษาไทย]
        form.addEventListener('submit', function(event) {
            
            if (productSelect.value === "") {
                event.preventDefault(); 
                alert('กรุณาเลือกสินค้าที่ร่วมรายการก่อนครับ');
                return;
            }

            if (discountInput.readOnly) {
                return; 
            }

            const discountValue = parseFloat(discountInput.value);

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