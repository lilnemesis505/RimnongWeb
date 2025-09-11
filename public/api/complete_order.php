<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');

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

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['order_id']) || !isset($data['action'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
    exit;
}

$orderId = $data['order_id'];
$action = $data['action'];
$emId = $data['em_id'] ?? null;

try {
    if ($action === 'accept') {
        // อัปเดต em_id เมื่อพนักงานรับคำสั่งซื้อ
        $stmt = $pdo->prepare("UPDATE `order` SET em_id = ? WHERE order_id = ?");
        $stmt->execute([$emId, $orderId]);
        echo json_encode(['status' => 'success', 'message' => 'Order accepted successfully']);
    } elseif ($action === 'complete') {
        // อัปเดต receive_date เมื่อทำรายการเสร็จสิ้น
        $stmt = $pdo->prepare("UPDATE `order` SET receive_date = NOW() WHERE order_id = ?");
        $stmt->execute([$orderId]);
        echo json_encode(['status' => 'success', 'message' => 'Order completed successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update order status: ' . $e->getMessage()]);
}
?>
