<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Get order_id from GET, sanitize it
$order_id = isset($_GET['order_id']) ? htmlspecialchars($_GET['order_id']) : '0';

// Build the URL that the QR code will point to
// Replace this with your actual live URL, not localhost
$url = "https://msoshub.com/__bagdeluxe/Delivery/update_order_status.php?order_id=" . urlencode($order_id);

// Create Google Chart API URL for QR code image
$qr_code_url = "https://chart.googleapis.com/chart?cht=qr&chs=200x200&chl=" . urlencode($url) . "&chld=L|1";

// Set header for image output
header('Content-Type: image/png');

// Fetch the QR code image from Google and output it directly
echo file_get_contents($qr_code_url);
?>
