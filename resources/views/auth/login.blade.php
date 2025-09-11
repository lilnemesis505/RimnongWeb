<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f4f6f9;
        }
        .login-box {
            width: 400px;
            margin: 80px auto;
        }
        .card-header h1 {
            font-size: 24px;
            margin: 0;
        }
    </style>
</head>
<body>

<div class="login-box">
    <div class="card card-outline card-primary">
        <div class="card-header text-center">
            <h1><b>เข้าสู่ระบบ</b></h1>
        </div>
        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('login.submit') }}">
                @csrf
                <div class="input-group mb-3">
                    <input type="text" name="username" class="form-control" placeholder="ชื่อผู้ใช้" required autofocus>
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-user"></span></div>
                    </div>
                </div>

                <div class="input-group mb-3">
                    <input type="password" name="password" class="form-control" placeholder="รหัสผ่าน" required>
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-lock"></span></div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block">เข้าสู่ระบบ</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
