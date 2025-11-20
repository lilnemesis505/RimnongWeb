<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AdminLTE Template</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
    
    <style>
        .table th.text-center, .table td.text-center {
            text-align: center;
        }
        .table th.text-right, .table td.text-right {
            text-align: right;
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
                <h3 class="card-title"><i class="fas fa-users mr-2"></i>ข้อมูลลูกค้า</h3>
            </div>
            
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 5%;">#</th>
                                <th>ชื่อ-สกุล</th>
                                <th>Username</th>
                                <th>เบอร์โทร</th>
                                <th>Email</th>
                                <th class="text-right" style="width: 20%;">ยอดสั่งซื้อรวม (บาท)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customers as $i => $customer)
                            <tr>
                                <td class="text-center">{{ $customers->firstItem() + $i }}</td>
                                <td>{{ $customer->fullname }}</td>
                                <td>{{ $customer->username }}</td>
                                <td>{{ $customer->cus_tel }}</td>
                                <td>{{ $customer->email }}</td>
                                
                                {{-- [แก้ไข] 👈 เปลี่ยนจาก 'orders_sum_price_total' เป็น 'receipts_sum_price_total' --}}
                                <td class="text-right">{{ number_format($customer->receipts_sum_price_total ?? 0, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer clearfix">
                {{ $customers->links() }}
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>