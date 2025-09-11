<?php
// fetch_history_orders.php
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

// ตรวจสอบว่ามี em_id ส่งมาหรือไม่
if (!isset($_GET['em_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Employee ID is missing']);
    exit;
}

$emId = $_GET['em_id'];

// เลือกคำสั่งซื้อที่เสร็จสิ้นแล้วของพนักงานคนนั้น (จากตาราง order)
// แก้ไข: ใช้ LEFT JOIN เพื่อป้องกันข้อผิดพลาดหากข้อมูลในตารางอื่นไม่สมบูรณ์
$stmt = $pdo->prepare("SELECT
                        o.order_id,
                        o.cus_id,
                        c.fullname AS cus_name,
                        o.order_date,
                        o.price_total,
                        o.receive_date,
                        p.promo_name AS promo_code,
                        o.remarks
                       FROM `order` o
                       LEFT JOIN customer c ON o.cus_id = c.cus_id
                       LEFT JOIN promotion p ON o.promo_id = p.promo_id
                       WHERE o.receive_date IS NOT NULL AND o.em_id = ?
                       ORDER BY o.order_date DESC");
$stmt->execute([$emId]);
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
