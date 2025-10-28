<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>หน้าหลัก</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
    <style>
        .info-box .info-box-icon { font-size: 2rem; width: 70px; height: 70px; line-height: 70px; }
        .card-title a { font-size: 0.8rem; vertical-align: middle; margin-left: 8px; }
        .price-original { text-decoration: line-through; color: #6c757d; }
        .price-special { font-weight: bold; color: #dc3545; }
        .row-eq-height > [class*='col-'] { display: flex; flex-direction: column; }
        .row-eq-height > [class*='col-'] > .card { flex: 1 1 auto; }
        .placeholder-text { color: #6c757d; font-style: italic; }
        /* Responsive Info Box Columns */
        .info-box-col { margin-bottom: 1rem; } /* Add bottom margin for spacing */
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
             {{-- ✅ เพิ่มปุ่ม Toggle Sidebar --}}
             <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">ระบบจัดการร้าน ริมหนองคาเฟ่</a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                    <i class="fas fa-expand-arrows-alt"></i>
                </a>
            </li>
        </ul>
    </nav>

    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="{{ route('welcome') }}" class="brand-link"> {{-- ✅ Link brand to welcome --}}
            <span class="brand-text font-weight-light">{{ session('admin_fullname') }}</span>
        </a>
        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false"> {{-- ✅ Add widgets to main UL --}}
                    <li class="nav-item">
                        <a href="{{ route('welcome') }}" class="nav-link active"><i class="nav-icon fas fa-home-alt"></i> <p>หน้าหลัก</p></a>
                    </li>

                    <li class="nav-header">การจัดการ</li> {{-- ✅ Add header for clarity --}}

                    {{-- ✅ [FIX] แก้ไขโครงสร้าง Treeview --}}
                    <li class="nav-item has-treeview"> {{-- Add has-treeview here --}}
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-cogs"></i>
                            <p>จัดการข้อมูลระบบ <i class="right fas fa-angle-left"></i></p>
                        </a>
{{-- (Dropdown จัดการข้อมูลระบบ) --}}
                        <ul class="nav nav-treeview">
                            {{-- เปลี่ยน icon เป็น shopping-cart --}}
                            <li class="nav-item">
                                <a href="{{ route('product.index') }}" class="nav-link">
                                    <i class="nav-icon fas fa-shopping-cart"></i>
                                    <p>จัดการข้อมูลสินค้า</p>
                                </a>
                            </li>
                            {{-- เปลี่ยน icon เป็น user-tie (เหมาะกับพนักงาน) --}}
                            <li class="nav-item">
                                <a href="{{ route('employee.index') }}" class="nav-link">
                                    <i class="nav-icon fas fa-user-tie"></i>
                                    <p>จัดการข้อมูลพนักงาน</p>
                                </a>
                            </li>
                            {{-- เปลี่ยน icon เป็น users --}}
                            <li class="nav-item">
                                <a href="{{ route('customer.index') }}" class="nav-link">
                                    <i class="nav-icon fas fa-users"></i>
                                    <p>ข้อมูลลูกค้า</p>
                                </a>
                            </li>
                            {{-- เปลี่ยน icon เป็น boxes (เหมาะกับสต็อก) --}}
                            <li class="nav-item">
                                <a href="{{ route('stock.index') }}" class="nav-link">
                                    <i class="nav-icon fas fa-boxes"></i>
                                    <p>จัดการข้อมูลล็อตสินค้า</p>
                                </a>
                            </li>
                            {{-- เปลี่ยน icon เป็น tags (เหมาะกับโปรโมชั่น) --}}
                            <li class="nav-item">
                                <a href="{{ route('promotion.index') }}" class="nav-link">
                                    <i class="nav-icon fas fa-tags"></i>
                                    <p>จัดการข้อมูลโปรโมชั่น</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('history.index') }}" class="nav-link"><i class="nav-icon fas fa-history"></i> <p>ข้อมูลการสั่งซื้อสินค้า</p></a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('withdraw.create') }}" class="nav-link"><i class="nav-icon fas fa-dolly-flatbed"></i> <p>เบิกวัตถุดิบ</p></a>
                    </li>

                     <li class="nav-header">รายงาน</li> {{-- ✅ Add header --}}

                     {{-- ✅ [FIX] แก้ไขโครงสร้าง Treeview และ ปิด Tag ให้ถูก --}}
                    <li class="nav-item has-treeview"> {{-- Add has-treeview here --}}
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-chart-line"></i>
                            <p>รายงาน <i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview"> {{-- Keep nav-treeview here --}}
                            <li class="nav-item"><a href="{{ route('salereport.index') }}" class="nav-link"><i class="far fa-circle nav-icon text-teal"></i> <p>รายงานการขายสินค้า</p></a></li>
                            <li class="nav-item"><a href="{{ route('report.bills') }}" class="nav-link"><i class="far fa-circle nav-icon text-purple"></i> <p>รายงานยอดขาย</p></a></li>
                            <li class="nav-item"><a href="#" class="nav-link"><i class="far fa-circle nav-icon text-orange"></i> <p>รายงานการเบิกวัตถุดิบ</p></a></li>
                        </ul>
                    </li> {{-- ปิด li ของ รายงาน --}}


                    <li class="nav-header">อื่นๆ</li> {{-- ✅ Add header --}}
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-toggle="modal" data-target="#logoutModal"><i class="nav-icon fas fa-sign-out-alt text-danger"></i> <p>ออกจากระบบ</p></a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <div class="content-wrapper p-3">
        <section class="content pt-3"> {{-- ✅ ลด Padding Top --}}
            <div class="container-fluid">
                {{-- ✅ ปรับปรุงส่วนหัว --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                     <h4 class="m-0">สรุปภาพรวม</h4>
                     {{-- ปุ่มเบิกวัตถุดิบ ย้ายมามุมขวาบน --}}
                     <a href="{{ route('withdraw.create') }}" class="btn btn-success shadow-sm">
                        <i class="fas fa-dolly-flatbed mr-2"></i> เบิกวัตถุดิบ
                    </a>
                </div>

                {{-- ✅ ใช้ Bootstrap Columns เพื่อ Responsive ที่ดีขึ้น --}}
                <div class="row">
                    <div class="col-lg col-md-4 col-sm-6 info-box-col"><div class="info-box shadow-sm"><span class="info-box-icon bg-info elevation-1"><i class="fas fa-users"></i></span><div class="info-box-content"><span class="info-box-text">ลูกค้าทั้งหมด</span><span class="info-box-number">{{ $customerCount }}</span></div></div></div>
                    <div class="col-lg col-md-4 col-sm-6 info-box-col"><div class="info-box shadow-sm"><span class="info-box-icon bg-success elevation-1"><i class="fas fa-user-tie"></i></span><div class="info-box-content"><span class="info-box-text">พนักงาน</span><span class="info-box-number">{{ $employeeCount }}</span></div></div></div>
                    <div class="col-lg col-md-4 col-sm-6 info-box-col"><div class="info-box shadow-sm"><span class="info-box-icon bg-secondary elevation-1"><i class="fas fa-box-open"></i></span><div class="info-box-content"><span class="info-box-text">สินค้าทั้งหมด</span><span class="info-box-number">{{ $productCount }}</span></div></div></div>
                    <div class="col-lg col-md-6 col-sm-6 info-box-col"><div class="info-box shadow-sm"><span class="info-box-icon bg-primary elevation-1"><i class="fas fa-cubes"></i></span><div class="info-box-content"><span class="info-box-text">ชนิดวัตถุดิบ</span><span class="info-box-number">{{ $stockItemCount ?? 'N/A' }}</span></div></div></div>
                    <div class="col-lg col-md-6 col-sm-12 info-box-col"><div class="info-box shadow-sm"><span class="info-box-icon bg-warning elevation-1"><i class="fas fa-money-bill-wave"></i></span><div class="info-box-content"><span class="info-box-text">ยอดขายรวม</span><span class="info-box-number">{{ number_format($totalSales, 2) }} <small>บาท</small></span></div></div></div>
                </div>

                {{-- ✅ ลบปุ่มเบิกตรงกลางออก --}}
                {{-- <div class="row mt-4 mb-4"> ... </div> --}}

                <div class="row mt-4 row-eq-height">
                    {{-- วัตถุดิบใกล้หมดอายุ --}}
                    <div class="col-lg-6 mb-4"> {{-- ✅ ใช้ col-lg-6 และ mb-4 --}}
                        <div class="card card-warning card-outline h-100"> {{-- ✅ เพิ่ม h-100 --}}
                            <div class="card-header"><h3 class="card-title"><i class="fas fa-exclamation-triangle text-warning"></i> วัตถุดิบใกล้หมดอายุ <a href="{{ route('stock.index') }}" class="text-sm">(จัดการสต็อก)</a></h3></div>
                            <div class="card-body p-0"><ul class="products-list product-list-in-card pl-2 pr-2">
                                @forelse($expiringStock as $stock)
                                <li class="item"><div class="product-info ml-2"><a href="{{ route('stock.edit', $stock->mat_id) }}" class="product-title">{{ $stock->mat_name }}<span class="badge badge-warning float-right">{{ $stock->remain }} ชิ้น</span></a><span class="product-description text-sm">จะหมดอายุใน: <strong>{{ $stock->days_to_expire }} วัน</strong> ({{ \Carbon\Carbon::parse($stock->exp_date)->format('d/m/Y') }})</span></div></li>
                                @empty
                                <li class="item text-center p-3 text-muted"><span><i class="fas fa-check-circle text-success"></i> ไม่มีวัตถุดิบใกล้หมดอายุใน 15 วัน</span></li>
                                @endforelse
                            </ul></div>
                        </div>
                    </div>

                    {{-- สินค้าโปรโมชั่นพิเศษ --}}
                    <div class="col-lg-6 mb-4"> {{-- ✅ ใช้ col-lg-6 และ mb-4 --}}
                        <div class="card card-danger card-outline h-100"> {{-- ✅ เพิ่ม h-100 --}}
                            <div class="card-header"><h3 class="card-title"><i class="fas fa-star text-danger"></i> สินค้าโปรโมชั่นพิเศษ <a href="{{ route('promotion.index') }}" class="text-sm">(จัดการโปรโมชั่น)</a></h3></div>
                            <div class="card-body p-0">
                                <table class="table table-hover table-sm"> {{-- ✅ เพิ่ม table-sm --}}
                                    <thead><tr><th style="width: 50%;">สินค้า</th><th class="text-center">ราคาปกติ</th><th class="text-center">ราคาพิเศษ</th></tr></thead>
                                    <tbody>
                                        @forelse($activePromotions as $promo)
                                            @if($promo->product)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('product.edit', $promo->pro_id) }}" class="font-weight-bold">{{ $promo->product->pro_name }}</a>
                                                        <small class="d-block text-muted">{{ $promo->promo_name }} (เหลือ {{ $promo->days_left }} วัน)</small>
                                                    </td>
                                                    <td class="text-center align-middle"><span class="price-original">{{ number_format($promo->product->price, 2) }}</span></td>
                                                    <td class="text-center align-middle">
                                                        @php $specialPrice = $promo->product->price - $promo->promo_discount; @endphp
                                                        <span class="price-special">{{ number_format($specialPrice, 2) }}</span><span class="badge bg-danger ml-1">-{{ number_format($promo->promo_discount, 0) }}</span> {{-- ✅ Format ส่วนลด --}}
                                                    </td>
                                                </tr>
                                            @endif
                                        @empty
                                            <tr><td colspan="3" class="text-center p-4 text-muted"><i class="fas fa-info-circle"></i> ขณะนี้ไม่มีสินค้าโปรโมชั่น</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-2"> {{-- ✅ ลด mt --}}
                    <div class="col-12">
                        <div class="card card-info card-outline">
                            <div class="card-header"><h3 class="card-title"><i class="fas fa-exchange-alt text-info"></i> ประวัติการเบิกวัตถุดิบ (5 รายการล่าสุด)</h3></div>
                            <div class="card-body p-0">
                                @if($latestWithdrawals->isEmpty())
                                    <p class="placeholder-text text-center p-4">ยังไม่มีข้อมูลการเบิกวัตถุดิบ</p>
                                @else
                                    <table class="table table-striped table-sm"> {{-- ✅ เพิ่ม table-sm --}}
                                        <thead><tr><th style="width: 10%;">#</th><th>ชื่อวัตถุดิบ</th><th class="text-center">จำนวน</th><th class="text-right">ราคา</th><th class="text-center">เวลา</th><th>ผู้เบิก</th></tr></thead>
                                        <tbody>
                                            @foreach($latestWithdrawals as $withdrawal)
                                                <tr>
                                                    <td>#{{ $withdrawal->withdrawal_id }}</td>
                                                    <td>{{ $withdrawal->stockMaterial->mat_name ?? 'N/A' }}</td>
                                                    <td class="text-center">{{ $withdrawal->withdraw_amount }}</td>
                                                    <td class="text-right text-muted">{{ number_format($withdrawal->calculated_cost, 2) }}</td>
                                                    <td class="text-center text-sm">{{ $withdrawal->withdraw_date->format('d/m H:i') }}</td>
                                                    <td class="text-sm">{{ $withdrawal->admin->fullname ?? 'N/A' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            </div>
                             {{-- ✅ เพิ่ม Footer ลิงก์ดูทั้งหมด (ถ้าต้องการ) --}}
                             @if($latestWithdrawals->isNotEmpty())
                             <div class="card-footer text-center">
                                 <a href="#">ดูประวัติการเบิกทั้งหมด</a>
                             </div>
                             @endif
                        </div>
                    </div>
                </div>

            </div></section>
    </div><div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
       <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header bg-danger"><h5 class="modal-title" id="logoutModalLabel">ยืนยันการออกจากระบบ</h5><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
          <div class="modal-body">คุณแน่ใจหรือไม่ว่าต้องการออกจากระบบ?</div>
          <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button><form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="btn btn-danger">ออกจากระบบ</button></form></div>
        </div>
      </div>
    </div>

</div><script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>