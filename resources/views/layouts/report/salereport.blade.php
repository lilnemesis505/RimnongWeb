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
    {{-- Sidebar (เมนูด้านข้าง) - ไม่มีการแก้ไข --}}
    @include('layouts.assets._navbar')
    
    @include('layouts.assets._sidebar')

    {{-- Content (เนื้อหา) - ไม่มีการแก้ไข --}}
    <div class="content-wrapper">
        <section class="content pt-4">
            <div class="container-fluid">
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
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white">
                        <h4 class="mb-0">📊 {{ $reportType ?? 'รายงานยอดขาย' }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="salesChart"></canvas>
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

{{-- Modal Logout - ไม่มีการแก้ไข --}}
 <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
       <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header bg-danger"><h5 class="modal-title" id="logoutModalLabel">ยืนยันการออกจากระบบ</h5><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
          <div class="modal-body">คุณแน่ใจหรือไม่ว่าต้องการออกจากระบบ?</div>
          <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button><form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="btn btn-danger">ออกจากระบบ</button></form></div>
        </div>
      </div>
    </div>

{{-- ⭐️ [แก้ไข] Script ทั้งหมดด้านล่างนี้ ⭐️ --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        
        // 1. ดึงข้อมูลจากตาราง ($reportData) มาใช้แทน
        const reportData = @json($reportData);

        // 2. เช็คว่ามีข้อมูลหรือไม่
        if (reportData && reportData.length > 0) {
            
            // 3. เตรียมข้อมูลสำหรับกราฟใหม่
            // แกน X = ชื่อสินค้า
            const newChartLabels = reportData.map(item => item.product_name);
            // แกน Y = จำนวนที่ขายได้ (ตามที่คุณขอ)
            const newChartData = reportData.map(item => item.total_amount);

            // 4. สร้างแท่งกราฟแท่งเดียว สีเขียวเข้ม (ตามที่คุณขอ)
            const newDataset = {
                label: 'จำนวนที่ขายได้ (ชิ้น)',
                data: newChartData,
                backgroundColor: '#27ae60', // สีเขียวเข้ม
                borderColor: '#2ecc71',
                borderWidth: 1
            };
            
            const ctx = document.getElementById('salesChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar', // ใช้กราฟแท่ง
                data: { 
                    labels: newChartLabels, 
                    datasets: [newDataset] // 5. ใช้ Dataset ใหม่ที่สร้างขึ้น
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        // 6. เปลี่ยนชื่อแกน Y
                        y: { beginAtZero: true, title: { display: true, text: 'จำนวนที่ขายได้ (ชิ้น)' } },
                        // 7. เปลี่ยนชื่อแกน X
                        x: { title: { display: true, text: 'ชื่อสินค้า' } }
                    },
                    plugins: { 
                        legend: { display: false }, // 8. ซ่อน Legend (เพราะมีแท่งเดียว)
                        tooltip: { 
                            // 9. แก้ไข Tooltip ให้แสดง "X ชิ้น"
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += context.parsed.y + ' ชิ้น';
                                    }
                                    return label;
                                }
                            }
                        } 
                    },
                    interaction: { mode: 'index', intersect: false },
                }
            });
        } else {
            // (เหมือนเดิม) ถ้าไม่มีข้อมูล ให้ซ่อนกราฟ
            document.getElementById('salesChart').style.display = 'none';
            document.getElementById('noChartData').style.display = 'block';
        }
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</html>