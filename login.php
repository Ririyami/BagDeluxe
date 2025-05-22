<?php
include 'components/connect.php';
session_start();

if (isset($_SESSION['user_id'])) {
    header('location:home.php');
    exit();
}

$message = [];
if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $email = filter_var($email, FILTER_SANITIZE_STRING);
    $pass = $_POST['pass'];

    $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
    $select_user->execute([$email]);
    $row = $select_user->fetch(PDO::FETCH_ASSOC);

    if ($select_user->rowCount() > 0) {
        if (password_verify($pass, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['name'];
            $_SESSION['profile_picture'] = $row['profile_picture'];
            header('location:home.php');
            exit();
        } else {
            $message[] = 'Invalid password.';
        }
    } else {
        $message[] = 'User not found.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style>
    body {
        min-height: 100vh;
        background: linear-gradient(130deg, #f857a6 0%, #ff5858 100%);
        display: flex;
        justify-content: center;
        align-items: center;
        font-family: 'Segoe UI', 'Arial', sans-serif;
        overflow: hidden;
        margin: 0;
    }
    .container {
        background: linear-gradient(135deg, #fdf6e3 30%, #fceabb 100%);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.2);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border-radius: 20px;
        border: 1.5px solid rgba(255,255,255,0.25);
        width: 370px;
        padding: 42px 34px 38px 34px;
        position: relative;
        z-index: 2;
        overflow: hidden;
        transition: height 0.3s;
    }
    .form-title {
        text-align: center;
        font-size: 2rem;
        font-weight: bold;
        color: #b23232;
        letter-spacing: 1px;
        margin-bottom: 24px;
        transition: color 0.2s;
        text-shadow: 0 2px 8px #3332;
    }
    form {
        display: flex;
        flex-direction: column;
        gap: 18px;
        z-index: 2;
        transition: all 0.3s;
    }
    .form-group {
        position: relative;
    }
    .form-group input {
        width: 100%;
        padding: 13px 16px 13px 44px;
        border: none;
        border-radius: 10px;
        background: rgba(253, 246, 227, 0.7);
        font-size: 1em;
        color: #b23232;
        box-shadow: 0 2px 10px #0001;
        outline: none;
        transition: background 0.2s;
    }
    .form-group input:focus {
        background: rgba(253, 246, 227, 1);
    }
    .form-group .fa {
        position: absolute;
        left: 13px;
        top: 50%;
        transform: translateY(-50%);
        color: #b23232;
        font-size: 1.13em;
        pointer-events: none;
    }
    button[type="submit"] {
        background: linear-gradient(90deg, #c94b4b 0%, #4b134f 100%);
        color: #fff;
        border: none;
        padding: 12px;
        border-radius: 10px;
        font-size: 1.1em;
        font-weight: 600;
        cursor: pointer;
        box-shadow: 0 2px 8px #c94b4b70;
        margin-top: 6px;
        transition: background 0.2s, transform 0.13s;
        letter-spacing: 1px;
    }
    button[type="submit"]:hover {
        background: linear-gradient(90deg, #4b134f 0%, #c94b4b 100%);
        transform: translateY(-2px) scale(1.03);
    }
    .toggle-link {
        text-align: center;
        color: #b23232;
        margin-top: 20px;
        font-size: 0.97em;
        opacity: 0.96;
    }
    .toggle-link a {
        color: #c94b4b;
        cursor: pointer;
        font-weight: 500;
        text-decoration: underline;
        margin-left: 6px;
        transition: color 0.18s;
    }
    .toggle-link a:hover {
        color: #4b134f;
    }
    .message {
        background: #fff4;
        color: #c94b4b;
        border-radius: 7px;
        padding: 10px 15px;
        margin-bottom: 12px;
        text-align: center;
        font-size: 1em;
        box-shadow: 0 1px 4px #0001;
    }
    @media (max-width: 450px) {
        .container { width: 96vw; padding: 35px 10px 34px 10px;}
    }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-title">Sign In</div>
        <?php if (!empty($message)) foreach ($message as $msg): ?>
            <div class="message"><?= htmlspecialchars($msg) ?></div>
        <?php endforeach; ?>
        <form method="post" action="login.php" autocomplete="on">
            <div class="form-group">
                <i class="fa fa-envelope"></i>
                <input type="email" name="email" placeholder="Email" required maxlength="50">
            </div>
            <div class="form-group">
                <i class="fa fa-lock"></i>
                <input type="password" name="pass" placeholder="Password" required maxlength="50">
            </div>
            <button type="submit" name="submit" value="1">Sign In</button>
        </form>
        <div class="toggle-link">
            Don't have an account?
            <a href="register.php">Sign up</a>
        </div>
    </div>
</body>
</html>
