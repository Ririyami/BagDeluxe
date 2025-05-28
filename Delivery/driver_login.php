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
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #ff0000, #800000); /* Red gradient background */
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background-color: rgba(255, 255, 255, 0.1); /* Semi-transparent white container */
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            width: 400px;
            text-align: center;
        }

        h3 {
            color: white;
            margin-bottom: 20px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
            background-color: rgba(255, 255, 255, 0.2); /* Semi-transparent white input fields */
            color: white;
            box-sizing: border-box;
        }

        input[type="text"]::placeholder,
        input[type="password"]::placeholder {
            color: rgba(2, 2, 2, 0.7);
        }

        button[type="submit"] {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border: none;
            border-radius: 5px;
            background-color: #d9534f; /* Red button */
            color: white;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button[type="submit"]:hover {
            background-color: #c9302c;
        }

        .toggle-buttons {
            margin-top: 20px;
            display: flex;
            justify-content: space-around;
        }

        .toggle-buttons button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background-color: #5bc0de; /* Blue button */
            color: white;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .toggle-buttons button:hover {
            background-color: #31b0d5;
        }

        a {
            color: white;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
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
            <button onclick="window.location.href='driver_register.php'">Register</button>
        </div>
    </div>
</body>
</html>