<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ข้อมูลสินค้า</title>

    <!-- AdminLTE CSS via CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">

        <form action="{{ route('product.filter') }}" method="GET" class="form-inline ml-3">
    <div class="input-group input-group-sm">
        <select name="type_id" class="form-control">
            <option value="">-- ทุกประเภท --</option>
            @foreach($types as $type)
                <option value="{{ $type->type_id }}" {{ request('type_id') == $type->type_id ? 'selected' : '' }}>
                    {{ $type->type_name }}
                </option>
            @endforeach
        </select>
        <div class="input-group-append">
            <button class="btn btn-primary btn-sm" type="submit">
                <i class="fas fa-filter"></i> 
            </button>
        </div>
    </div>
</form>
    </nav>

    <!-- Sidebar -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="#" class="brand-link">
            <span class="brand-text font-weight-light">{{ session('admin_fullname') }}</span>
        </a>
        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column">
                    <li class="nav-item">
                        <a href="{{ route('welcome') }}" class="nav-link text-white">
                            <i class="nav-icon fas fa-home-alt"></i>
                            <p>หน้าหลัก</p>
                        </a>
                    </li>
                </ul>
                <hr style="border-top: 1px solid #fff;">
                <ul class="nav nav-pills nav-sidebar flex-column">
                    <li class="nav-item">
                        <a href="#" class="nav-link active" >
                            <i class="nav-icon fas fa-shopping-cart"></i>
                            <p>ข้อมูลสินค้า</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('product.add') }}" class="nav-link text-white">
                            <i class="nav-icon fas fa-plus"></i>
                            <p>เพิ่มข้อมูลสินค้า</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link text-white">
                        <i class="nav-icon fas fa-gear"></i>
                        <p>แก้ไขข้อมูลสินค้า</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('protype.add') }}" class="nav-link text-white" >
                            <i class="nav-icon fas fa-chart-bar"></i>
                            <p>ประเภทสินค้า</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <!-- Content Wrapper -->
    <div class="content-wrapper p-3">
    <div class="container-fluid">
        <div class="row">
            @foreach($products as $product)
                <div class="col-6 col-md-3 col-lg-2 mb-3">
                    <div class="card h-100 d-flex flex-column" style="border: 1px solid #ccc; min-height: 380px;">
                        @php
                            $extensions = ['jpg', 'jpeg', 'png', 'gif', 'svg'];
                            $imagePath = null;

                            foreach ($extensions as $ext) {
                                $path = 'storage/products/' . $product->pro_id . '.' . $ext;
                                if (file_exists(public_path($path))) {
                                    $imagePath = asset($path);
                                    break;
                                }
                            }
                        @endphp

                       <img src="{{ $product->image_path }}" class="card-img-top" alt="รูปสินค้า"style="height: 300px; width: 100%; object-fit: cover;">


                        <div class="card-body mt-auto p-2">
                            <h5 class="card-title mb-1">{{ $product->pro_name }}</h5>
                            <p class="card-text mb-1">ราคา: {{ number_format($product->price, 2) }} บาท</p>
                            @if($product->type)
                                <p class="card-text">
                                    <small class="text-muted">ประเภท: {{ $product->type->type_name }}</small>
                                </p>
                            @endif
                        </div>

                        <div class="card-footer bg-transparent border-top-0 d-flex justify-content-start p-2">
                            <a href="{{ route('product.edit', ['product' => $product->pro_id ]) }}"
                               class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i> แก้ไข
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $products->links() }}
        </div>
    </div>
</div>
</div>

<!-- AdminLTE JS via CDN -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
