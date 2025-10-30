<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>รายงานการเบิกวัตถุดิบ</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    {{-- (Navbar และ Sidebar เหมือนเดิม - คัดลอกจากไฟล์ welcome.blade.php มาได้เลย) --}}
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
         {{-- (โค้ด Navbar...) --}}
    </nav>
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
                                <a href="{{ route('report.bills') }}" class="nav-link ">
                                    <i class="fas fa-chart-bar nav-icon "></i> 
                                    <p>รายงานยอดขาย</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('report.withdrawals') }}" class="nav-link active">
                                    <i class="fas fa-clipboard-list nav-icon"></i> 
                                    <p>รายงานการเบิกวัตถุดิบ</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('report.adjustments') }}" class="nav-link ">
                                    <i class="fas fa-sliders-h nav-icon "></i> 
                                    <p>รายงานกาปรับยอด</p>
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
    <div class="content-wrapper p-3">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>รายงานการเบิกวัตถุดิบ</h1>
                    </div>
                </div>
            </div>
        </section>
        
        <section class="content">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">ประวัติการเบิกทั้งหมด</h3>
                   <a href="{{ route('report.withdrawals.print') }}" target="_blank" class="btn btn-primary btn-sm float-right">
                        <i class="fas fa-print"></i> พิมพ์รายงาน
                    </a>
                </div>
                
                <div class="card-body p-0">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                {{-- 2. หัวตาราง (ตรงกับที่คุณขอ) --}}
                                <th style="width: 10%;">#รหัสการเบิก</th>
                                <th>ชื่อแอดมิน</th>
                                <th>สินค้าที่เบิก</th>
                                <th class="text-center">จำนวน</th>
                                <th class="text-center">วันที่</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($withdrawals as $item)
                                <tr>
                                    {{-- 3. แสดงข้อมูล (ตามที่ Controller ส่งมา) --}}
                                    <td>#{{ $item->withdrawal_id }}</td>
                                    <td>{{ $item->admin->fullname ?? 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('stock.edit', $item->mat_id) }}">
                                            {{ $item->stockMaterial->mat_name ?? 'N/A' }}
                                        </a>
                                    </td>
                                    <td class="text-center">{{ $item->withdraw_amount }}</td>
                                    <td class="text-center">{{ $item->withdraw_date->format('d/m/Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted p-4">
                                        <i class="fas fa-info-circle"></i> ยังไม่มีข้อมูลการเบิก
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="card-footer clearfix">
                    {{-- 4. แสดงตัวแบ่งหน้า --}}
                    {{ $withdrawals->links() }}
                </div>
            </div>
        </section>
    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>