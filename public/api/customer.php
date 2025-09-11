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

// ตรวจสอบว่ามี cus_id ส่งมาหรือไม่
if (!isset($_GET['cus_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Customer ID is missing']);
    exit;
}

$cusId = $_GET['cus_id'];

try {
    // ดึงชื่อและอีเมลของลูกค้าจากฐานข้อมูล
    $stmt = $pdo->prepare("SELECT fullname, email FROM customer WHERE cus_id = ?");
    $stmt->execute([$cusId]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($customer) {
        echo json_encode(['status' => 'success', 'fullname' => $customer['fullname'], 'email' => $customer['email']]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Customer not found']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error fetching customer data: ' . $e->getMessage()]);
}
?>
