<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>รายละเอียดคำสั่งซื้อ #{{ $order->order_id }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
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
                        <a href="{{ route('history.index') }}" class="nav-link text-white">
                            <i class="nav-icon fas fa-history"></i> <p>ประวัติการขาย</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link text-white active">
                            <i class="nav-icon fas fa-dollar-sign "></i> <p>รายละเอียดการขาย</p>
                        </a>
                    </li>
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                     @if(!is_null($order->receive_date))
                      <li class="nav-item">
                 <a href="{{ route('order.receipt', ['id' => $order->order_id]) }}" class="nav-link text-white">
                      <i class="nav-icon fas fa-receipt"></i> <p>ใบเสร็จ</p>
                        </a>
                </li>
             @endif
                </ul>
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
                        <div class="row">
                            <div class="col-md-6">
                                <h5>ข้อมูลลูกค้า</h5>
                                <p><strong>ชื่อลูกค้า:</strong> {{ $order->customer->fullname ?? 'ไม่ระบุ' }}</p>
                                <p><strong>เบอร์โทรศัพท์:</strong> {{ $order->customer->cus_tel ?? 'ไม่ระบุ' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h5>ข้อมูลการสั่งซื้อ</h5>
                                <p><strong>ชื่อพนักงาน:</strong> {{ $order->employee->em_name ?? 'ไม่มี' }}</p>
                                <p><strong>โปรโมชั่นที่ใช้:</strong> {{ $order->promotion->promo_name ?? 'ไม่มี' }}</p>
                                <p><strong>หมายเหตุ:</strong> {{ $order->remarks ?? 'ไม่มี' }}</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>วันที่สั่งซื้อ:</strong> {{ $order->order_date ?? 'ไม่ระบุ' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>วันที่ทำรายการ:</strong>
                                    @if(is_null($order->receive_date))
                                        <span class="text-danger">กำลังดำเนินการ</span>
                                    @else
                                        {{ $order->receive_date }}
                                    @endif
                                </p>
                            </div>
                        </div>

                        <hr>
                        <h5>รายการสินค้า</h5>
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
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
                                        <td>{{ $detail->amount }}</td>
                                        <td>{{ number_format($detail->price_list, 2) }}</td>
                                        <td>{{ number_format($detail->pay_total, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        <div class="d-flex justify-content-end">
                            <div class="col-md-4">
                                @php
                                    // คำนวณราคารวมของสินค้าทั้งหมดก่อนหักส่วนลด
                                    $subtotal = $order->details->sum('pay_total');

                                    // ดึงส่วนลดจากโปรโมชั่น ถ้ามี
                                    $discount = $order->promotion->promo_discount ?? 0;

                                    // คำนวณราคารวมสุทธิ
                                    $netTotal = $subtotal - $discount;
                                @endphp
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td><strong>ราคารวมสินค้า:</strong></td>
                                        <td class="text-right">{{ number_format($subtotal, 2) }} บาท</td>
                                    </tr>
                                    <tr>
                                        <td><strong>ส่วนลด ({{ $order->promotion->promo_name ?? 'ไม่มี' }}):</strong></td>
                                        <td class="text-right">-{{ number_format($discount, 2) }} บาท</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold"><strong>ราคารวมสุทธิ:</strong></td>
                                        <td class="text-right font-weight-bold">{{ number_format($netTotal, 2) }} บาท</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="d-flex justify-content-end align-items-center mb-4 mr-4">
        <a href="{{ route('history.index') }}" class="btn btn-secondary mr-2">
            <i class="fas fa-arrow-left"></i> กลับ
        </a>
        
        @if(is_null($order->em_id))
            <form action="{{ route('order.destroy', ['id' => $order->order_id]) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบรายการนี้?');">
                    <i class="fas fa-trash"></i> ลบรายการสั่งซื้อ
                </button>
            </form>
        @endif
    </div>
</div>
<script>
    // ตรวจสอบว่ามีข้อความ Error ใน Session หรือไม่
    const errorMessage = "{{ session('error') }}";
    
    // ถ้ามีข้อความ Error ให้แสดงใน Console
    if (errorMessage) {
        console.error('Application Error:', errorMessage);
        // สามารถเพิ่ม alert เพื่อแจ้งผู้ใช้ก็ได้
        // alert(errorMessage);
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
</body>
</html>