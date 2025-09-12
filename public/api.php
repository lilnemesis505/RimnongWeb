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

while ($row = $result->fetch_assoc()) {
    // ดึง URL รูปภาพจากคอลัมน์ 'image' โดยตรง
    $row['image_url'] = $row['image'] ?? 'https://placehold.co/100x100?text=No+Image';

    $products[] = $row;
}

// ส่งข้อมูลกลับเป็น JSON
echo json_encode($products, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>
