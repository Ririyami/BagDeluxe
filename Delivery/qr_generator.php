<?php
require_once __DIR__ . '/vendor/autoload.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    die('Composer autoload.php file is missing. Please run "composer install".');
}

try {
    // Get the order ID from the query string
    $order_id = isset($_GET['order_id']) ? htmlspecialchars($_GET['order_id']) : '0';

    // Construct the URL to encode in the QR code
    $url = "http://localhost/bagdeluxe/Delivery/update_order_status.php?order_id=" . urlencode($order_id);

    // Create QrCode object with the URL
    $qrCode = new QrCode($url);

    // Create a writer instance
    $writer = new PngWriter();

    // Render QR code with size and margin set in the write() options array
    $result = $writer->write(
        $qrCode,
        null, // no logo
        null, // no label
        [
            'size' => 200,
            'margin' => 10
        ]
    );

    // Output as PNG
    header('Content-Type: image/png');
    echo $result->getString();
} catch (Exception $e) {
    error_log("QR Code Generation Error: " . $e->getMessage());
    if (!headers_sent()) {
        header('Content-Type: text/plain');
    }
    echo "Error generating QR code: " . $e->getMessage();
}