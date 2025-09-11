<?php
// order.php
header('Content-Type: application/json');

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

// ตรวจสอบว่าคำขอเป็นแบบ multipart/form-data และมีไฟล์รูปภาพสลิปถูกอัปโหลด
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['slip_image'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request or no slip image uploaded']);
    exit;
}

// รับข้อมูลจาก fields ที่ถูกส่งมา
$data = $_POST;
// ข้อมูล order_items ถูก encode เป็น JSON string มาจาก Flutter ต้อง decode กลับ
$orderItems = json_decode($data['order_items'], true);

// ตรวจสอบข้อมูลที่จำเป็น
if (!is_array($orderItems) || empty($orderItems) || !isset($data['cus_id']) || !isset($data['price_total'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid order data received']);
    exit;
}

// ตรวจสอบประเภทไฟล์
$allowed_extensions = ['jpg', 'jpeg', 'png'];
$image_extension = strtolower(pathinfo($_FILES['slip_image']['name'], PATHINFO_EXTENSION));

if (!in_array($image_extension, $allowed_extensions)) {
    echo json_encode(['status' => 'error', 'message' => 'Unsupported file type. Only JPG, JPEG, and PNG are allowed.']);
    exit;
}

try {
    $pdo->beginTransaction();

    // 1. ตรวจสอบ promo_id ก่อนบันทึก
    $promoId = isset($data['promo_id']) && !empty($data['promo_id']) ? $data['promo_id'] : null;
    if ($promoId !== null) {
        $stmtPromo = $pdo->prepare("SELECT COUNT(*) FROM `promotion` WHERE promo_id = ?");
        $stmtPromo->execute([$promoId]);
        if ($stmtPromo->fetchColumn() === 0) {
            throw new Exception("Invalid promo_id: The specified promotion does not exist.");
        }
    }
    
    $remarks = $data['remarks'] ?? '';
    
    $stmtOrder = $pdo->prepare("INSERT INTO `order` (cus_id, order_date, promo_id, price_total, remarks) 
                                VALUES (:cus_id, NOW(), :promo_id, :price_total, :remarks)");
    $stmtOrder->execute([
        ':cus_id'     => $data['cus_id'],
        ':promo_id'   => $promoId,
        ':price_total' => $data['price_total'],
        ':remarks'    => $remarks
    ]);
    
    $orderId = $pdo->lastInsertId();

    // 2. อัปโหลดสลิป
    $slipImage = $_FILES['slip_image'];
    $fileName = 'slip_' . $orderId . '.' . $image_extension;
    $uploadPath = '../storage/app/public/slips/';
    
    if (!is_dir($uploadPath)) {
        mkdir($uploadPath, 0777, true);
    }
    
    move_uploaded_file($slipImage['tmp_name'], $uploadPath . $fileName);

    // 3. บันทึกรายละเอียดสินค้าลงในตาราง 'order_detail'
    $stmtDetail = $pdo->prepare("INSERT INTO order_detail (order_id, pro_id, amount, price_list, pay_total) 
                                 VALUES (:order_id, :pro_id, :amount, :price_list, :pay_total)");
    foreach ($orderItems as $item) {
        $stmtDetail->execute([
            ':order_id'   => $orderId,
            ':pro_id'     => $item['pro_id'],
            ':amount'     => $item['amount'],
            ':price_list' => $item['price_list'],
            ':pay_total'  => $item['pay_total'],
        ]);
    }

    $pdo->commit();

    echo json_encode(['status' => 'success', 'order_id' => $orderId]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['status' => 'error', 'message' => 'Failed to save order: ' . $e->getMessage()]);
}
?>