<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ตั้งรหัสผ่านใหม่</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        body { background: #f4f4f4; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .reset-box { width: 400px; padding: 30px; background: #fff; border-radius: 10px; box-shadow: 0 0 15px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="reset-box">
        <h4>ตั้งรหัสผ่านใหม่</h4>

        @if ($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.password.update') }}">
            @csrf
            {{-- [OTP CHANGE] ลบ hidden token ออก --}}
            
            <label for="email">อีเมล</label>
            <input type="email" name="email" id="email" class="form-control mb-2" required value="{{ $email ?? old('email') }}" readonly>
            
            <label for="password">รหัสผ่านใหม่(6ตัวอักษรขึ้นไป)</label>
           <input type="password" name="password" id="password" class="form-control mb-3" required>

            <label for="password_confirmation">ยืนยันรหัสผ่านใหม่</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control mb-3" required>

            <button type="submit" class="btn btn-primary btn-block">บันทึกรหัสผ่านใหม่</button>
        </form>
    </div>
</body>
</html>