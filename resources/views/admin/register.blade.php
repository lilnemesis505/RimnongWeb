<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Register</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
    
    {{-- (คัดลอก Style จาก login.blade.php มา) --}}
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow-y: auto; /* [FIX] เปลี่ยนเป็น auto เพื่อให้เลื่อนได้ */
            position: relative;
            font-family: 'Segoe UI', sans-serif;
        }
        body::before {
            content: "";
            position: fixed; /* [FIX] เปลี่ยนเป็น fixed */
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-image: url('https://gencraft.com/api_resources/images/model_previews/core_image_v3_flux_schnell.jpg?noCors=2');
            background-size: cover;
            background-position: center;
            filter: blur(8px);
            z-index: -1;
        }
        .login-box {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 450px; /* [FIX] ขยายกล่องเล็กน้อย */
            margin: 50px auto; /* [FIX] ปรับ margin สำหรับหน้ายาว */
            padding: 30px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
            /* (ลบ top: 50% และ transform ออก) */
        }
        .login-box h2 {
            margin-bottom: 20px;
            text-align: center;
            color: #343a40;
        }
        .form-control {
            margin-bottom: 5px; /* [FIX] ลดระยะห่าง mb */
        }
        .btn-login {
            width: 100%;
        }
        .error-message {
            color: red;
            font-size: 0.9rem;
            margin-top: -10px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

    <div class="login-box">
        <h2><i class="fas fa-user-plus"></i> สมัครสมาชิกผู้ดูแล</h2>

        {{-- (แสดง Error รวม) --}}
        @if ($errors->has('register'))
            <div class="alert alert-danger">
                {{ $errors->first('register') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.register.submit') }}">
            @csrf
            
            <label for="fullname">ชื่อ-สกุล</label>
            <input type="text" name="fullname" id="fullname" class="form-control" value="{{ old('fullname') }}" required>
            @error('fullname') <div class="error-message">{{ $message }}</div> @enderror

            <label for="username">ชื่อผู้ใช้</label>
            <input type="text" name="username" id="username" class="form-control" value="{{ old('username') }}" required>
            @error('username') <div class="error-message">{{ $message }}</div> @enderror

            <label for="email">อีเมล</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
            @error('email') <div class="error-message">{{ $message }}</div> @enderror

            <label for="admin_tel">เบอร์โทร</label>
            <input type="text" name="admin_tel" id="admin_tel" class="form-control" value="{{ old('admin_tel') }}" required maxlength="10">
            @error('admin_tel') <div class="error-message">{{ $message }}</div> @enderror

            <label for="password">รหัสผ่าน</label>
            <input type="password" name="password" id="password" class="form-control" required>
            @error('password') <div class="error-message">{{ $message }}</div> @enderror

            <label for="password_confirmation">ยืนยันรหัสผ่าน</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
            
            <br>
            <button type="submit" class="btn btn-primary btn-login">สมัครสมาชิก</button>
        </form>
        <div class="text-center mt-3">
            <a href="{{ route('login') }}">มีบัญชีแล้ว? กลับไปเข้าสู่ระบบ</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>