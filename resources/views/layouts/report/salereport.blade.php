<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>รายงานยอดขาย</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container {
            position: relative;
            height: 400px;
            width: 80%;
            margin: auto;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
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
                            <i class="nav-icon fas fa-chart-line"></i> <p>รายงานยอดขาย</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <div class="content-wrapper">
        <section class="content pt-4">
            <div class="container-fluid">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white">
                        <h4 class="mb-0">📊 {{ $reportType ?? 'รายงานยอดขาย' }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <!-- ฟอร์มสำหรับกรองรายวัน -->
                            <div class="col-md-4">
                                <form action="{{ route('salereport.index') }}" method="GET">
                                    <label for="day_filter">กรองตามวัน:</label>
                                    <input type="date" name="day_filter" id="day_filter" class="form-control" value="{{ request('day_filter') }}" onchange="this.form.submit()">
                                </form>
                            </div>

                            <!-- ฟอร์มสำหรับกรองรายเดือน -->
                            <div class="col-md-4">
                                <form action="{{ route('salereport.index') }}" method="GET">
                                    <label for="month_filter">กรองตามเดือน:</label>
                                    <select name="month_filter" id="month_filter" class="form-control" onchange="this.form.submit()">
                                        <option value="">ทั้งหมด</option>
                                        @for ($m = 1; $m <= 12; $m++)
                                            <option value="{{ $m }}" @if(request('month_filter') == $m) selected @endif>{{ date('F', mktime(0, 0, 0, $m, 10)) }}</option>
                                        @endfor
                                    </select>
                                    <input type="hidden" name="year_filter" value="{{ request('year_filter') }}">
                                </form>
                            </div>

                            <!-- ฟอร์มสำหรับกรองรายปี -->
                            <div class="col-md-4">
                                <form action="{{ route('salereport.index') }}" method="GET">
                                    <label for="year_filter">กรองตามปี:</label>
                                    <select name="year_filter" id="year_filter" class="form-control" onchange="this.form.submit()">
                                        <option value="">ทั้งหมด</option>
                                        @for ($y = date('Y'); $y >= 2020; $y--)
                                            <option value="{{ $y }}" @if(request('year_filter') == $y) selected @endif>{{ $y }}</option>
                                        @endfor
                                    </select>
                                </form>
                            </div>
                        </div>

                        <div class="chart-container">
                            <canvas id="salesChart"></canvas>
                        </div>
                        <hr>
                        <h5>สรุปยอดขายสินค้า</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-secondary">
                                    <tr>
                                        <th>ลำดับ</th>
                                        <th>ชื่อสินค้า</th>
                                        <th>จำนวนที่ขายได้</th>
                                        <th>ยอดขายรวม</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reportData as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->product_name }}</td>
                                            <td>{{ $item->total_amount }} ชิ้น</td>
                                            <td>{{ number_format($item->total_revenue, 2) }} บาท</td>
                                        </tr>
                                    @endforeach
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
        const chartLabels = @json(array_values($chartLabels));
        const chartData = @json(array_values($chartData));
        
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'ยอดขาย',
                    data: chartData,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'ยอดขาย (บาท)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'วันที่'
                        }
                    }
                }
            }
        });
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
