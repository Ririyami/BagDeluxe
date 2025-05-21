<!-- filepath: c:\xampp\htdocs\BagDeluxe(Latest)\BagDeluxe\userapp\chat.php -->
<?php
session_start();
include '../components/connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: user_index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$driver_id = $_GET['driver_id'] ?? null;
$order_id = $_GET['order_id'] ?? null;

if (!$driver_id || !$order_id) {
    die("Driver ID and Order ID are required.");
}

var_dump($driver_id, $order_id); // Debug: Check if driver_id and order_id are set

// Fetch driver details
$stmt = $conn->prepare("SELECT * FROM drivers WHERE id = :driver_id");
$stmt->bindValue(':driver_id', $driver_id, PDO::PARAM_INT);
$stmt->execute();
$driver = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$driver) {
    die("Driver not found.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Chat with Driver</title>
    <link rel="stylesheet" href="style.css">
    <script>
        // Auto-refresh chat messages every 5 seconds
        setInterval(() => {
            const chatBox = document.querySelector('.chat-box');
            fetch(`fetch_messages.php?driver_id=<?php echo $driver_id; ?>&user_id=<?php echo $user_id; ?>`)
                .then(response => response.text())
                .then(data => {
                    chatBox.innerHTML = data;
                    chatBox.scrollTop = chatBox.scrollHeight; // Scroll to the bottom
                })
                .catch(error => console.error('Error fetching messages:', error));
        }, 5000);
    </script>
</head>
<body>
<div class="chat-container">
    <h2>Chat with <?php echo htmlspecialchars($driver['name']); ?></h2>
    <div class="chat-box">
        <?php
        // Fetch initial chat messages
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
        ?>
    </div>
    <form method="POST" action="send_message.php">
        <input type="hidden" name="receiver_id" value="<?php echo htmlspecialchars($driver_id); ?>">
        <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order_id); ?>">
        <input type="text" name="message" placeholder="Type your message..." required>
        <button type="submit">Send</button>
    </form>
    <a href="user_home.php" class="go-back">Go back to homepage</a>
</div>
</body>
</html>