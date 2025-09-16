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
    <aside class="main-sidebar sidebar-dark-primary elevation-4 min-vh-100">
           <aside class="main-sidebar sidebar-dark-primary elevation-4 min-vh-100">
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
    <a href="{{ route('salereport.index') }}" class="nav-link text-white active">
        <i class="nav-icon fas fa-chart-line"></i> <p>รายงานยอดขายสินค้า</p>
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('report.bills') }}" class="nav-link text-white">
        <i class="nav-icon fas fa-file-invoice-dollar"></i> <p>รายงานใบเสร็จ</p>
    </a>
</li>
                            
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const chartLabels = @json($chartLabels);
        const chartDatasets = @json($chartDatasets);

        // ✅ [ADD] ตรวจสอบว่ามีข้อมูลสำหรับกราฟหรือไม่
        if (chartDatasets.length > 0) {
            function generateColor() {
                const r = Math.floor(Math.random() * 200);
                const g = Math.floor(Math.random() * 200);
                const b = Math.floor(Math.random() * 200);
                return `rgb(${r}, ${g}, ${b})`;
            }

            chartDatasets.forEach(dataset => {
                const color = generateColor();
                dataset.borderColor = color;
                dataset.backgroundColor = color + '33';
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
            // ถ้าไม่มีข้อมูล ให้ซ่อน Canvas และแสดงข้อความ
            document.getElementById('salesChart').style.display = 'none';
            document.getElementById('noChartData').style.display = 'block';
        }
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>