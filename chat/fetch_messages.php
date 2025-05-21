<!-- filepath: c:\xampp\htdocs\BagDeluxe(Latest)\BagDeluxe\chat\fetch_messages.php -->
<?php
session_start();
include '../components/connect.php';

$user_id = $_GET['user_id'] ?? null;
$driver_id = $_GET['driver_id'] ?? null;

if (!$user_id || !$driver_id) {
    die("Invalid parameters.");
}

$stmt = $conn->prepare("SELECT * FROM chat_messages WHERE (sender_id = :user_id AND receiver_id = :driver_id) 
                        OR (sender_id = :driver_id AND receiver_id = :user_id) ORDER BY created_at ASC");
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->bindValue(':driver_id', $driver_id, PDO::PARAM_INT);
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($messages as $message) {
    $class = $message['sender_id'] == $user_id ? 'user' : 'driver';
    echo "<div class='chat-message $class'>";
    echo "<p>" . htmlspecialchars($message['message']) . "</p>";
    echo "</div>";
}