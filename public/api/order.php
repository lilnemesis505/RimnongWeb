<?php

ob_start();
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
header('Content-Type: application/json');

// เพิ่ม autoload ของ Composer (ปรับ path ตามจริง)
require_once __DIR__ . '/../../vendor/autoload.php';

use ImageKit\ImageKit;


function log_order_error($message) {
    $logFile = __DIR__ . '/order_error.log';
    $date = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$date] $message\n", FILE_APPEND);
}

// log ว่าเริ่มต้น script
log_order_error('--- script start ---');

try {
    // เชื่อมต่อฐานข้อมูล
    $host = 'localhost';
    $dbname = 'rimnong'; 
    $user = 'root';
    $pass = '';

    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ตรวจสอบ request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['slip_image'])) {
        throw new Exception('Invalid request or no slip image uploaded');
    }

    $data = $_POST;
    $orderItems = json_decode($data['order_items'] ?? '', true);

    if (!is_array($orderItems) || empty($orderItems) || !isset($data['cus_id']) || !isset($data['price_total'])) {
        throw new Exception('Invalid order data received: ' . json_encode($data));
    }

    $imageKit = new ImageKit(
        'public_S3cZPtMJKWgU5sCA4u8sW2u/rOk=',
        'private_fvwKcuOleXQE8Sz6fbZFliTjS8s=',
        'https://ik.imagekit.io/lilnemesis505/'
    );

    $pdo->beginTransaction();

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

    $slipImage = $_FILES['slip_image'];
    $fileName = 'slip_' . $orderId . '.' . pathinfo($slipImage['name'], PATHINFO_EXTENSION);

    $uploadResult = $imageKit->uploadFile([
        'file' => base64_encode(file_get_contents($slipImage['tmp_name'])),
        'fileName' => $fileName,
        'folder' => '/Slips'
    ]);

    if (!isset($uploadResult->result)) {
        throw new Exception("ImageKit upload failed: " . ($uploadResult->error->message ?? 'Unknown error'));
    }

    $slipImageUrl = $uploadResult->result->url;
    $slipFileId = $uploadResult->result->fileId;

    $stmtUpdateSlip = $pdo->prepare("UPDATE `order` SET slips_url = ?, slips_id = ? WHERE order_id = ?");
    $stmtUpdateSlip->execute([$slipImageUrl, $slipFileId, $orderId]);

    $pdo->commit();

    echo json_encode(['status' => 'success', 'order_id' => $orderId]);
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    log_order_error($e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Failed to save order: ' . $e->getMessage()]);
}

// ตรวจสอบ output ที่ไม่ใช่ JSON และ log ทิ้ง
$output = ob_get_contents();
ob_end_clean();
if (preg_match('/^\s*{.*}\s*$/s', $output)) {
    echo $output;
} else {
    log_order_error("Non-JSON output: " . $output);
    echo json_encode(['status' => 'error', 'message' => 'Internal server error']);
}