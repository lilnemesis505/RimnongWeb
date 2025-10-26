<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow: hidden;
            position: relative;
            font-family: 'Segoe UI', sans-serif;
        }

        /* ... (style อื่นๆ เหมือนเดิม) ... */
        .login-box {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 400px;
            margin: auto;
            padding: 30px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
            top: 50%;
            transform: translateY(-50%);
        }

        .login-box h2 {
            margin-bottom: 20px;
            text-align: center;
            color: #343a40;
        }

        .form-control {
            margin-bottom: 15px;
        }

        .btn-login {
            width: 100%;
        }

        .error-message {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

    <div class="login-box">
        <h2><i class="fas fa-user-shield"></i> เข้าสู่ระบบผู้ดูแลระบบ</h2>

        {{-- แสดง Error (ถ้า Login ไม่ผ่าน) --}}
        @if ($errors->any())
            <div class="error-message">
                {{ $errors->first('login') }}
            </div>
        @endif

        {{-- ✅ [เพิ่ม] แสดงข้อความ Success (เช่น "สมัครสำเร็จ") --}}
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
         @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif


        <form method="POST" action="{{ route('login.submit') }}">
            @csrf
            <label for="username">ชื่อผู้ใช้</label>
            <input type="text" name="username" id="username" class="form-control" required>

            <label for="password">รหัสผ่าน</label>
            <input type="password" name="password" id="password" class="form-control" required>

            <button type="submit" class="btn btn-primary btn-login">เข้าสู่ระบบ</button>
        </form>
        
        <div class="text-center mt-3">
            <a href="{{ route('admin.password.request') }}">ลืมรหัสผ่าน?</a>
        </div>

        <div class="text-center mt-2">
            <span>ยังไม่มีบัญชี? </span>
            <a href="{{ route('admin.register.form') }}">สมัครสมาชิกที่นี่</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>