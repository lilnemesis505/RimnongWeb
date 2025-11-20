<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ใบเสร็จรับเงิน #{{ $receipt->re_id ?? 'ไม่ระบุ' }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
    <style>
        .receipt-container {
            max-width: 400px;
            margin: 20px auto;
            border: 1px dashed #000; /* ⭐️ (Goal 2) เปลี่ยนเป็นสีดำ */
            padding: 20px;
            font-family: 'Courier New', Courier, monospace;
            background-color: #fff;
            color: #000 !important; /* ⭐️ (Goal 2) บังคับตัวอักษรเป็นสีดำทั้งหมด */
        }
        .receipt-header, .receipt-footer {
            text-align: center;
        }
        .item-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        
        /* ⭐️ (Goal 1) เพิ่ม CSS นี้เพื่อให้ราคาทั้งหมดจัดชิดขวา */
        .item-row span:last-child {
            min-width: 100px; /* กำหนดความกว้างขั้นต่ำ (ปรับได้) */
            text-align: right;
        }

        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body class="hold-transition sidebar-mini">

{{-- ✅ [FIX] เพิ่มส่วนคำนวณ Logic ไว้ด้านบน --}}
@php
    // ราคาสุทธิที่ถูกต้อง (จากตาราง receipt)
    $netTotal = $receipt->price_total ?? $order->details->sum('pay_total');
    
    $originalTotal = 0;
    
    // วนลูปเพื่อหา "ราคาเต็ม"
    foreach ($order->details as $detail) {
        if ($detail->product) {
            $originalTotal += $detail->product->price * $detail->amount;
        } else {
            $originalTotal += $detail->pay_total; // Fallback
        }
    }
    
    // ส่วนลดที่แท้จริง
    $totalDiscount = $originalTotal - $netTotal;
    
    // ⭐️ (ลบ $promoNames) - เราจะวนลูปแทน
@endphp


<div class="wrapper">
    <aside class="main-sidebar sidebar-dark-primary elevation-4 min-vh-100 no-print">
         {{-- ... (Sidebar เหมือนเดิม) ... --}}
        <a href="#" class="brand-link">
            <span class="brand-text font-weight-light">{{ session('admin_fullname') }}</span>
        </a>
        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    <li class="nav-item">
                        <a href="{{ route('welcome') }}" class="nav-link text-white">
                            <i class="nav-icon fas fa-home-alt"></i> <p>หน้าหลัก</p>
                        </a>
                    </li>
                </ul>
                <hr class="bg-white">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    <li class="nav-item">
                        <a href="{{ route('history.index') }}" class="nav-link text-white">
                            <i class="nav-icon fas fa-history"></i> <p>ประวัติการขาย</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('order.details', ['id' => $order->order_id]) }}" class="nav-link text-white">
                            <i class="nav-icon fas fa-dollar-sign"></i> <p>รายละเอียดการขาย</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('order.receipt', ['id' => $order->order_id]) }}" class="nav-link text-white active">
                            <i class="nav-icon fas fa-receipt"></i> <p>ใบเสร็จ</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <div class="content-wrapper">
        <section class="content pt-4">
            <div class="container-fluid">
                <div class="receipt-container">
                    <div class="receipt-header mb-4">
                        {{-- ... (ส่วน Header ของใบเสร็จ เหมือนเดิม) ... --}}
                        <h4 class="mb-0">ริมหนองคาเฟ่</h4>
                        <p class="text-muted mb-0">ใบเสร็จรับเงิน</p>
                        <hr class="my-2">
                        <p><strong>คำสั่งซื้อเลขที่:</strong> {{ $order->order_id ?? 'ไม่ระบุ' }}</p>
                        <p><strong>วันที่:</strong> {{ $order->receive_date ?? 'ไม่ระบุ' }}</p>
                    </div>

                    <div class="receipt-body">
                        @foreach($order->details as $detail)
                            <div class="item-row">
                                <span>{{ $detail->product->pro_name ?? 'ไม่ระบุ' }} (x{{ $detail->amount }})</span>
                                <span>{{ number_format($detail->pay_total, 2) }} บาท</span>
                            </div>
                        @endforeach
                    </div>
                    
                    <hr class="my-2">

                    {{-- ⭐️ (Goal 1) เปลี่ยน class เป็น .item-row --}}
                    <div class="item-row">
                        <span>ราคารวมสินค้า:</span>
                        <span>{{ number_format($originalTotal, 2) }} บาท</span>
                    </div>

                    {{-- ⭐️ (ใหม่) แสดงรายการโปรโมชั่นที่ใช้ --}}
                    @if($order->promotions->isNotEmpty())
                        <div class="mb-1">
                            <span style="font-weight:bold;">โปรโมชั่นที่ใช้:</span>
                            {{-- วนลูปแสดงชื่อโปรโมชั่น --}}
                            
                            {{-- ⭐️ เปลี่ยน ul เป็น div และลบ padding/list-style ออก --}}
                            <div style="margin-bottom: 5px; font-size: 0.95em; margin-top: 5px;">
                                @foreach($order->promotions as $promo)
                                    @php
                                        $discountAmount = 0;
                                        
                                        // 1. ตรวจสอบว่าโปรโมชั่นนี้มี pro_id (เชื่อมโยงกับสินค้า) หรือไม่
                                        if (!empty($promo->pro_id)) {
                                        
                                            // 2. หาสินค้าในออเดอร์ (details) ที่ตรงกับ pro_id ของโปรโมชั่นนี้
                                            $matchingDetail = $order->details->firstWhere('pro_id', $promo->pro_id);
                                            
                                            if ($matchingDetail && $promo->promo_discount) {
                                                // 3. ถ้าเจอ, ส่วนลด = (ส่วนลดต่อชิ้น * จำนวน)
                                                $discountAmount = $promo->promo_discount * $matchingDetail->amount;
                                            }
                                            
                                        } else if ($promo->promo_discount) {
                                            // 4. ถ้าไม่เชื่อมโยงกับสินค้า (pro_id=null)
                                            $discountAmount = $promo->promo_discount;
                                        }
                                    @endphp
                                    
                                    {{-- ⭐️ เปลี่ยน li เป็น div.item-row และแยก span ซ้าย-ขวา --}}
                                    <div class="item-row">
                                        <span>{{ $promo->promo_name }}</span>
                                        @if($discountAmount > 0)
                                            <span>-{{ number_format($discountAmount, 2) }} บาท</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- ⭐️ (Goal 1) เปลี่ยน class เป็น .item-row --}}
                    <div class="item-row">
                        <span>ส่วนลดรวม:</span>
                         {{-- ⭐️ (Goal 2) ลบ style สีแดง --}}
                        <span>-{{ number_format($totalDiscount, 2) }} บาท</span>
                    </div>

                    {{-- ⭐️ (Goal 1) เปลี่ยน class เป็น .item-row และเพิ่ม font-weight-bold --}}
                    <div class="item-row font-weight-bold">
                        <span>ราคารวมสุทธิ:</span>
                        <span>{{ number_format($netTotal, 2) }} บาท</span>
                    </div>
                    
                    <hr class="my-2">

                   <div class="receipt-footer mt-4">
                       {{-- ... (ส่วน Footer ของใบเสร็จ เหมือนเดิม) ... --}}
                        <p class="text-sm mb-1">
                         <strong>พนักงาน:</strong> {{ $order->employee->em_name ?? 'ไม่ระบุ' }}
                         </p>
                            <p class="text-sm mb-1">
                          <strong>เบอร์โทร:</strong> {{ $order->employee->em_tel ?? 'ไม่ระบุ' }}
                        </p>
                            <p class="mt-3">ขอบคุณที่ใช้บริการ</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<div class="d-flex justify-content-end align-items-center mb-4 mr-4 no-print">
    <a href="{{ route('history.index') }}" class="btn btn-secondary mr-2">
        <i class="fas fa-arrow-left"></i> กลับ
    </a>
    <button onclick="window.print()" class="btn btn-primary">
        <i class="fas fa-print"></i> พิมพ์ใบเสร็จ
    </button>
</div>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>