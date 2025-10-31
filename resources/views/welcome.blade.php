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
        .info-box-col { margin-bottom: 1rem; }
        
        .alert-feed-box {
            max-height: 380px; /* ความสูงสำหรับ scroll */
            overflow-y: auto;
        }
        .adjustment-increase { background-color: #d4edda !important; }
        .adjustment-decrease { background-color: #f8d7da !important; }
        .card-header-tabs { margin-bottom: -1px; }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    {{-- (Navbar และ Sidebar เหมือนเดิม) --}}
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
         <ul class="navbar-nav">
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
        <a href="{{ route('welcome') }}" class="brand-link">
            <span class="brand-text font-weight-light">{{ session('admin_fullname') }}</span>
        </a>
        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-item">
                        <a href="{{ route('welcome') }}" class="nav-link active"><i class="nav-icon fas fa-home-alt"></i> <p>หน้าหลัก</p></a>
                    </li>
                    <li class="nav-header">การจัดการ</li>
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-cogs"></i>
                            <p>จัดการข้อมูลระบบ <i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item"><a href="{{ route('product.index') }}" class="nav-link"><i class="nav-icon fas fa-shopping-cart"></i><p>จัดการข้อมูลสินค้า</p></a></li>
                            <li class="nav-item"><a href="{{ route('employee.index') }}" class="nav-link"><i class="nav-icon fas fa-user-tie"></i><p>จัดการข้อมูลพนักงาน</p></a></li>
                            <li class="nav-item"><a href="{{ route('customer.index') }}" class="nav-link"><i class="nav-icon fas fa-users"></i><p>ข้อมูลลูกค้า</p></a></li>
                            <li class="nav-item"><a href="{{ route('stock.index') }}" class="nav-link"><i class="nav-icon fas fa-boxes"></i><p>จัดการข้อมูลล็อตสินค้า</p></a></li>
                            <li class="nav-item"><a href="{{ route('promotion.index') }}" class="nav-link"><i class="nav-icon fas fa-tags"></i><p>จัดการข้อมูลโปรโมชั่น</p></a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('history.index') }}" class="nav-link"><i class="nav-icon fas fa-history"></i> <p>ข้อมูลการสั่งซื้อสินค้า</p></a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('withdraw.create') }}" class="nav-link"><i class="nav-icon fas fa-dolly-flatbed"></i> <p>เบิกวัตถุดิบ</p></a>
                    </li>
                     <li class="nav-header">รายงาน</li>
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-chart-line"></i>
                            <p>รายงาน <i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item"><a href="{{ route('salereport.index') }}" class="nav-link"><i class="fas fa-chart-line nav-icon"></i><p>รายงานการขายสินค้า</p></a></li>
                            <li class="nav-item"><a href="{{ route('report.bills') }}" class="nav-link"><i class="fas fa-chart-bar nav-icon"></i> <p>รายงานยอดขาย</p></a></li>
                            <li class="nav-item"><a href="{{ route('report.withdrawals') }}" class="nav-link"><i class="fas fa-clipboard-list nav-icon"></i> <p>รายงานการเบิกวัตถุดิบ</p></a></li>
                            <li class="nav-item"><a href="{{ route('report.adjustments') }}" class="nav-link"><i class="fas fa-sliders-h nav-icon "></i> <p>รายงานกาปรับยอดล็อตสินค้า</p></a></li>
                        </ul>
                    </li>
                    <li class="nav-header">อื่นๆ</li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-toggle="modal" data-target="#logoutModal"><i class="nav-icon fas fa-sign-out-alt text-danger"></i> <p>ออกจากระบบ</p></a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <div class="content-wrapper p-3">
        <section class="content pt-3">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center mb-4">
                     <h4 class="m-0">สรุปภาพรวม</h4>
                     <a href="{{ route('withdraw.create') }}" class="btn btn-success shadow-sm">
                        <i class="fas fa-dolly-flatbed mr-2"></i> เบิกวัตถุดิบ
                    </a>
                </div>

                {{-- (Info Boxes... เหมือนเดิม) --}}
                <div class="row">
                    <div class="col-lg col-md-4 col-sm-6 info-box-col"><div class="info-box shadow-sm"><span class="info-box-icon bg-info elevation-1"><i class="fas fa-users"></i></span><div class="info-box-content"><span class="info-box-text">ลูกค้าทั้งหมด</span><span class="info-box-number">{{ $customerCount }}</span></div></div></div>
                    <div class="col-lg col-md-4 col-sm-6 info-box-col"><div class="info-box shadow-sm"><span class="info-box-icon bg-success elevation-1"><i class="fas fa-user-tie"></i></span><div class="info-box-content"><span class="info-box-text">พนักงาน</span><span class="info-box-number">{{ $employeeCount }}</span></div></div></div>
                    <div class="col-lg col-md-4 col-sm-6 info-box-col"><div class="info-box shadow-sm"><span class="info-box-icon bg-secondary elevation-1"><i class="fas fa-box-open"></i></span><div class="info-box-content"><span class="info-box-text">สินค้าทั้งหมด</span><span class="info-box-number">{{ $productCount }}</span></div></div></div>
                    <div class="col-lg col-md-6 col-sm-6 info-box-col"><div class="info-box shadow-sm"><span class="info-box-icon bg-primary elevation-1"><i class="fas fa-cubes"></i></span><div class="info-box-content"><span class="info-box-text">ชนิดวัตถุดิบ</span><span class="info-box-number">{{ $stockItemCount ?? 'N/A' }}</span></div></div></div>
                    
                    <div class="col-lg col-md-6 col-sm-6 info-box-col">
                        <div class="info-box shadow-sm">
                            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-calendar-day"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">ยอดขายวันนี้ ({{ $today->format('d/m/Y') }})</span>
                                <span class="info-box-number">{{ number_format($todaySales, 2) }} <small>บาท</small></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg col-md-6 col-sm-12 info-box-col"><div class="info-box shadow-sm"><span class="info-box-icon bg-warning elevation-1"><i class="fas fa-money-bill-wave"></i></span><div class="info-box-content"><span class="info-box-text">ยอดขายรวม</span><span class="info-box-number">{{ number_format($totalSales, 2) }} <small>บาท</small></span></div></div></div>
                </div>

                <div class="row mt-4">
                    <div class="col-lg-6 mb-4">
                        <div class="card card-warning card-outline ">
                            
                            <div class="card-header d-flex p-0">
                                <h3 class="card-title p-3"><i class="fas fa-bell text-warning"></i> แจ้งเตือนและประวัติสต็อก</h3>
                                <ul class="nav nav-pills ml-auto p-2 card-header-tabs">
                                    <li class="nav-item"><a class="nav-link active" href="#tab-expiring" data-toggle="tab">ใกล้หมดอายุ</a></li>
                                    <li class="nav-item"><a class="nav-link" href="#tab-adjustments" data-toggle="tab">การปรับยอด</a></li>
                                    <li class="nav-item"><a class="nav-link" href="#tab-withdrawals" data-toggle="tab">การเบิกล่าสุด</a></li>
                                </ul>
                            </div>
                            
                            <div class="card-body p-0">
                                <div class="tab-content" id="alert-tabs-content">
                                    
                                    <div class="tab-pane fade show active alert-feed-box" id="tab-expiring" role="tabpanel">
                                        <ul class="products-list product-list-in-card pl-2 pr-2">
                                            @forelse($expiringStock as $stock)
                                            <li class="item"><div class="product-info ml-2"><a href="{{ route('stock.edit', $stock->mat_id) }}" class="product-title">{{ $stock->mat_name }}<span class="badge badge-warning float-right">{{ $stock->remain }} ชิ้น</span></a><span class="product-description text-sm">จะหมดอายุใน: <strong>{{ $stock->days_to_expire }} วัน</strong> ({{ \Carbon\Carbon::parse($stock->exp_date)->format('d/m/Y') }})</span></div></li>
                                            @empty
                                            <li class="item text-center p-3 text-muted"><span><i class="fas fa-check-circle text-success"></i> ไม่มีวัตถุดิบใกล้หมดอายุใน 15 วัน</span></li>
                                            @endforelse
                                        </ul>
                                    </div>
                                    
                                <div class="tab-pane fade alert-feed-box" id="tab-adjustments" role="tabpanel">
                                        <table class="table table-sm table-hover">
                                            <tbody>
                                                @forelse($latestAdjustments as $adj)
                            
                                                    <tr class="{{ $adj->amount > 0 ? 'adjustment-increase' : 'adjustment-decrease' }}">
                                                        <td style="width: 75%;">
                                                            <a href="{{ route('stock.edit', $adj->stock_mat_id) }}" class="font-weight-bold">{{ $adj->stockMat->mat_name ?? 'N/A' }}</a>
                                                            <small class="d-block text-muted">
                                                                {{ $adj->admin->fullname ?? 'N/A' }}
                                                                @if($adj->amount > 0)
                                                                    <span class="text-success">(เพิ่ม {{ abs($adj->amount) }})</span>
                                                                @else
                                                                    <span class="text-danger">(ลด {{ abs($adj->amount) }})</span>
                                                                @endif
                                                            </small>
                                                        </td>
                                                        <td class="text-right text-sm align-middle">{{ $adj->adjust_date->format('d/m H:i') }}</td>
                                                    </tr>
                                                @empty
                                                    <tr><td colspan="2" class="text-center p-3 text-muted"><i class="fas fa-info-circle"></i> ยังไม่มีประวัติการปรับยอด</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                   <div class="tab-pane fade alert-feed-box" id="tab-withdrawals" role="tabpanel">
                                        <table class="table table-sm table-hover">
                                            <tbody>
                                                @forelse($latestWithdrawals as $withdrawal)
                                                    <tr>
                                                        <td style="width: 75%;">
                                                            @if($withdrawal->mat_id)
                                                                <a href="{{ route('stock.edit', $withdrawal->mat_id) }}" class="font-weight-bold">
                                                                    {{ $withdrawal->stockMaterial->mat_name ?? 'N/A' }}
                                                                </a>
                                                            @else
                                                                <span class="font-weight-bold text-danger">(ข้อมูลวัตถุดิบไม่สมบูรณ์)</span>
                                                            @endif
                                                            
                                                            <small class="d-block text-muted">
                                                                {{ $withdrawal->admin->fullname ?? 'N/A' }}
                                                                <span class="text-secondary">(เบิก {{ $withdrawal->withdraw_amount }})</span>
                                                            </small>
                                                        </td>
                                                        <td class="text-right text-sm align-middle">{{ $withdrawal->withdraw_date->format('d/m H:i') }}</td>
                                                    </tr>
                                                @empty
                                                    <tr><td colspan="2" class="text-center p-3 text-muted"><i class="fas fa-info-circle"></i> ยังไม่มีประวัติการเบิก</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                </div>
                            </div>

                        </div>
                    </div>
                    {{-- สินค้าโปรโมชั่นพิเศษ (เหมือนเดิม) --}}
                    <div class="col-lg-6 mb-4">
                        <div class="card card-danger card-outline ">
                            <div class="card-header"><h3 class="card-title"><i class="fas fa-star text-danger"></i> สินค้าโปรโมชั่นพิเศษ <a href="{{ route('promotion.index') }}" class="text-sm">(จัดการโปรโมชั่น)</a></h3></div>
                            <div class="card-body p-0">
                                <table class="table table-hover table-sm">
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
                                                        <span class="price-special">{{ number_format($specialPrice, 2) }}</span><span class="badge bg-danger ml-1">-{{ number_format($promo->promo_discount, 0) }}</span>
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

                {{-- ประวัติการขาย (5 รายการล่าสุด) --}}
             <div class="row mt-2">
    <div class="col-12">
        <div class="card card-info card-outline">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-history text-info"></i> ประวัติการขาย (5 รายการล่าสุด)</h3></div>
            <div class="card-body p-0">
                @if($latestSales->isEmpty())
                    <p class="placeholder-text text-center p-4">ยังไม่มีข้อมูลการขาย</p>
                @else
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th style="width: 10%;">#บิล</th>
                                <th>ผู้ทำรายการ</th>
                                <th class="text-right">ยอดรวม</th>
                                <th class="text-center">เวลา</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($latestSales as $sale)
                                <tr>
                                    <td>#{{ $sale->re_id }}</td>
                                    
                                    {{-- [แก้ไข] 👈 เปลี่ยน 'fullname' เป็น 'em_name' --}}
                                    <td>{{ $sale->order->employee->em_name ?? 'N/A' }}</td>
                                    
                                    <td class="text-right text-success font-weight-bold">{{ number_format($sale->price_total, 2) }}</td>
                                    <td class="text-center text-sm">{{ $sale->re_date->format('d/m H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
             @if($latestSales->isNotEmpty())
             <div class="card-footer text-center">
                 <a href="{{ route('history.index') }}">ดูประวัติการขายทั้งหมด</a>
             </div>
             @endif
        </div>
    </div>
</div>
    
    {{-- (Modal Logout - โค้ดที่คุณส่งมาถูกต้องครับ) --}}
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
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