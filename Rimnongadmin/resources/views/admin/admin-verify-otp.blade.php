<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ยืนยัน OTP</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        body { background: #f4f4f4; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .verify-box { width: 400px; padding: 30px; background: #fff; border-radius: 10px; box-shadow: 0 0 15px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="verify-box">
        <h4>ยืนยันตัวตน</h4>
        <p>เราได้ส่งรหัส OTP 6 หลักไปที่ <strong>{{ $email }}</strong> กรุณากรอกรหัสด้านล่าง (หมดอายุใน 10 นาที)</p>

        @if ($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.otp.check') }}">
            @csrf
            {{-- ส่งอีเมลไปด้วย (แบบซ่อน) --}}
            <input type="hidden" name="email" value="{{ $email }}">
            
            <label for="otp">รหัส OTP 6 หลัก</label>
            <input type="text" name="otp" id="otp" class="form-control mb-3" required autofocus maxlength="6" pattern="\d{6}">

            <button type="submit" class="btn btn-primary btn-block">ยืนยัน OTP</button>
        </form>
         <div class="text-center mt-3">
            <a href="{{ route('admin.password.request') }}">ขอรหัส OTP ใหม่</a>
        </div>
    </div>
</body>
</html>