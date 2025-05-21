<?php
include '../components/connect.php';
session_start();

if (isset($_POST['login'])) {
    $number = $_POST['number'];
    $password = $_POST['password'];

    try {
        $stmt = $conn->prepare("SELECT * FROM drivers WHERE number = :number");
        $stmt->bindValue(':number', $number);
        $stmt->execute();
        $driver = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($driver && password_verify($password, $driver['password'])) {
            $_SESSION['driver_id'] = $driver['id'];
            $_SESSION['driver_name'] = $driver['name'];
            header("Location: driver_home.php");
            exit;
        } else {
            echo "<p style='color:red; text-align:center;'>Invalid driver number or password.</p>";
        }
    } catch (PDOException $e) {
        echo "<p style='color:red; text-align:center;'>Error: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Login</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <h3>Driver Login</h3>
        <form method="post" action="driver_login.php">
            <input type="text" name="number" placeholder="Driver Number" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
        </form>
        <div class="toggle-buttons">
            <button onclick="window.location.href='driver_login.php'">Login</button>
            <button onclick="window.location.href='driver_register.php'">Register</button>
        </div>
    </div>
</body>
</html>