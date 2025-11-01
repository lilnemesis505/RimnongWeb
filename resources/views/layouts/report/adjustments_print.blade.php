<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>พิมพ์รายงานการปรับยอด</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <style>
        body { margin: 20px; }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0,0,0,.05);
        }
        .text-success { color: #28a745 !important; }
        .text-danger { color: #dc3545 !important; }
    </style>
</head>
<body>
    
    <h2>รายงานการปรับยอด</h2>
    <p>พิมพ์เมื่อวันที่: {{ now()->format('d/m/Y H:i') }}</p>

    <table class="table table-bordered table-striped">
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
                    <td>{{ $item->stockMat->mat_name ?? 'N/A' }}</td>
                    
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
                        ยังไม่มีข้อมูลการปรับยอด
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>