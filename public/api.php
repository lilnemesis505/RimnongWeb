<?php
header('Content-Type: application/json');

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli('localhost', 'root', '', 'rimnong');
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// ดึงข้อมูลจาก table product
$result = $conn->query("SELECT * FROM product");
if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Query failed']);
    exit;
}

$products = [];
$baseUrl = 'http://10.0.2.2/storage/products/';
$storagePath = realpath(__DIR__ . '/../storage/app/public/products/') . '/';
$extensions = ['png', 'jpg', 'jpeg'];

while ($row = $result->fetch_assoc()) {
    $imagePath = null;

    foreach ($extensions as $ext) {
        $fileName = $row['pro_id'] . '.' . $ext;
        $fullPath = $storagePath . $fileName;

        if (file_exists($fullPath)) {
            $imagePath = $baseUrl . $fileName;
            break;
        }
    }

    // ถ้าไม่เจอไฟล์ภาพ ให้ใช้ภาพ default
    $fallbackImage = 'no-image.png';
    $row['image_url'] = $imagePath ?? $baseUrl . $fallbackImage;

    $products[] = $row;
}

// ส่งข้อมูลกลับเป็น JSON
echo json_encode($products, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>
