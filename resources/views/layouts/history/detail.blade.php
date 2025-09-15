<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>รายละเอียดคำสั่งซื้อ #{{ $order->order_id }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
    
    {{-- ✅ [ADD] เพิ่ม CSS สำหรับ Tag โปรโมชั่น --}}
    <style>
        .promo-tag {
            display: inline-block;
            padding: 0.3em 0.6em;
            font-size: 85%;
            font-weight: 600;
            line-height: 1;
            color: #fff;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
            background-color: #17a2b8; /* สี Cyan หรือ Teal */
            margin-right: 5px;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    {{-- ... ส่วนของ Sidebar ... --}}
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
                        <a href="{{ route('history.index') }}" class="nav-link text-white">
                            <i class="nav-icon fas fa-history"></i> <p>ประวัติการขาย</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link text-white active">
                            <i class="nav-icon fas fa-dollar-sign "></i> <p>รายละเอียดการขาย</p>
                        </a>
                    </li>
                    @if(!is_null($order->receive_date))
                    <li class="nav-item">
                        <a href="#" class="nav-link text-white"> {{--  Route::get('order.receipt', ['id' => $order->order_id]) --}}
                            <i class="nav-icon fas fa-receipt"></i> <p>ใบเสร็จ</p>
                        </a>
                    </li>
                    @endif
                </ul>
            </nav>
        </div>
    </aside>

    <div class="content-wrapper">
        <section class="content pt-4">
            <div class="container-fluid">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white">
                        <h4 class="mb-0">รายละเอียดคำสั่งซื้อ #{{ $order->order_id }}</h4>
                    </div>
                    <div class="card-body">
                        {{-- ✅ [RE-LAYOUT] จัด Layout ใหม่ทั้งหมด --}}
                        <div class="row">
                            <div class="col-md-6">
                                <h5>ข้อมูลลูกค้า</h5>
                                <p><strong>ชื่อลูกค้า:</strong> {{ $order->customer->fullname ?? 'ไม่ระบุ' }}</p>
                                <p><strong>เบอร์โทรศัพท์:</strong> {{ $order->customer->cus_tel ?? 'ไม่ระบุ' }}</p>
                                <p><strong>วันที่มารับ:</strong>
                                    @if($order->grab_date)
                                        <span class="text-success font-weight-bold">{{ \Carbon\Carbon::parse($order->grab_date)->format('d/m/Y H:i') }}</span>
                                    @else
                                        <span class="text-muted">ไม่มี</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h5>ข้อมูลการสั่งซื้อ</h5>
                                <p><strong>ชื่อพนักงาน:</strong> {{ $order->employee->em_name ?? 'ไม่มี' }}</p>
                                <p><strong>วันที่สั่งซื้อ:</strong> {{ \Carbon\Carbon::parse($order->order_date)->format('d/m/Y H:i') ?? 'ไม่ระบุ' }}</p>
                                <p><strong>วันที่ทำรายการ:</strong>
                                    @if(is_null($order->receive_date))
                                        <span class="text-danger">กำลังดำเนินการ</span>
                                    @else
                                        {{ \Carbon\Carbon::parse($order->receive_date)->format('d/m/Y H:i') }}
                                    @endif
                                </p>
                                <p><strong>โปรโมชั่นที่ใช้:</strong>
                                    {{-- ✅ [FIX] เปลี่ยน Style การแสดงผลโปรโมชั่น --}}
                                    @forelse($order->promotions as $promo)
                                        <span class="promo-tag">{{ $promo->promo_name }}</span>
                                    @empty
                                        <span class="badge badge-secondary">ไม่มี</span>
                                    @endforelse
                                </p>
                                <p><strong>หมายเหตุ:</strong> {{ $order->remarks ?? 'ไม่มี' }}</p>
                            </div>
                        </div>

                        <hr>
                        <h5>รายการสินค้า</h5>
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light text-center">
                                <tr>
                                    <th>สินค้า</th>
                                    <th>จำนวน</th>
                                    <th>ราคาต่อหน่วย</th>
                                    <th>ราคารวม</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->details as $detail)
                                    <tr>
                                        <td>{{ $detail->product->pro_name ?? 'ไม่ระบุ' }}</td>
                                        <td class="text-center">{{ $detail->amount }}</td>
                                        <td class="text-right">{{ number_format($detail->price_list, 2) }}</td>
                                        <td class="text-right">{{ number_format($detail->pay_total, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        <div class="row mt-4 justify-content-end">
                            <div class="col-md-5">
                                <h5 class="mb-3">สรุปยอดรวม</h5>
                                @php
                                    $totalDiscount = $order->promotions->sum('promo_discount');
                                    $netTotal = $order->price_total;
                                @endphp
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td><strong>ราคารวม (ก่อนหักส่วนลด):</strong></td>
                                        <td class="text-right">{{ number_format($netTotal + $totalDiscount, 2) }} บาท</td>
                                    </tr>
                                    <tr>
                                        <td><strong>ส่วนลดโปรโมชั่น:</strong></td>
                                        <td class="text-right text-danger">-{{ number_format($totalDiscount, 2) }} บาท</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold h5"><strong>ยอดชำระสุทธิ:</strong></td>
                                        <td class="text-right font-weight-bold h5">{{ number_format($netTotal, 2) }} บาท</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end align-items-center my-4">
                    <a href="{{ route('history.index') }}" class="btn btn-secondary mr-2">
                        <i class="fas fa-arrow-left"></i> กลับ
                    </a>
                    
                    @if(is_null($order->receive_date))
                        <form action="#" method="POST" class="d-inline"> {{-- route('order.destroy', ['id' => $order->order_id]) --}}
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบรายการนี้?');">
                                <i class="fas fa-trash"></i> ลบรายการสั่งซื้อ
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </section>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>