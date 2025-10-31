<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เบิกวัตถุดิบ</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
    {{-- Select2 CSS (สำหรับ Dropdown สวยๆ + ค้นหาได้) --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <span class="navbar-brand">เบิกวัตถุดิบ</span>
    </nav>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="{{ route('welcome') }}" class="brand-link">
            <span class="brand-text font-weight-light">{{ session('admin_fullname') }}</span>
        </a>
        <div class="sidebar">
            <nav class="mt-2">
                {{-- [แก้ไข] 1. ใช้ <ul> หลักอันเดียว --}}
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-item">
                        {{-- 2. ลิงก์ 'active' ออกจากหน้าหลัก --}}
                        <a href="{{ route('welcome') }}" class="nav-link">
                            <i class="nav-icon fas fa-home-alt"></i> <p>หน้าหลัก</p>
                        </a>
                    </li>
                    
                    {{-- (ผมเพิ่มเมนู "การจัดการ" จากไฟล์ welcome.blade.php เข้ามาให้ครบ) --}}
                    <li class="nav-header">การจัดการ</li>
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-cogs"></i>
                            <p>จัดการข้อมูลระบบ <i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item"><a href="{{ route('product.index') }}" class="nav-link"><i class="nav-icon fas fa-shopping-cart"></i><p>จัดการข้อมูลสินค้า</p></a></li>
                            <li class="nav-item"><a href="{{ route('employee.index') }}" class="nav-link"><i class="nav-icon fas fa-user-tie"></i><p>จัดการข้อมูลพนักงาน</p></a></li>
                            <li class="nav-item"><a href="{{ route('customer.index') }}" class="nav-link"><i class="nav-icon fas fa-users"></i><p>ข้อมูลลูกค้า</p></a></li>
                            <li class="nav-item"><a href="{{ route('stock.index') }}" class="nav-link"><i class="nav-icon fas fa-boxes"></i><p>จัดการข้อมูลล็อตสินค้า</p></a></li>
                            <li class="nav-item"><a href="{{ route('promotion.index') }}" class="nav-link"><i class="nav-icon fas fa-tags"></i><p>จัดการข้อมูลโปรโมชั่น</p></a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('history.index') }}" class="nav-link"><i class="nav-icon fas fa-history"></i> <p>ข้อมูลการสั่งซื้อสินค้า</p></a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('withdraw.create') }}" class="nav-link active"><i class="nav-icon fas fa-dolly-flatbed"></i> <p>เบิกวัตถุดิบ</p></a>
                    </li>
                    
                     <li class="nav-header">รายงาน</li>
                     {{-- [แก้ไข] 3. ทำให้เมนูรายงาน 'active' และ 'menu-open' --}}
                    <li class="nav-item has-treeview menu-open">
                        <a href="#" class="nav-link ">
                            <i class="nav-icon fas fa-chart-pie"></i> {{-- 👈 ไอคอนหลักของ "รายงาน" --}}
                            <p>รายงาน <i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                {{-- [แก้ไข] 4. ทำให้หน้านี้ 'active' และเปลี่ยนไอคอน --}}
                                <a href="{{ route('salereport.index') }}" class="nav-link active">
                                    <i class="fas fa-chart-line nav-icon text-teal"></i> {{-- 👈 ไอคอนสื่อความหมาย --}}
                                    <p>รายงานการขายสินค้า</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('report.bills') }}" class="nav-link">
                                    <i class="fas fa-chart-bar nav-icon"></i> {{-- 👈 ไอคอนสื่อความหมาย --}}
                                    <p>รายงานยอดขาย</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('report.withdrawals') }}" class="nav-link ">
                                    <i class="fas fa-clipboard-list nav-icon"></i> {{-- 👈 ไอคอนสื่อความหมาย --}}
                                    <p>รายงานการเบิกวัตถุดิบ</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('report.adjustments') }}" class="nav-link">
                                    <i class="fas fa-sliders-h nav-icon "></i> {{-- 👈 ไอคอนสื่อความหมาย --}}
                                    <p>รายงานกาปรับยอดล็อตสินค้า</p>
                                </a>
                            </li>
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

    <div class="content-wrapper p-3">
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-dolly-flatbed"></i> ฟอร์มเบิกวัตถุดิบ</h3>
            </div>

            <form action="{{ route('withdraw.store') }}" method="POST" id="withdraw-form">
                @csrf
                <div class="card-body">
                    {{-- แสดง Success Message --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                    @endif
                    {{-- แสดง Error Message --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="mat_id">เลือกวัตถุดิบ</label>
                        <select name="mat_id" id="mat_id" class="form-control select2bs4" required style="width: 100%;"> {{-- Add style width 100% --}}
                            <option value="">-- กรุณาเลือก --</option>
                            @foreach($stockMaterials as $mat)
                                <option value="{{ $mat->mat_id }}"
                                        data-remain="{{ $mat->remain }}"
                                        data-unitcost="{{ $mat->unitcost }}"
                                        data-type="{{ $mat->type->type_name ?? 'ไม่ระบุ' }}"
                                        {{ old('mat_id') == $mat->mat_id ? 'selected' : '' }}>
                                    {{ $mat->mat_name }} (คงเหลือ: {{ $mat->remain }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                             <div class="form-group">
                                <label>ประเภท</label>
                                <input type="text" id="material_type" class="form-control" readonly placeholder="-- ประเภท --">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="withdraw_amount">จำนวนที่เบิก</label>
                                <input type="number" name="withdraw_amount" id="withdraw_amount" class="form-control"
                                       min="1" value="{{ old('withdraw_amount') }}" required>
                                <small id="remain_helper" class="form-text text-muted"></small>
                            </div>
                        </div>
                         <div class="col-md-4">
                             <div class="form-group">
                                <label>ราคาโดยประมาณ</label>
                                <input type="text" id="calculated_cost" class="form-control" readonly placeholder="0.00 บาท">
                            </div>
                        </div>
                    </div>

                </div>

                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-success" id="submit_button" disabled>
                        <i class="fas fa-check-circle"></i> ยืนยันการเบิก
                    </button>
                </div>
            </form>
        </div>

        {{-- เพิ่มส่วนแสดงประวัติการเบิกล่าสุด (ถ้าต้องการ) --}}
        {{-- <div class="card mt-4"> ... </div> --}}

    </div> {{-- (Logout Modal เหมือนเดิม) --}}
     <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header bg-warning"><h5 class="modal-title" id="logoutModalLabel">ยืนยันการออกจากระบบ</h5><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
          <div class="modal-body">คุณแน่ใจหรือไม่ว่าต้องการออกจากระบบ?</div>
          <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button><form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="btn btn-danger">ออกจากระบบ</button></form></div>
        </div>
      </div>
    </div>
</div><script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
{{-- Select2 JS --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize Select2
        $('.select2bs4').select2({
            theme: 'bootstrap4',
            placeholder: '-- กรุณาเลือก --', // Add placeholder
            allowClear: true // Allow clearing selection
        });

        const matSelect = $('#mat_id');
        const amountInput = $('#withdraw_amount');
        const typeDisplay = $('#material_type');
        const costDisplay = $('#calculated_cost');
        const remainHelper = $('#remain_helper');
        const submitButton = $('#submit_button');

        let currentRemain = 0;
        let currentUnitCost = 0;

        function updateDisplay() {
            const selectedOption = matSelect.find('option:selected');
            const amount = parseInt(amountInput.val()) || 0;

            // Reset validation state first
            amountInput.removeClass('is-invalid');
            remainHelper.removeClass('text-danger').addClass('text-muted'); // Reset helper text style

            if (selectedOption.val() && selectedOption.data('remain') !== undefined) { // Check if data-remain exists
                currentRemain = parseInt(selectedOption.data('remain')) || 0;
                currentUnitCost = parseFloat(selectedOption.data('unitcost')) || 0;
                const typeName = selectedOption.data('type') || 'ไม่ระบุ';

                typeDisplay.val(typeName);
                remainHelper.text(`คงเหลือในสต็อก: ${currentRemain}`);
                amountInput.attr('max', currentRemain); // Set max attribute dynamically

                if (amount > 0) {
                    const cost = (amount * currentUnitCost).toFixed(2);
                    costDisplay.val(`${cost} บาท`);
                } else {
                    costDisplay.val('0.00 บาท');
                }

                // Enable/disable submit button and validate amount
                if (amount > 0 && amount <= currentRemain) {
                    submitButton.prop('disabled', false);
                } else {
                    submitButton.prop('disabled', true);
                    if (amount > currentRemain) {
                         amountInput.addClass('is-invalid'); // Add red border if over limit
                         remainHelper.text(`คงเหลือในสต็อก: ${currentRemain} (เบิกเกิน!)`).removeClass('text-muted').addClass('text-danger');
                    } else if (amount <= 0 && amountInput.val() !== '') { // Handle case where user enters 0 or negative
                         amountInput.addClass('is-invalid');
                         remainHelper.text('จำนวนต้องมากกว่า 0').removeClass('text-muted').addClass('text-danger');
                    }
                }

            } else {
                // Reset if no material is selected or data is missing
                currentRemain = 0;
                currentUnitCost = 0;
                typeDisplay.val('-- ประเภท --');
                costDisplay.val('0.00 บาท');
                remainHelper.text('');
                amountInput.removeAttr('max').val(''); // Clear amount input
                submitButton.prop('disabled', true);
            }
        }

        // Event listeners
        matSelect.on('change', updateDisplay);
        amountInput.on('input', updateDisplay);

        // Initial display update on page load (if there's old input)
        updateDisplay();
    });
</script>
</body>
</html>