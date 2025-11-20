<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ลืมรหัสผ่าน</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        body { background: #f4f4f4; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .forgot-box { width: 400px; padding: 30px; background: #fff; border-radius: 10px; box-shadow: 0 0 15px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="forgot-box">
        <h4>ลืมรหัสผ่าน</h4>
        <p>กรุณากรอกอีเมลของคุณ เราจะส่งOTPสำหรับรีเซ็ตรหัสผ่านไปให้</p>

        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.password.email') }}">
            @csrf
            <label for="email">อีเมล</label>
            <input type.="email" name="email" id="email" class="form-control mb-3" required value="{{ old('email') }}">
            <button type="submit" class="btn btn-primary btn-block">ส่งรหัส OTP</button>
        </form>
         <div class="text-center mt-3">
            <a href="{{ route('login') }}">กลับไปหน้าเข้าสู่ระบบ</a>
        </div>
    </div>
</body>
</html>