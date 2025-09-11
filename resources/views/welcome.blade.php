<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>หน้าหลัก</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
    <style>
        .info-box {
            display: flex;
            align-items: center;
        }
        .info-box .info-box-icon {
            font-size: 2rem;
            width: 70px;
            height: 70px;
            border-radius: 0.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .info-box .info-box-content {
            flex: 1;
            padding: 0 10px;
        }
        .info-box .info-box-text {
            display: block;
            font-size: 1rem;
            font-weight: 400;
        }
        .info-box .info-box-number {
            display: block;
            font-size: 1.5rem;
            font-weight: 700;
        }
        /* Style for the new collapsible menu */
        .sidebar .nav-item.has-treeview > .nav-link {
            border-radius: 5px;
        }
        .sidebar .nav-treeview .nav-item .nav-link {
            padding-left: 30px;
            font-size: 0.95rem;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-footer-fixed">
<div class="wrapper">
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item ">
                <a href="#" class="nav-link">ระบบจัดการร้าน ริมหนองคาเฟ่</a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                    <i class="fas fa-expand-arrows-alt"></i>
                </a>
            </li>
        </ul>
    </nav>
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="#" class="brand-link">
        <span class="brand-text font-weight-light">{{ session('admin_fullname') }}</span>
    </a>
    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column">
                <li class="nav-item">
                    <a href="#" class="nav-link active"><i class="nav-icon fas fa-home-alt"></i> <p>หน้าหลัก</p></a>
                </li>
            </ul>
            <hr style="border-top: 1px solid #fff;">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>
                            จัดการข้อมูลระบบ
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('product.index') }}" class="nav-link"><i class="nav-icon fas fa-shopping-cart"></i> <p>จัดการข้อมูลสินค้า</p></a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('employee.index') }}" class="nav-link"><i class="nav-icon fas fa-user"></i> <p>จัดการข้อมูลพนักงาน</p></a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('customer.index') }}" class="nav-link"><i class="nav-icon fas fa-users"></i> <p>ข้อมูลลูกค้า</p></a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('stock.index') }}" class="nav-link"><i class="nav-icon fas fa-box"></i> <p>จัดการข้อมูลล็อตสินค้า</p></a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('promotion.index') }}" class="nav-link"><i class="nav-icon fas fa-ticket"></i> <p>จัดการข้อมูลโปรโมชั่น</p></a>
                        </li>
                    </ul>
                </li>
            </ul>
            <hr style="border-top: 1px solid #fff;">
            <ul class="nav nav-pills nav-sidebar flex-column">
                <li class="nav-item">
                    <a href="{{ route('history.index') }}" class="nav-link"><i class="nav-icon fas fa-history"></i> <p>ข้อมูลการสั่งซื้อสินค้า</p></a>
                </li>
            </ul>
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-chart-line"></i>
                        <p>
                            รายงาน
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('salereport.index') }}" class="nav-link"><i class="nav-icon fas fa-chart-bar"></i> <p>รายงานการขาย</p></a>
                        </li>
                    </ul>
                </li>
            </ul>
            <hr style="border-top: 1px solid #fff;">
            <ul class="nav nav-pills nav-sidebar flex-column">
                <li class="nav-item">
                    <a href="#" class="nav-link" data-toggle="modal" data-target="#logoutModal"><i class="nav-icon fas fa-sign-out-alt"></i> <p>ออกจากระบบ</p></a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
    <div class="content-wrapper p-3">
        <section class="content pt-4">
            <div class="container-fluid">
                <h4 class="mb-4">สรุปภาพรวม</h4>
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="info-box bg-info">
                            <span class="info-box-icon"><i class="fas fa-users"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">จำนวนลูกค้าทั้งหมด</span>
                                <span class="info-box-number">{{ $customerCount }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="info-box bg-success">
                            <span class="info-box-icon"><i class="fas fa-user-tie"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">จำนวนพนักงาน</span>
                                <span class="info-box-number">{{ $employeeCount }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="info-box bg-secondary">
                            <span class="info-box-icon"><i class="fas fa-box-open"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">จำนวนสินค้าทั้งหมด</span>
                                <span class="info-box-number">{{ $productCount }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="info-box bg-warning">
                            <span class="info-box-icon"><i class="fas fa-money-bill-wave"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">ยอดขายรวมทั้งหมด</span>
                                <span class="info-box-number">{{ number_format($totalSales, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card card-warning card-outline">
                            <div class="card-header">
                                <h3 class="card-title">วัตถุดิบใกล้หมดอายุ</h3>
                            </div>
                            <div class="card-body p-0">
                                <ul class="products-list product-list-in-card pl-2 pr-2">
                                    @forelse($expiringStock as $stock)
                                    <li class="item">
                                        <div class="product-img">
                                            <i class="fas fa-box text-warning"></i>
                                        </div>
                                        <div class="product-info">
                                            <a href="{{ route('stock.index') }}" class="product-title">{{ $stock->mat_name }}
                                                <span class="badge badge-warning float-right">{{ $stock->remain }} ชิ้น</span>
                                            </a>
                                            <span class="product-description">
                                                เหลืออีก {{ Carbon::now()->diffInDays($stock->exp_date, false) }} วัน
                                            </span>
                                        </div>
                                    </li>
                                    @empty
                                    <li class="item text-center">
                                        <div class="product-info mt-2 mb-2">
                                            <span>ไม่มีวัตถุดิบใกล้หมดอายุ</span>
                                        </div>
                                    </li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card card-danger card-outline">
                            <div class="card-header">
                                <h3 class="card-title">โปรโมชั่นที่ใช้งานอยู่</h3>
                            </div>
                            <div class="card-body p-0">
                                <ul class="products-list product-list-in-card pl-2 pr-2">
                                    @forelse($activePromotions as $promo)
                                    <li class="item">
                                        <div class="product-img">
                                            <i class="fas fa-ticket-alt text-danger"></i>
                                        </div>
                                        <div class="product-info">
                                            <a href="{{ route('promotion.index') }}" class="product-title">{{ $promo->promo_name }}
                                                <span class="badge badge-danger float-right">เหลืออีก {{ $promo->days_left }} วัน</span>
                                            </a>
                                            <span class="product-description">
                                                ส่วนลด {{ number_format($promo->promo_discount, 2) }} บาท
                                            </span>
                                        </div>
                                    </li>
                                    @empty
                                    <li class="item text-center">
                                        <div class="product-info mt-2 mb-2">
                                            <span>ไม่มีโปรโมชั่นที่ใช้งานอยู่</span>
                                        </div>
                                    </li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<footer class="main-footer bg-secondary text-center fixed-bottom">
    <strong>&copy; {{ date('Y') }} ร้านริมหนอง คาเฟ่ จังหวัดเชียงใหม่.</strong> กินกาแฟให้อร่อย
</footer>

<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-warning">
        <h5 class="modal-title" id="logoutModalLabel">ยืนยันการออกจากระบบ</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        คุณแน่ใจหรือไม่ว่าต้องการออกจากระบบ?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-danger">ออกจากระบบ</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>