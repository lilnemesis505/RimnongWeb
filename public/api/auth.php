<?php
header('Content-Type: application/json');

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli('localhost', 'root', '', 'rimnong');
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

// ✅ อ่าน raw body แล้ว parse เป็น array
$raw = file_get_contents("php://input");
parse_str($raw, $parsed);

// ✅ debug เพื่อดูว่าได้ข้อมูลจริงไหม
file_put_contents('debug.txt', print_r($parsed, true));

// ตรวจสอบว่าได้รับข้อมูลจาก Flutter หรือไม่
if (!isset($parsed['username']) || !isset($parsed['password'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing credentials"]);
    exit;
}

$username = $parsed['username'];
$password = $parsed['password'];


// ฟังก์ชันตรวจสอบผู้ใช้
function check($table, $idField) {
    global $conn, $username, $password;
    $sql = "SELECT $idField, password FROM $table WHERE username=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            return $row;
        }
    }
    return null;
}

// ตรวจสอบจากตาราง Customer และ Employee
if ($user = check("Customer", "cus_id")) {
    echo json_encode(["status" => "success", "role" => "customer", "id" => $user["cus_id"]]);
} elseif ($user = check("Employee", "em_id")) {
    echo json_encode(["status" => "success", "role" => "employee", "id" => $user["em_id"]]);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid credentials"]);
}



