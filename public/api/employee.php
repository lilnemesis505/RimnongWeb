<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');

// เชื่อมต่อฐานข้อมูล
$host = 'localhost';
$dbname = 'rimnong';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit;
}

// ตรวจสอบว่ามี em_id ส่งมาหรือไม่
if (!isset($_GET['em_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Employee ID is missing']);
    exit;
}

$emId = $_GET['em_id'];

try {
    // ✅ ดึงชื่อและอีเมลพนักงานจากฐานข้อมูล
    $stmt = $pdo->prepare("SELECT em_name, em_email FROM employee WHERE em_id = ?");
    $stmt->execute([$emId]);
    $employee = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($employee) {
        // ✅ ส่งทั้ง em_name และ em_email กลับไป
        echo json_encode(['status' => 'success', 'em_name' => $employee['em_name'], 'em_email' => $employee['em_email']]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Employee not found']);
    }

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error fetching employee data: ' . $e->getMessage()]);
}
?>
