<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>รายงานยอดขาย</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container { position: relative; height: 450px; width: 100%; }
    </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="{{ route('welcome') }}" class="brand-link">
            <span class="brand-text font-weight-light">{{ session('admin_fullname') }}</span>
        </a>
        <div class="sidebar">
            <nav class="mt-2">
                {{-- [แก้ไข] 1. ใช้ <ul> หลักอันเดียว --}}
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-item">
                        {{-- 2. ลิงก์ 'active' ออกจากหน้าหลัก --}}
                        <a href="{{ route('welcome') }}" class="nav-link">
                            <i class="nav-icon fas fa-home-alt"></i> <p>หน้าหลัก</p>
                        </a>
                    </li>
                    
                    {{-- (ผมเพิ่มเมนู "การจัดการ" จากไฟล์ welcome.blade.php เข้ามาให้ครบ) --}}
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
                     {{-- [แก้ไข] 3. ทำให้เมนูรายงาน 'active' และ 'menu-open' --}}
                    <li class="nav-item has-treeview menu-open">
                        <a href="#" class="nav-link active">
                            <i class="nav-icon fas fa-chart-pie"></i> {{-- 👈 ไอคอนหลักของ "รายงาน" --}}
                            <p>รายงาน <i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                {{-- [แก้ไข] 4. ทำให้หน้านี้ 'active' และเปลี่ยนไอคอน --}}
                                <a href="{{ route('salereport.index') }}" class="nav-link active">
                                    <i class="fas fa-chart-line nav-icon text-teal"></i> {{-- 👈 ไอคอนสื่อความหมาย --}}
                                    <p>รายงานการขายสินค้า</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('report.bills') }}" class="nav-link">
                                    <i class="fas fa-chart-bar nav-icon"></i> {{-- 👈 ไอคอนสื่อความหมาย --}}
                                    <p>รายงานยอดขาย</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('report.withdrawals') }}" class="nav-link">
                                    <i class="fas fa-clipboard-list nav-icon"></i> {{-- 👈 ไอคอนสื่อความหมาย --}}
                                    <p>รายงานการเบิกวัตถุดิบ</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('report.adjustments') }}" class="nav-link">
                                    <i class="fas fa-sliders-h nav-icon "></i> {{-- 👈 ไอคอนสื่อความหมาย --}}
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
    <div class="content-wrapper">
        <section class="content pt-4">
            <div class="container-fluid">

                {{-- ✅ [RE-LAYOUT] แยกฟอร์มกรองข้อมูลออกมาเป็น Card ของตัวเอง --}}
                <div class="card card-outline card-primary shadow-sm mb-4">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-filter"></i> ตัวกรองข้อมูล</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('salereport.index') }}" method="GET">
                            <div class="form-row align-items-end">
                                <div class="col-lg-4 col-md-6 mb-2">
                                    <label for="start_date">วันที่เริ่มต้น:</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}">
                                </div>
                                <div class="col-lg-4 col-md-6 mb-2">
                                    <label for="end_date">วันที่สิ้นสุด:</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date', now()->format('Y-m-d')) }}">
                                </div>
                                <div class="col-lg-4 col-md-12">
                                    <button type="submit" class="btn btn-success mr-2"><i class="fas fa-search"></i> แสดงรายงาน</button>
                                    <a href="{{ route('salereport.index') }}" class="btn btn-secondary"><i class="fas fa-eraser"></i> ล้างค่า</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- ✅ [RE-LAYOUT] ส่วนแสดงผลรายงาน --}}
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white">
                        <h4 class="mb-0">📊 {{ $reportType ?? 'รายงานยอดขาย' }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="salesChart"></canvas>
                            {{-- ✅ [ADD] เพิ่มข้อความเมื่อไม่มีข้อมูลกราฟ --}}
                            <div id="noChartData" class="text-center text-muted" style="display: none; padding-top: 150px;">
                                <h5><i class="fas fa-chart-bar"></i> ไม่มีข้อมูลสำหรับแสดงผลในกราฟ</h5>
                            </div>
                        </div>
                        <hr>
                        <h5>สรุปยอดขายสินค้า</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-secondary text-center">
                                    <tr>
                                        <th>ลำดับ</th>
                                        <th>ชื่อสินค้า</th>
                                        <th>จำนวนที่ขายได้ (ชิ้น)</th>
                                        <th>ยอดขายรวม (บาท)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($reportData as $index => $item)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>{{ $item->product_name }}</td>
                                            <td class="text-center">{{ $item->total_amount }}</td>
                                            <td class="text-right">{{ number_format($item->total_revenue, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">ไม่พบข้อมูลสำหรับช่วงวันที่ที่เลือก</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const chartLabels = @json($chartLabels);
        const chartDatasets = @json($chartDatasets);

        if (chartDatasets.length > 0) {
            // ✅ [REVISED] เปลี่ยนจากการสุ่มสี เป็นการใช้ชุดสีที่กำหนดไว้
            const colorPalette = [
                '#3498db', '#e74c3c', '#2ecc71', '#9b59b6', '#f1c40f', '#1abc9c',
                '#e67e22', '#34495e', '#d35400', '#c0392b', '#7f8c8d', '#2980b9'
            ];

            // วนลูปเพื่อกำหนดสีให้แต่ละเส้นกราฟจากชุดสีที่เตรียมไว้
            chartDatasets.forEach((dataset, index) => {
                // ใช้ % เพื่อวนกลับมาใช้สีแรกถ้ามีสินค้ามากกว่าจำนวนสี
                const color = colorPalette[index % colorPalette.length];
                dataset.borderColor = color;
                dataset.backgroundColor = color + '33'; // '33' คือการเติม Alpha เพื่อให้สีโปร่งแสง
            });
            
            const ctx = document.getElementById('salesChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: { labels: chartLabels, datasets: chartDatasets },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, title: { display: true, text: 'ยอดขาย (บาท)' } },
                        x: { title: { display: true, text: 'วันที่ขาย' } }
                    },
                    plugins: { legend: { position: 'top' }, tooltip: { mode: 'index', intersect: false } },
                    interaction: { mode: 'index', intersect: false },
                }
            });
        } else {
            document.getElementById('salesChart').style.display = 'none';
            document.getElementById('noChartData').style.display = 'block';
        }
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</html>