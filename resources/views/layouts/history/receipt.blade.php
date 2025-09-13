<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ใบเสร็จรับเงิน #{{ $receipt->re_id }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
    <style>
        .receipt-container {
            max-width: 400px;
            margin: 20px auto;
            border: 1px dashed #ccc;
            padding: 20px;
            font-family: 'Courier New', Courier, monospace;
            background-color: #fff;
        }
        .receipt-header, .receipt-footer {
            text-align: center;
        }
        .item-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        /* ซ่อนปุ่มและ sidebar เมื่อพิมพ์ */
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <aside class="main-sidebar sidebar-dark-primary elevation-4 min-vh-100 no-print">
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
                        <a href="{{ route('order.details', ['id' => $order->order_id]) }}" class="nav-link text-white">
                            <i class="nav-icon fas fa-dollar-sign"></i> <p>รายละเอียดการขาย</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('order.receipt', ['id' => $order->order_id]) }}" class="nav-link text-white active">
                            <i class="nav-icon fas fa-receipt"></i> <p>ใบเสร็จ</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <div class="content-wrapper">
        <section class="content pt-4">
            <div class="container-fluid">
                <div class="receipt-container">
                    <div class="receipt-header mb-4">
                        <h4 class="mb-0">ริมหนองคาเฟ่</h4>
                        <p class="text-muted mb-0">ใบเสร็จรับเงิน</p>
                        <hr class="my-2">
                        <p><strong>คำสั่งซื้อเลขที่:</strong> {{ $order->order_id ?? 'ไม่ระบุ' }}</p>
                        <p><strong>วันที่:</strong> {{ $order->receive_date }}</p>
                    </div>

                    <div class="receipt-body">
                        @foreach($order->details as $detail)
                            <div class="item-row">
                                <span>{{ $detail->product->pro_name ?? 'ไม่ระบุ' }} (x{{ $detail->amount }})</span>
                                <span>{{ number_format($detail->pay_total, 2) }} บาท</span>
                            </div>
                        @endforeach
                    </div>
                    
                    <hr class="my-2">

                    <div class="d-flex justify-content-between mb-1">
                        <span>ราคารวมสินค้า:</span>
                        <span>{{ number_format($order->details->sum('pay_total'), 2) }} บาท</span>
                    </div>

                    <div class="d-flex justify-content-between mb-1">
                        <span>ส่วนลด ({{ $order->promotion->promo_name ?? 'ไม่มี' }}):</span>
                        <span>-{{ number_format($order->promotion->promo_discount ?? 0, 2) }} บาท</span>
                    </div>

                    <div class="d-flex justify-content-between font-weight-bold mb-2">
                        <span>ราคารวมสุทธิ:</span>
                        <span>{{ number_format($receipt->price_total ?? 0, 2) }} บาท</span>
                    </div>
                    
                    <hr class="my-2">

                   <div class="receipt-footer mt-4">
                <p class="text-sm mb-1">
                 <strong>พนักงาน:</strong> {{ $order->employee->em_name ?? 'ไม่ระบุ' }}
                 </p>
                    <p class="text-sm mb-1">
                  <strong>เบอร์โทร:</strong> {{ $order->employee->em_tel ?? 'ไม่ระบุ' }}
                </p>
                    <p class="mt-3">ขอบคุณที่ใช้บริการ</p>
                </div>
                </div>
            </div>
        </section>
    </div>
</div>
<div class="d-flex justify-content-end align-items-center mb-4 mr-4 no-print">
    <a href="{{ route('history.index') }}" class="btn btn-secondary mr-2">
        <i class="fas fa-arrow-left"></i> กลับ
    </a>
    <button onclick="window.print()" class="btn btn-primary">
        <i class="fas fa-print"></i> พิมพ์ใบเสร็จ
    </button>
</div>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>