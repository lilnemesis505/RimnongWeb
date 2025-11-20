<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>พิมพ์รายงานการเบิกวัตถุดิบ</title>
    {{-- ใช้ CSS ของ AdminLTE เพื่อให้ตารางสวย --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <style>
        body { margin: 20px; }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0,0,0,.05);
        }
    </style>
</head>
<body>
    
    <h2>รายงานการเบิกวัตถุดิบ</h2>
    <p>พิมพ์เมื่อวันที่: {{ now()->format('d/m/Y H:i') }}</p>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th style="width: 10%;">#รหัสการเบิก</th>
                <th>ชื่อแอดมิน</th>
                <th>สินค้าที่เบิก</th>
                <th class="text-center">จำนวน</th>
                <th class="text-center">วันที่</th>
            </tr>
        </thead>
        <tbody>
            @forelse($withdrawals as $item)
                <tr>
                    <td>#{{ $item->withdrawal_id }}</td>
                    <td>{{ $item->admin->fullname ?? 'N/A' }}</td>
                    <td>{{ $item->stockMaterial->mat_name ?? 'N/A' }}</td>
                    <td class="text-center">{{ $item->withdraw_amount }}</td>
                    <td class="text-center">{{ $item->withdraw_date->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted p-4">
                        ยังไม่มีข้อมูลการเบิก
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- สั่งพิมพ์อัตโนมัติเมื่อโหลดหน้านี้ --}}
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>