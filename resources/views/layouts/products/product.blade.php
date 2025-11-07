<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ข้อมูลสินค้า</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

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

    @include('layouts.assets._sidebar')

    <div class="content-wrapper p-3">
    <div class="container-fluid">
        <div class="row">
            @foreach($products as $product)
                <div class="col-6 col-md-3 col-lg-2 mb-3">
                    <div class="card h-100 d-flex flex-column" style="border: 1px solid #ccc; min-height: 380px;">
                        
                        <img src="{{ $product->image }}" class="card-img-top" alt="รูปสินค้า" style="height: 300px; width: 100%; object-fit: cover;">


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

        <div class="d-flex justify-content-center">
            {{ $products->links() }}
        </div>
    </div>
</div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>