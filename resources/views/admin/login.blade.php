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

        body::before {
            content: "";
            position: fixed; 
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-image: url('https://gencraft.com/api_resources/images/model_previews/core_image_v3_flux_schnell.jpg?noCors=2');
            background-size: cover;
            background-position: center;
            filter: blur(8px);
            z-index: -1;
        }

        /* [แก้ไข] 1. เปลี่ยนพื้นหลังเป็นสีเทา, เพิ่ม overflow: hidden */
        .login-box {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 400px;
            margin: auto;
            /* background: rgba(255, 255, 255, 0.9); */ /* <-- อันเก่า */
            background: rgba(248, 249, 250, 0.95); /* <-- สีเทาอ่อน (Bootstrap light gray) */
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
            top: 50%;
            transform: translateY(-50%);
            overflow: hidden; /* 👈 ซ่อนส่วนที่สไลด์ออกไป */
            padding: 0; /* 👈 ย้าย padding ไปไว้ใน .form-panel แทน */
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

        /* [เพิ่ม] 2. CSS สำหรับการ์ดพลิก (สไลด์) */

        /* ซ่อน Checkbox ที่ใช้สลับหน้า */
        #flipper-toggle {
            display: none;
        }

        /* "ราง" ที่ให้ 2 หน้ามาเรียงต่อกัน */
        .flipper-track {
            display: flex;
            width: 200%; /* (100% สำหรับ Login + 100% สำหรับคำเตือน) */
            transition: transform 0.5s ease;
        }

        /* แต่ละหน้า (Panel) */
        .form-panel {
            width: 100%; /* (สำคัญมาก: แต่ละหน้ากว้าง 100% ของ login-box) */
            padding: 30px; /* 👈 ย้าย padding มาไว้ตรงนี้ */
            box-sizing: border-box; /* 👈 ป้องกัน padding ดัน layout */
        }

        /* เมื่อ Checkbox ถูกติ๊ก (โดยการกดปุ่ม "ไปหน้าคำเตือน") */
        #flipper-toggle:checked ~ .flipper-track {
            /* สั่งให้ "ราง" เลื่อนไปทางซ้าย 50% (ก็คือ 1 หน้าพอดี) */
            transform: translateX(-50%);
        }

        .panel-warning {
            text-align: center;
        }

    </style>
</head>
<body>

    <div class="login-box">

        {{-- [เพิ่ม] 3. Checkbox สำหรับสลับหน้า (ซ่อนไว้) --}}
        <input type="checkbox" id="flipper-toggle">

        {{-- [เพิ่ม] 4. "ราง" สำหรับสไลด์ --}}
        <div class="flipper-track">

            {{-- ================ หน้าที่ 1: Login Form ================ --}}
            <div class="form-panel panel-login">
                <h2><i class="fas fa-user-shield"></i> เข้าสู่ระบบผู้ดูแลระบบ</h2>

                @if ($errors->any())
                    <div class="error-message">
                        @foreach ($errors->all() as $error)
                            {{ $error }}<br>
                        @endforeach
                    </div>
                @endif
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
                    
                    <label for="login_identity">ชื่อผู้ใช้ หรือ อีเมล</label>
                    <input type="text" 
                           name="login_identity" 
                           id="login_identity" 
                           class="form-control" 
                           value="{{ old('login_identity') }}" 
                           required>
        
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

                <hr>
                
                {{-- [เพิ่ม] 5. ปุ่มสลับไปหน้า "คำเตือน" (ปุ่มขวา) --}}
                <label for="flipper-toggle" class="btn btn-outline-warning btn-block mb-0">
                    อ่านคำเตือน <i class="fas fa-arrow-right"></i>
                </label>

            </div> {{-- จบ panel-login --}}

            {{-- ================ หน้าที่ 2: Warning ================ --}}
            <div class="form-panel panel-warning">
                <h2><i class="fas fa-exclamation-triangle text-warning"></i> คำเตือน</h2>
                
                <p class="lead">ระบบนี้สำหรับผู้ที่ได้รับอนุญาตเท่านั้น</p>
                <p>เมื่อเข้าใช้งานเสร็จสิ้นกรุณาlogoutออกด้วย เพื่อความปลอดภัยของระบบ ซึ่งช่างแม้งง มี Registerให้อยู่ละ เข้าๆมาเหอะ Welcome Acttacker</p>
                <p>มีปัญหาการใช้งานระบบให้ติดต่อ สุดหล่อทีทีวี</p>
                <p>เบอร์โทร หาเอง</p>
                <hr>

                {{-- [เพิ่ม] 6. ปุ่มสลับกลับไปหน้า "Login" (ปุ่มซ้าย) --}}
                <label for="flipper-toggle" class="btn btn-outline-primary btn-block mb-0">
                    <i class="fas fa-arrow-left"></i> กลับไปหน้า Login
                </label>

            </div> {{-- จบ panel-warning --}}

        </div> {{-- จบ flipper-track --}}

    </div> {{-- จบ login-box --}}

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>