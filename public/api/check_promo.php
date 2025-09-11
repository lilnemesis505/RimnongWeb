<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');

$servername = "localhost";
$username = "root"; // เปลี่ยนตามชื่อผู้ใช้จริง
$password = ""; // เปลี่ยนตามรหัสผ่านจริง
$dbname = "rimnong"; // เปลี่ยนเป็นชื่อฐานข้อมูลของคุณ

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Connection failed: ' . $conn->connect_error]));
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['promo_name'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

$promo_name = $data['promo_name'];
$current_time = date('Y-m-d H:i:s');

// เตรียมคำสั่ง SQL เพื่อค้นหาโปรโมชันที่ถูกต้องตามชื่อและช่วงเวลา
$sql = "SELECT promo_id, promo_discount FROM promotion WHERE promo_name = ? AND promo_start <= ? AND promo_end >= ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $promo_name, $current_time, $current_time);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode([
        'status' => 'success',
        'message' => 'Promotion code applied successfully',
        'promo_discount' => $row['promo_discount'],
        'promo_id' => $row['promo_id']
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Promotion code is invalid or expired'
    ]);
}

$stmt->close();
$conn->close();
?>
