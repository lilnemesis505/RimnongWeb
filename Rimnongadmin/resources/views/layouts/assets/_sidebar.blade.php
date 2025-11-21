<style>
    /* 1. (ลบออก) ไม่จำเป็นต้องมีพื้นหลัง body สำหรับธีมสีทึบ */
    /* 2. (ลบออก) ไม่จำเป็นต้องทำให้ content-wrapper โปร่งใส */

    /* 3. (สำคัญ) ตั้งค่า Sidebar เป็นสีน้ำตาล Pastel แบบทึบ */
    aside.main-sidebar {
        /* ⭐️ นี่คือสีน้ำตาล Pastel (Tan) ⭐️ */
        background-color: #473c2eff !important; 
    }

    /* 4. ทำให้หัวโลโก้ (BrandLink) โปร่งใส (แต่เปลี่ยนเส้นขอบ) */
    .brand-link {
        background-color: transparent !important;
        /* ⭐️ เปลี่ยนสีเส้นขอบเป็นน้ำตาลเข้มขึ้น ⭐️ */
        border-bottom: 1px solid #B09270 !important;
    }

    /* 5. (สำคัญ) ทำให้ตัวอักษรทั้งหมดเป็น "สีขาว" เพื่อให้อ่านง่าย */
    .sidebar .brand-text,
    .sidebar .nav-header,
    .sidebar .nav-link p,
    .sidebar .nav-link i {
        color: #ffffff !important;
        text-shadow: 0 0 5px rgba(0,0,0,0.3); /* เพิ่มเงาเล็กน้อยให้อ่านง่ายขึ้น */
    }

    /* 6. เปลี่ยนสีเมนูที่ "เลือกอยู่" (Active) ⭐️ */
    .sidebar .nav-pills .nav-link.active {
        background-color: #B09270 !important; /* สีน้ำตาลเข้มขึ้น */
        box-shadow: none;
    }

    /* 7. เปลี่ยนสีเมนูเมื่อ "ชี้" (Hover) ⭐️ */
    .sidebar .nav-pills .nav-link:not(.active):hover {
        background-color: #C1A37E !important; /* สีน้ำตาลเข้มปานกลาง */
    }
    /* 8.1. กำหนดความเร็วในการ "ไหล" ของเนื้อหาและ Navbar */
    .content-wrapper, .main-header {
        /* ความเร็ว 0.3 วินาที (เร็ว-ช้า-เร็ว) */
        transition: margin-left 0.3s ease-in-out !important; 
    }

    /* 8.2. กำหนดความเร็วในการ "ไหล" ของ Sidebar */
    aside.main-sidebar {
        /* ความเร็ว 0.3 วินาที (เร็ว-ช้า-เร็ว) */
        transition: width 0.3s ease-in-out, transform 0.3s ease-in-out !important;
    }
</style>

{{-- 
    [แก้ไข HTML]
    ผมได้ลบคลาส 'sidebar-dark-primary' ออกจาก <aside> 
    เพื่อให้ CSS ที่เราเขียนด้านบนทำงานได้
--}}
<aside class="main-sidebar elevation-4">
    <a href="{{ route('welcome') }}" class="brand-link">
        <span class="brand-text font-weight-light">{{ session('admin_fullname') }}</span>
    </a>
    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{ route('welcome') }}" class="nav-link {{ Request::routeIs('welcome') ? 'active' : '' }}"><i class="nav-icon fas fa-home-alt"></i> <p>หน้าหลัก</p></a>
                </li>
                <li class="nav-header">การจัดการ</li>
                
                {{-- (เมนู "จัดการข้อมูลระบบ") --}}
                <li class="nav-item has-treeview {{ Request::routeIs('product.*', 'employee.*', 'customer.index', 'stock.*', 'promotion.*', 'protype.add') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ Request::routeIs('product.*', 'employee.*', 'customer.index', 'stock.*', 'promotion.*', 'protype.add') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>จัดการข้อมูลระบบ <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        
                        {{-- (Dropdown "จัดการข้อมูลสินค้า") --}}
                        <li class="nav-item has-treeview {{ Request::routeIs('product.*', 'protype.add') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ Request::routeIs('product.*', 'protype.add') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-shopping-cart"></i>
                                <p>จัดการข้อมูลสินค้า <i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('product.index') }}" class="nav-link {{ Request::routeIs('product.index', 'product.edit') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-list"></i>
                                        <p>ข้อมูลสินค้า</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('product.add') }}" class="nav-link {{ Request::routeIs('product.add') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-plus"></i>
                                        <p>เพิ่มข้อมูลสินค้า</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('protype.add') }}" class="nav-link {{ Request::routeIs('protype.add') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-chart-bar"></i> 
                                        <p>ประเภทสินค้า</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        
                        {{-- (Dropdown "จัดการข้อมูลพนักงาน") --}}
                        <li class="nav-item has-treeview {{ Request::routeIs('employee.*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ Request::routeIs('employee.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-user-tie"></i>
                                <p>จัดการข้อมูลพนักงาน <i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('employee.index') }}" class="nav-link {{ Request::routeIs('employee.index', 'employee.edit') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-user"></i> 
                                        <p>ข้อมูลพนักงาน</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('employee.add') }}" class="nav-link {{ Request::routeIs('employee.add') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-plus"></i> 
                                        <p>เพิ่มข้อมูลพนักงาน</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        
                        <li class="nav-item"><a href="{{ route('customer.index') }}" class="nav-link {{ Request::routeIs('customer.index') ? 'active' : '' }}"><i class="nav-icon fas fa-users"></i><p>ข้อมูลลูกค้า</p></a></li>
                        
                        {{-- (Dropdown "จัดการข้อมูลวัตถุดิบสินค้า") --}}
                        <li class="nav-item has-treeview {{ Request::routeIs('stock.*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ Request::routeIs('stock.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-boxes"></i>
                                <p>จัดการข้อมูลวัตถุดิบ <i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('stock.index') }}" class="nav-link {{ Request::routeIs('stock.index', 'stock.edit') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-list"></i> 
                                        <p>ข้อมูลวัตถุดิบ</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('stock.add') }}" class="nav-link {{ Request::routeIs('stock.add') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-plus"></i> 
                                        <p>เพิ่มข้อมูลนำเข้า</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        
                        {{-- (Dropdown "จัดการข้อมูลโปรโมชั่น") --}}
                        <li class="nav-item has-treeview {{ Request::routeIs('promotion.*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ Request::routeIs('promotion.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tags"></i>
                                <p>จัดการข้อมูลโปรโมชั่น <i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('promotion.index') }}" class="nav-link {{ Request::routeIs('promotion.index', 'promotion.edit') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-list"></i> 
                                        <p>ข้อมูลโปรโมชั่น</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('promotion.add') }}" class="nav-link {{ Request::routeIs('promotion.add') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-plus"></i> 
                                        <p>เพิ่มโปรโมชั่น</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="{{ route('history.index') }}" class="nav-link {{ Request::routeIs('history.index') ? 'active' : '' }}"><i class="nav-icon fas fa-history"></i> <p>ข้อมูลการสั่งซื้อสินค้า</p></a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('withdraw.create') }}" class="nav-link {{ Request::routeIs('withdraw.create') ? 'active' : '' }}"><i class="nav-icon fas fa-dolly-flatbed"></i> <p>เบิกวัตถุดิบ</p></a>
                </li>
                 <li class="nav-header">รายงาน</li>
                 
                {{-- (เมนูรายงาน) --}}
                <li class="nav-item has-treeview {{ Request::routeIs('salereport.index', 'report.bills', 'report.withdrawals', 'report.adjustments') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ Request::routeIs('salereport.index', 'report.bills', 'report.withdrawals', 'report.adjustments') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-chart-line"></i>
                        <p>รายงาน <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item"><a href="{{ route('salereport.index') }}" class="nav-link {{ Request::routeIs('salereport.index') ? 'active' : '' }}"><i class="fas fa-chart-line nav-icon"></i><p>รายงานการขายสินค้า</p></a></li>
                        <li class="nav-item"><a href="{{ route('report.bills') }}" class="nav-link {{ Request::routeIs('report.bills') ? 'active' : '' }}"><i class="fas fa-chart-bar nav-icon"></i> <p>รายงานยอดขาย</p></a></li>
                        <li class="nav-item"><a href="{{ route('report.withdrawals') }}" class="nav-link {{ Request::routeIs('report.withdrawals') ? 'active' : '' }}"><i class="fas fa-clipboard-list nav-icon"></i> <p>รายงานการเบิกวัตถุดิบ</p></a></li>
                        <li class="nav-item"><a href="{{ route('report.adjustments') }}" class="nav-link {{ Request::routeIs('report.adjustments') ? 'active' : '' }}"><i class="fas fa-sliders-h nav-icon "></i> <p>รายงานกาปรับยอดวัตถุดิบ</p></a></li>
                    </ul>
                </li>
                <li class="nav-header">อื่นๆ</li>
                <!-- ss -->
                <li class="nav-item">
                    <a href="#" class="nav-link" data-toggle="modal" data-target="#logoutModal"><i class="nav-icon fas fa-sign-out-alt text-danger"></i> <p>ออกจากระบบ</p></a>
                </li>
            </ul>
        </nav>
    </div>
</aside>