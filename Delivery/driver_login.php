<?php
include '../components/connect.php';
session_start();

$message = '';
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
            $message = "Invalid driver number or password.";
        }
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
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
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #682020;
            margin: 0;
            font-family: 'Montserrat', Arial, sans-serif;
        }
        .login-form-container {
            background: rgba(255,255,255,0.13);
            box-shadow: 0 8px 32px 0 rgba(125, 38, 38, 0.18);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 22px;
            border: 2px solid #b23232;
            width: 450px;
            padding: 44px 36px 38px 36px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
            overflow: hidden;
            transition: box-shadow 0.3s;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .login-form-container h3 {
            text-align: center;
            font-size: 2rem;
            font-weight: bold;
            color: #7d2626;
            letter-spacing: 1px;
            margin-bottom: 18px;
            text-shadow: 0 2px 8px #fff2;
        }
        .login-form-container form {
            width: 350px;
            align-items: center;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }
        .login-form-container input[type="text"],
        .login-form-container input[type="password"] {
            width: 100%;
            padding: 13px 16px;
            border: 1.5px solid #b23232;
            border-radius: 12px;
            background: #fff;
            font-size: 1em;
            color: #7d2626;
            box-shadow: 0 2px 10px #7d262610;
            outline: none;
            transition: border 0.2s, box-shadow 0.2s;
        }
        .login-form-container input[type="text"]:focus,
        .login-form-container input[type="password"]:focus {
            border: 2px solid #ff7eb3;
            box-shadow: 0 2px 12px #ff7eb344;
        }
        .login-form-container .btn {
            background: linear-gradient(90deg, #b23232 0%, #7d2626 100%);
            color: #fff;
            border: none;
            padding: 13px;
            border-radius: 12px;
            font-size: 1.15em;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 2px 12px #7d262655;
            margin-top: 10px;
            transition: background 0.2s, transform 0.13s;
            letter-spacing: 1px;
        }
        .login-form-container .btn:hover {
            background: linear-gradient(90deg, #ff7eb3 0%, #b23232 100%);
            color: #fffbe7;
            transform: translateY(-2px) scale(1.04);
        }
        .login-form-container .register-link {
            margin-top: 18px;
            color: #999;
            font-size: 1.1em;
            text-align: center;
        }
        .login-form-container .register-link a {
            color: #1a237e;
            text-decoration: underline;
            font-weight: 500;
        }
        @media (max-width: 600px) {
            .login-form-container {
                width: 98vw;
                padding: 18px 4vw 18px 4vw;
            }
        }
         .message {
        background: #fff6;
        color: #b23232;
        border-radius: 8px;
        padding: 11px 15px;
        margin-bottom: 14px;
        text-align: center;
        font-size: 1em;
        box-shadow: 0 1px 4px #7d262610;
        border: 1px solid #ff7eb3;
    }
    @media (max-width: 500px) {
        .container { width: 98vw; padding: 30px 6px 26px 6px;}
        .form-title { font-size: 1.5rem; }
    }
    </style>
</head>
<body>
    <div class="login-form-container">
        <h3>Driver Login</h3>
        <?php if ($message): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <form method="post" action="driver_login.php">
            <input type="text" name="number" placeholder="Driver Number" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login" class="btn">Login</button>
        </form>
        <div class="register-link">
            Don't have an account?
            <a href="driver_register.php">Register now</a>
        </div>
    </div>
</body>
</html>