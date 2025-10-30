<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>รายงานใบเสร็จ</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    
    {{-- (Navbar - ถูกต้องแล้ว) --}}
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

    {{-- (Sidebar - ถูกต้องแล้ว) --}}
   <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="{{ route('welcome') }}" class="brand-link">
            <span class="brand-text font-weight-light">{{ session('admin_fullname') }}</span>
        </a>
        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-item">
                        <a href="{{ route('welcome') }}" class="nav-link">
                            <i class="nav-icon fas fa-home-alt"></i> <p>หน้าหลัก</p>
                        </a>
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
                    <li class="nav-item has-treeview menu-open">
                        <a href="#" class="nav-link active">
                            <i class="nav-icon fas fa-chart-pie"></i> 
                            <p>รายงาน <i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('salereport.index') }}" class="nav-link">
                                    <i class="fas fa-chart-line nav-icon text-teal"></i> 
                                    <p>รายงานการขายสินค้า</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('report.bills') }}" class="nav-link active">
                                    <i class="fas fa-chart-bar nav-icon "></i> 
                                    <p>รายงานยอดขาย</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('report.withdrawals') }}" class="nav-link">
                                    <i class="fas fa-clipboard-list nav-icon"></i> 
                                    <p>รายงานการเบิกวัตถุดิบ</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('report.adjustments') }}" class="nav-link">
                                    <i class="fas fa-sliders-h nav-icon "></i> 
                                    <p>รายงานกาปรับยอดล็อตสินค้า</p>
                                </a>
                            </li>
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

    {{-- (Content Wrapper... โค้ดที่เหลือเหมือนเดิม) --}}
    <div class="content-wrapper">
        <section class="content pt-4">
            <div class="container-fluid">
                {{-- ฟอร์มกรองข้อมูล --}}
                <div class="card card-outline card-primary shadow-sm mb-4">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-filter"></i> ตัวกรองข้อมูล</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('report.bills') }}" method="GET">
                            <div class="form-row align-items-end">
                                <div class="col-lg-4 col-md-6 mb-2">
                                    <label for="start_date">วันที่เริ่มต้น:</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                                </div>
                                <div class="col-lg-4 col-md-6 mb-2">
                                    <label for="end_date">วันที่สิ้นสุด:</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                                </div>
                                <div class="col-lg-4 col-md-12">
                                    <button type="submit" class="btn btn-success mr-2"><i class="fas fa-search"></i> แสดงรายงาน</button>
                                    <a href="{{ route('report.bills') }}" class="btn btn-secondary"><i class="fas fa-eraser"></i> ล้างค่า</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- ส่วนแสดงผลรายงาน --}}
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white">
                        <h4 class="mb-0">📜 {{ $reportType ?? 'รายงานใบเสร็จ' }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="small-box bg-info">
                                    <div class="inner">
                                        <h3>{{ number_format($receiptCount) }}</h3>
                                        <p>จำนวนใบเสร็จทั้งหมด</p>
                                    </div>
                                    <div class="icon"><i class="fas fa-receipt"></i></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="small-box bg-success">
                                    <div class="inner">
                                        <h3>{{ number_format($totalRevenue, 2) }}<sup style="font-size: 20px"> ฿</sup></h3>
                                        <p>รายรับรวม</p>
                                    </div>
                                    <div class="icon"><i class="fas fa-dollar-sign"></i></div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-secondary text-center">
                                    <tr>
                                        <th>#</th>
                                        <th>เลขที่ใบเสร็จ</th>
                                        <th>วันที่</th>
                                        <th>ชื่อลูกค้า</th>
                                        <th>พนักงาน</th>
                                        <th>ยอดรวม (บาท)</th>
                                        <th>จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($reportData as $index => $item)
                                        <tr>
                                            <td class="text-center">{{ $reportData->firstItem() + $index }}</td>
                                            <td class="text-center">{{ $item->re_id }}</td>
                                            <td class="text-center">{{ \Carbon\Carbon::parse($item->re_date)->format('d/m/Y H:i') }}</td>
                                            <td>{{ $item->customer_name }}</td>
                                            <td>{{ $item->employee_name ?? 'N/A' }}</td>
                                            <td class="text-right">{{ number_format($item->price_total, 2) }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('order.receipt', ['id' => $item->order_id]) }}" class="btn btn-primary btn-sm" target="_blank">
                                                    <i class="fas fa-eye"></i> ดูใบเสร็จ
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">ไม่พบข้อมูลใบเสร็จสำหรับช่วงวันที่ที่เลือก</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $reportData->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
       <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header bg-danger"><h5 class="modal-title" id="logoutModalLabel">ยืนยันการออกจากระบบ</h5><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
          <div class="modal-body">คุณแน่ใจหรือไม่ว่าต้องการออกจากระบบ?</div>
          <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button><form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="btn btn-danger">ออกจากระบบ</button></form></div>
        </div>
      </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>