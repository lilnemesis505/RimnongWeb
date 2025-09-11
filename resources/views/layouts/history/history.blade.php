<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Promotion Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
    <style>
        .text-muted-del {
            color: #6c757d;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <span class="navbar-brand">ข้อมูลการสั่งซื้อ</span>
    </nav>

    <aside class="main-sidebar sidebar-dark-primary elevation-4">
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
                        <a href="{{ route('promotion.index') }}" class="nav-link bg-primary text-white">
                            <i class="nav-icon fas fa-history"></i> <p>ข้อมูลการสั่งซื้อ</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link text-white">
                            <i class="nav-icon fas fa-dollar-sign"></i> <p>รายละเอียดการขาย</p>
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
                        <h4 class="mb-0">📜 ข้อมูลการสั่งซื้อ</h4>
                    </div>
                    <div class="card-body">
                       @if($orders->isEmpty())
    <div class="alert alert-info text-center">ยังไม่มีรายการขายในระบบ</div>
@else
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-secondary text-center">
                <tr>
                    <th>#</th>
                    <th>รหัสคำสั่งซื้อ</th>
                    <th>ราคารวม</th>
                    <th>วันที่สั่งซื้อ</th>
                    <th>สถานะ</th>
                    <th>สลิป</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody>
    @foreach($orders as $index => $order)
        <tr class="text-center">
            <td>{{ $index + 1 }}</td>
            <td>{{ $order->order_id }}</td>
            <td>
                @php
                    $discount = $order->promotion->promo_discount ?? 0;
                    $oldPrice = $order->price_total + $discount; // คำนวณราคาเดิม
                    $netTotal = $order->price_total;
                @endphp
                @if($discount > 0)
                    <del class="text-muted-del">{{ number_format($oldPrice, 2) }}</del><br>
                    <strong>{{ number_format($netTotal, 2) }}</strong>
                @else
                    {{ number_format($netTotal, 2) }}
                @endif
            </td>
            <td>{{ $order->order_date }}</td>
            <td>
                @if(is_null($order->em_id))
                    <span class="badge badge-danger">ยังไม่ถูกรับรายการ</span>
                @elseif(is_null($order->receive_date))
                    <span class="badge badge-warning">กำลังดำเนินการ</span>
                @else
                    <span class="badge badge-success">สำเร็จรายการ</span>
                @endif
            </td>
         <td>
    @php
        $slipPath = 'storage/app/public/slips/slip_' . $order->order_id . '.jpg';
    @endphp
    @if(file_exists(public_path($slipPath)))
        <a href="{{ url($slipPath) }}" target="_blank">
            <img src="{{ url($slipPath) }}" alt="Slip" style="width: 50px;">
        </a>
    @else
        <span>ไม่มีสลิป</span>
    @endif
</td>
            <td>
                <a href="{{ route('order.details', ['id' => $order->order_id]) }}" class="btn btn-info btn-sm">
    <i class="fas fa-eye"></i> แสดงรายละเอียด
</a>
            </td>
        </tr>
    @endforeach
</tbody>
        </table>
        {{ $orders->links() }}
    </div>
@endif
                    </div>
                </div>
            </div>
        </section>

        </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
