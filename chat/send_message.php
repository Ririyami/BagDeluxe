<!-- filepath: c:\xampp\htdocs\BagDeluxe(Latest)\BagDeluxe\userapp\send_message.php -->
<?php
session_start();
include '../components/connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: user_index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$receiver_id = $_POST['receiver_id'];
$order_id = $_POST['order_id'];
$message = $_POST['message'];

$stmt = $conn->prepare("INSERT INTO chat_messages (sender_id, receiver_id, order_id, message, created_at) 
                        VALUES (:sender_id, :receiver_id, :order_id, :message, NOW())");
$stmt->bindValue(':sender_id', $user_id, PDO::PARAM_INT);
$stmt->bindValue(':receiver_id', $receiver_id, PDO::PARAM_INT);
$stmt->bindValue(':order_id', $order_id, PDO::PARAM_INT);
$stmt->bindValue(':message', $message, PDO::PARAM_STR);
$stmt->execute();

header("Location: chat.php?driver_id=$receiver_id&order_id=$order_id");
exit();