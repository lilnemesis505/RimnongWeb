<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>รายงานการปรับยอด</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
    {{-- [เพิ่ม] CSS สำหรับสีเขียว/แดง --}}
    <style>
        .text-success { color: #28a745 !important; }
        .text-danger { color: #dc3545 !important; }
    </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    {{-- (Navbar - คัดลอกจากไฟล์ reportbill.blade.php มาได้เลย) --}}
   @include('layouts.assets._navbar')
    
    {{-- (Sidebar - คัดลอก Sidebar มา แล้วแก้ 'active' ให้ถูกหน้า) --}}
@include('layouts.assets._sidebar')

    <div class="content-wrapper p-3">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>รายงานการปรับยอด</h1>
                    </div>
                </div>
            </div>
        </section>
        
        <section class="content">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">ประวัติการปรับยอดทั้งหมด</h3>
                    
                    <a href="{{ route('report.adjustments.print') }}" target="_blank" class="btn btn-primary btn-sm float-right">
                        <i class="fas fa-print"></i> พิมพ์รายงาน
                    </a>
                </div>
                
                <div class="card-body p-0">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th style="width: 10%;">#รหัส</th>
                                <th>ชื่อแอดมิน</th>
                                <th>สินค้า</th>
                                <th class="text-center">จำนวน (เพิ่ม/ลด)</th>
                                <th class="text-center">วันที่</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($adjustments as $item)
                                <tr>
                                    <td>#{{ $item->adjustment_id }}</td>
                                    <td>{{ $item->admin->fullname ?? 'N/A' }}</td>
                                    <td>
                                        {{-- (Model ของคุณใช้ stock_mat_id) --}}
                                        <a href="{{ route('stock.edit', $item->stock_mat_id) }}">
                                            {{ $item->stockMat->mat_name ?? 'N/A' }}
                                        </a>
                                    </td>
                                    
                                    {{-- [เพิ่ม] Logic แสดงสีเขียว/แดง --}}
                                    @if($item->amount > 0)
                                        <td class="text-center text-success font-weight-bold">
                                            +{{ $item->amount }}
                                        </td>
                                    @else
                                        <td class="text-center text-danger font-weight-bold">
                                            {{ $item->amount }}
                                        </td>
                                    @endif
                                    
                                    <td class="text-center">{{ $item->adjust_date->format('d/m/Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted p-4">
                                        <i class="fas fa-info-circle"></i> ยังไม่มีข้อมูลการปรับยอด
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="card-footer clearfix">
                    {{ $adjustments->links() }}
                </div>
            </div>
        </section>
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
</div>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>