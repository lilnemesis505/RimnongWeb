<?php
header('Content-Type: application/json');

// รับข้อมูล JSON จาก Flutter/Postman
$raw = file_get_contents('php://input');
$input = json_decode($raw, true);

// ตรวจสอบว่า decode สำเร็จ
if (!is_array($input)) {
    echo json_encode(['status' => 'error', 'message' => 'รูปแบบข้อมูลไม่ถูกต้อง']);
    exit;
}

// ล้างช่องว่างและ newline
foreach ($input as $key => $value) {
    $input[$key] = trim($value);
}

// ตรวจสอบว่าข้อมูลครบไหม
$required = ['fullname', 'username', 'password', 'email', 'cus_tel'];
foreach ($required as $field) {
    if (empty($input[$field])) {
        echo json_encode(['status' => 'error', 'message' => "ข้อมูล '$field' ไม่ครบ"]);
        exit;
    }
}

// เชื่อมต่อฐานข้อมูล (MySQL)
$conn = new mysqli('localhost', 'root', '', 'rimnong');
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'เชื่อมต่อฐานข้อมูลล้มเหลว']);
    exit;
}

// ตรวจสอบว่ามี username ซ้ำไหม
$username = $conn->real_escape_string($input['username']);
$check = $conn->query("SELECT * FROM customer WHERE username = '$username'");
if ($check->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Username นี้มีอยู่แล้ว']);
    exit;
}

// บันทึกข้อมูลลงฐานข้อมูล
$fullname = $conn->real_escape_string($input['fullname']);
$password = password_hash($input['password'], PASSWORD_DEFAULT);
$email = $conn->real_escape_string($input['email']);
$tel = $conn->real_escape_string($input['cus_tel']);

$sql = "INSERT INTO customer (fullname, username, password, email, cus_tel)
        VALUES ('$fullname', '$username', '$password', '$email', '$tel')";

if ($conn->query($sql)) {
    echo json_encode(['status' => 'success', 'message' => 'สมัครสมาชิกสำเร็จ']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'บันทึกข้อมูลไม่สำเร็จ']);
}

$conn->close();
?>
