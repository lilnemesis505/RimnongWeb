<aside class="main-sidebar sidebar-dark-primary elevation-4">
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
                
                {{-- (เมนู "จัดการข้อมูลระบบ" ... เหมือนเดิม) --}}
                <li class="nav-item has-treeview {{ Request::routeIs('product.*', 'employee.*', 'customer.index', 'stock.*', 'promotion.*', 'protype.add') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ Request::routeIs('product.*', 'employee.*', 'customer.index', 'stock.*', 'promotion.*', 'protype.add') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>จัดการข้อมูลระบบ <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        
                        {{-- (Dropdown "จัดการข้อมูลสินค้า" ... เหมือนเดิม) --}}
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
                        
                        {{-- (Dropdown "จัดการข้อมูลพนักงาน" ... เหมือนเดิม) --}}
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
                        
                        {{-- (Dropdown "จัดการข้อมูลล็อตสินค้า" ... เหมือนเดิม) --}}
                        <li class="nav-item has-treeview {{ Request::routeIs('stock.*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ Request::routeIs('stock.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-boxes"></i>
                                <p>จัดการข้อมูลล็อตสินค้า <i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('stock.index') }}" class="nav-link {{ Request::routeIs('stock.index', 'stock.edit') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-list"></i> 
                                        <p>ข้อมูลล็อตสินค้า</p>
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
                        
                        {{-- [แก้ไข] 1. เปลี่ยน "จัดการข้อมูลโปรโมชั่น" ให้เป็น Dropdown --}}
                        <li class="nav-item has-treeview {{ Request::routeIs('promotion.*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ Request::routeIs('promotion.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tags"></i>
                                <p>จัดการข้อมูลโปรโมชั่น <i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                {{-- [เพิ่ม] 2. เมนูย่อย "ข้อมูลโปรโมชั่น" --}}
                                <li class="nav-item">
                                    <a href="{{ route('promotion.index') }}" class="nav-link {{ Request::routeIs('promotion.index', 'promotion.edit') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-list"></i> 
                                        <p>ข้อมูลโปรโมชั่น</p>
                                    </a>
                                </li>
                                {{-- [เพิ่ม] 3. เมนูย่อย "เพิ่มโปรโมชั่น" --}}
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
                 
                {{-- (เมนูรายงาน ... เหมือนเดิม) --}}
                <li class="nav-item has-treeview {{ Request::routeIs('salereport.index', 'report.bills', 'report.withdrawals', 'report.adjustments') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ Request::routeIs('salereport.index', 'report.bills', 'report.withdrawals', 'report.adjustments') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-chart-line"></i>
                        <p>รายงาน <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item"><a href="{{ route('salereport.index') }}" class="nav-link {{ Request::routeIs('salereport.index') ? 'active' : '' }}"><i class="fas fa-chart-line nav-icon"></i><p>รายงานการขายสินค้า</p></a></li>
                        <li class="nav-item"><a href="{{ route('report.bills') }}" class="nav-link {{ Request::routeIs('report.bills') ? 'active' : '' }}"><i class="fas fa-chart-bar nav-icon"></i> <p>รายงานยอดขาย</p></a></li>
                        <li class="nav-item"><a href="{{ route('report.withdrawals') }}" class="nav-link {{ Request::routeIs('report.withdrawals') ? 'active' : '' }}"><i class="fas fa-clipboard-list nav-icon"></i> <p>รายงานการเบิกวัตถุดิบ</p></a></li>
                        <li class="nav-item"><a href="{{ route('report.adjustments') }}" class="nav-link {{ Request::routeIs('report.adjustments') ? 'active' : '' }}"><i class="fas fa-sliders-h nav-icon "></i> <p>รายงานกาปรับยอดล็อตสินค้า</p></a></li>
                    </ul>
                </li>
                <li class="nav-header">อื่นๆ</li>
                <li class="nav-item">
                    <a href="#" class="nav-link" data-toggle="modal" data-target="#logoutModal"><i class="nav-icon fas fa-sign-out-alt text-danger"></i> <p>ออกจากระบบ</p></a>
                </li>
            </ul>
        </nav>
    </div>
</aside>