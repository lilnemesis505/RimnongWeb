<?php
// fetch_cushistory.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

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

// ✅ แก้ไข: เพิ่ม em_id และ receive_date ในคำสั่ง SELECT
$stmt = $pdo->prepare("SELECT
                        o.order_id,
                        o.cus_id,
                        c.fullname AS cus_name,
                        o.order_date,
                        o.em_id,
                        o.price_total,
                        o.receive_date,
                        p.promo_name,
                        o.remarks
                       FROM `order` o
                       LEFT JOIN customer c ON o.cus_id = c.cus_id
                       LEFT JOIN promotion p ON o.promo_id = p.promo_id
                       WHERE o.cus_id = ?
                       ORDER BY o.order_date DESC");
$stmt->execute([$cusId]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ดึงรายละเอียดสินค้าสำหรับแต่ละคำสั่งซื้อ
foreach ($orders as &$order) {
    $stmtDetail = $pdo->prepare("SELECT
                                  od.pro_id,
                                  prod.pro_name,
                                  od.amount,
                                  od.price_list,
                                  od.pay_total
                                 FROM `order_detail` od
                                 JOIN product prod ON od.pro_id = prod.pro_id
                                 WHERE od.order_id = ?");
    $stmtDetail->execute([$order['order_id']]);
    $order['order_details'] = $stmtDetail->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($orders);
?>
