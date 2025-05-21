<?php
session_start();
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['pid'], $data['name'], $data['price'], $data['image'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid data.']);
    exit;
}

$pid = $data['pid'];
$name = $data['name'];
$price = $data['price'];
$image = $data['image'];
$qty = max(1, (int)($data['qty'] ?? 1));

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$found = false;
foreach ($_SESSION['cart'] as &$item) {
    if ($item['pid'] == $pid) {
        $item['qty'] += $qty;
        $found = true;
        break;
    }
}
unset($item);

if (!$found) {
    $_SESSION['cart'][] = [
        'pid' => $pid,
        'name' => $name,
        'price' => $price,
        'image' => $image,
        'qty' => $qty
    ];
}

echo json_encode(['success' => true]);
?>
