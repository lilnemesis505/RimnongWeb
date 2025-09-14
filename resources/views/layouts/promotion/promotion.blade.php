<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Promotion Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    {{-- Navbar and Sidebar --}}
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <span class="navbar-brand">ข้อมูลโปรโมชั่น</span>
    </nav>
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="#" class="brand-link">
            <span class="brand-text font-weight-light">{{ session('admin_fullname') }}</span>
        </a>
        <div class="sidebar">
            <nav class="mt-2">
                 <ul class="nav nav-pills nav-sidebar flex-column">
                    <li class="nav-item">
                        <a href="{{ route('welcome') }}" class="nav-link text-white"><i class="nav-icon fas fa-home-alt"></i> <p>หน้าหลัก</p></a>
                    </li>
                </ul>
                <hr class="bg-white">
                <ul class="nav nav-pills nav-sidebar flex-column">
                    <li class="nav-item">
                        <a href="{{ route('promotion.index') }}" class="nav-link active"><i class="nav-icon fas fa-ticket"></i> <p>ข้อมูลโปรโมชั่น</p></a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('promotion.add') }}" class="nav-link text-white"><i class="nav-icon fas fa-plus"></i> <p>เพิ่มโปรโมชั่น</p></a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <div class="content-wrapper p-3" style="min-height: 100vh;">
        <div class="card">
            <div class="card-header"><h3 class="card-title">ข้อมูลโปรโมชั่น</h3></div>
            <div class="card-body p-0">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ชื่อโปรโมชั่น</th>
                            <th>สินค้าที่ร่วมรายการ</th> {{-- <-- คอลัมน์ใหม่ --}}
                            <th>ส่วนลด (บาท)</th>
                            <th>วันที่เริ่ม</th>
                            <th>วันที่สิ้นสุด</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($promotions as $promotion)
                        <tr>
                            <td>{{ $loop->iteration + ($promotions->currentPage() - 1) * $promotions->perPage() }}</td>
                            <td>{{ $promotion->promo_name }}</td>
                            {{-- ใช้ relationship ที่เราสร้างไว้ --}}
                            <td>{{ $promotion->product->pro_name ?? 'N/A' }}</td>
                            <td>{{ number_format($promotion->promo_discount, 2) }}</td>
                            <td>{{ $promotion->promo_start }}</td>
                            <td>{{ $promotion->promo_end }}</td>
                            <td>
                                <form action="{{ route('promotion.delete', $promotion->promo_id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('คุณแน่ใจว่าจะลบโปรโมชั่นนี้?')">
                                        <i class="fas fa-trash"></i> ลบ
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer clearfix">{{ $promotions->links() }}</div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>