<?php
ob_clean();
ini_set('display_errors', 0);
error_reporting(0);

require_once __DIR__ . '/../vendor/autoload.php'; // adjust path if needed

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

// Validate input
$order_id = $_GET['order_id'] ?? null;
if (!$order_id || !preg_match('/^\d+$/', $order_id)) {
    http_response_code(400);
    exit('Invalid or missing order ID.');
}

// Construct the target URL the QR points to
$url = "http://localhost/bagdeluxe/Delivery/update_order_status.php?order_id=" . urlencode($order_id);

try {
    $result = Builder::create()
        ->writer(new PngWriter())
        ->data($url)
        ->size(300)
        ->margin(10)
        ->build();

    header('Content-Type: image/png');
    header('Content-Disposition: inline; filename="qrcode.png"');
    echo $result->getString();
    exit;
} catch (Exception $e) {
    http_response_code(500);
    exit('Error generating QR code: ' . $e->getMessage());
}
