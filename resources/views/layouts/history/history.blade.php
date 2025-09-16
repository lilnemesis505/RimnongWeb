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
                                <td>{{ $orders->firstItem() + $index }}</td> <td>{{ $order->order_id }}</td>
                                <td>
                                    {{-- ✅ [FIX] แก้ไขตรรกะการคำนวณราคาทั้งหมด --}}
                                    @php
                                        // คำนวณส่วนลดทั้งหมดจากทุกโปรโมชั่นที่เกี่ยวข้องกับ Order นี้
                                        $totalDiscount = $order->promotions->sum('promo_discount');
                                        // price_total คือราคาสุทธิหลังหักส่วนลดแล้ว
                                        $netTotal = $order->price_total;
                                        // คำนวณราคาเต็มก่อนหักส่วนลด
                                        $originalPrice = $netTotal + $totalDiscount;
                                    @endphp

                                    @if($totalDiscount > 0)
                                        {{-- ถ้ามีส่วนลด ให้ขีดฆ่าราคาเต็ม และแสดงราคาสุทธิ --}}
                                        <del class="text-muted-del">{{ number_format($originalPrice, 2) }}</del><br>
                                        <strong>{{ number_format($netTotal, 2) }}</strong>
                                    @else
                                        {{-- ถ้าไม่มีส่วนลด ก็แสดงราคาปกติ --}}
                                        <strong>{{ number_format($netTotal, 2) }}</strong>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($order->order_date)->format('d/m/Y H:i') }}</td>
                               <td>
    @if(is_null($order->em_id))
        <span class="badge badge-danger">ยังไม่ถูกรับรายการ</span>
    @elseif(!is_null($order->grab_date))
        <span class="badge badge-primary">ได้รับสินค้าแล้ว</span>
    @elseif(!is_null($order->receive_date))
        <span class="badge badge-success">เตรียมสินค้าเสร็จสิ้น</span>
    @else
        <span class="badge badge-warning">กำลังดำเนินการ</span>
    @endif
</td>
                                <td>
                                    @if(!empty($order->slips_url))
                                        <a href="{{ $order->slips_url }}" target="_blank">
                                            <img src="{{ $order->slips_url }}" alt="Slip" style="width: 50px; height: 50px; object-fit: cover;">
                                        </a>
                                    @else
                                        <span>ไม่มีสลิป</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('order.details', ['id' => $order->order_id]) }}" class="btn btn-info btn-sm"> <i class="fas fa-eye"></i> แสดงรายละเอียด
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
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
