<!DOCTYPE html>
<html>
<head>
    <title>รหัส OTP สำหรับรีเซ็ตรหัสผ่าน</title>
</head>
<body>
    <p>รหัส OTP สำหรับรีเซ็ตรหัสผ่าน (Admin) ของคุณคือ:</p>
    <h1 style="font-size: 32px; font-weight: bold; letter-spacing: 2px; color: #007bff;">
        {{ $otp }}
    </h1>
    <p>รหัสนี้จะหมดอายุใน 10 นาที</p>
    <br>
    <p>หากคุณไม่ได้ร้องขอการรีเซ็ตรหัสผ่าน กรุณาไม่ต้องดำเนินการใดๆ</p>
</body>
</html>