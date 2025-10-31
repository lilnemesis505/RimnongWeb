<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Promotion Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
    <style>
        .text-muted-del {
            color: #dc3545; /* สีแดง */
        }
    </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

    {{-- ... (Navbar and Sidebar เหมือนเดิม) ... --}}
@include('layouts.assets._navbar')

    {{-- Sidebar --}}
@include('layouts.assets._sidebar')

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
                                    {{-- ✅ [FIX] แก้ไข Logic การคำนวณทั้งหมด --}}
                                    @php
                                        // 1. ยอดสุทธิ (Net Total) (อันนี้ถูกต้องอยู่แล้ว)
                                        $netTotal = $order->price_total;
                                        
                                        $originalPrice = 0;
                                        
                                        // 2. คำนวณ "ราคาเต็ม"
                                        // (*** Controller ที่เรียกหน้านี้ ต้อง .with('details.product') มาด้วย ***)
                                        if ($order->relationLoaded('details') && $order->details) {
                                            foreach ($order->details as $detail) {
                                                if ($detail->relationLoaded('product') && $detail->product) {
                                                    
                                                    // ✅ [FIX] เปลี่ยนจาก pro_price เป็น price
                                                    $originalPrice += $detail->product->price * $detail->amount;

                                                } else {
                                                    $originalPrice = $netTotal; // Fallback
                                                    break;
                                                }
                                            }
                                        } else {
                                            $originalPrice = $netTotal; // Fallback
                                        }

                                        // 3. ส่วนลดที่แท้จริง = ราคาเต็ม - ยอดสุทธิ
                                        $totalDiscount = $originalPrice - $netTotal;
                                    @endphp

                                    @if($totalDiscount > 0.01) {{-- เทียบค่า float --}}
                                        <del class="text-muted-del">{{ number_format($originalPrice, 2) }}</del><br>
                                        <strong>{{ number_format($netTotal, 2) }}</strong>
                                    @else
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

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>