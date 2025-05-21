<?php
include '../components/connect.php';
session_start();
$driver_id = $_SESSION['driver_id'];

$result = $conn->query("SELECT * FROM orders WHERE driver_id = $driver_id AND status = 'new'");
$orders = [];
while ($row = $result->fetch_assoc()) {
    $row['chat_link'] = "chat.php?order_id=" . $row['id']; // Add chat link
    $orders[] = $row;
}
echo json_encode($orders);
?>