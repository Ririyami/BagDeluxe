<?php
include '../components/connect.php';

$query = "SELECT brand_name, SUM(stock_quantity) as total_stock FROM inventory GROUP BY brand_name";
$stmt = $conn->prepare($query);
$stmt->execute();

$brands = [];
$stockQuantities = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $brands[] = $row['brand_name'] ?? 'Unknown';
    $stockQuantities[] = (int)$row['total_stock'];
}

echo json_encode([
    'brands' => $brands,
    'stockQuantities' => $stockQuantities
]);
?>
