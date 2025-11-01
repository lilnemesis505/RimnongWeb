<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ข้อมูลล็อตสินค้า </title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
    <style>
        .table-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
            background-color: #eee;
        }
        .table th.text-center, .table td.text-center {
            text-align: center;
            vertical-align: middle; 
        }
        .table th.text-right, .table td.text-right {
            text-align: right;
            vertical-align: middle;
        }
        .table td {
             vertical-align: middle;
        }
        .pagination {
            justify-content: center;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
@include('layouts.assets._navbar')
   @include('layouts.assets._sidebar')
    <div class="content-wrapper p-3" style="min-height: 100vh;">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-boxes mr-2"></i>ข้อมูลล็อตสินค้า</h3>
            </div>
            
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th class="text-center" style="width: 5%;">#</th>
                                <th class="text-center" style="width: 80px;">รูปภาพ</th> 
                                <th>ชื่อวัสดุ</th>
                                <th>ประเภท</th>
                                <th class="text-center">วันที่นำเข้า</th> <th class="text-center">จำนวนที่นำเข้า(ล่าสุด)</th> <th class="text-center">วันหมดอายุ</th>
                                <th class="text-center">ยอดคงเหลือ</th> <th class="text-right">ราคาต่อหน่วย</th>
                                <th class="text-center">สถานะ</th>
                                <th class="text-center">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stock_mats as $i => $mat)
                            <tr>
                                <td class="text-center">{{ $stock_mats->firstItem() + $i }}</td>
                                <td class="text-center">
                                    @if($mat->image)
                                        <img src="{{ $mat->image }}?tr=w-50,h-50,fo-auto" alt="{{ $mat->mat_name }}" class="table-image">
                                    @else
                                        <span class="fa-stack fa-lg">
                                          <i class="fas fa-camera fa-stack-1x"></i>
                                          <i class="fas fa-ban fa-stack-2x text-danger" style="opacity: 0.7"></i>
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $mat->mat_name }}</td>
                                <td>{{ $mat->type->type_name ?? '-' }}</td>
                                <td class="text-center">{{ $mat->import_date ? \Carbon\Carbon::parse($mat->import_date)->format('m/d/Y') : '-' }}</td>
                                <td class="text-center">{{ $mat->quantity }}</td>
                                <td class="text-center">{{ $mat->exp_date ? \Carbon\Carbon::parse($mat->exp_date)->format('m/d/Y') : '-' }}</td>
                                <td class="text-center">{{ $mat->remain }}</td>
                                <td class="text-right">{{ number_format($mat->unitcost, 2) }}</td>
                                
                                <td class="text-center">
                                    @if($mat->status == 2)
                                        <span class="badge badge-warning">รอของเข้า</span>
                                    @elseif($mat->remain > 0)
                                        <span class="badge badge-success">ปกติ</span>
                                    @else
                                        <span class="badge badge-danger">หมด</span>
                                    @endif
                                </td>
                                
                                <td class="text-center">
                                    <a href="{{ route('stock.edit', $mat->mat_id) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> แก้ไข
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer clearfix">
                {{ $stock_mats->links() }}
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>