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
        background: linear-gradient(120deg, #7d2626 0%, #b23232 100%);
        display: flex;
        justify-content: center;
        align-items: center;
        font-family: 'Segoe UI', 'Arial', sans-serif;
        margin: 0;
        overflow: hidden;
    }
    .container {
        background: rgba(255,255,255,0.13);
        box-shadow: 0 8px 32px 0 rgba(125, 38, 38, 0.18);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border-radius: 22px;
        border: 2px solid #b23232;
        width: 370px;
        padding: 44px 36px 38px 36px;
        position: relative;
        z-index: 2;
        overflow: hidden;
        transition: box-shadow 0.3s;
    }
    .form-title {
        text-align: center;
        font-size: 2.2rem;
        font-weight: bold;
        color: #7d2626;
        letter-spacing: 1px;
        margin-bottom: 28px;
        transition: color 0.2s;
        text-shadow: 0 2px 8px #fff2;
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
    .form-group input[type="email"],
    .form-group input[type="password"] {
        width: 84%;
        padding: 13px 16px 13px 44px;
        border: 1.5px solid #b23232;
        border-radius: 12px;
        background: #fff;
        font-size: 1em;
        color: #7d2626;
        box-shadow: 0 2px 10px #7d262610;
        outline: none;
        transition: border 0.2s, box-shadow 0.2s;
    }
    .form-group input:focus {
        border: 2px solid #ff7eb3;
        box-shadow: 0 2px 12px #ff7eb344;
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
    button[type="submit"]:hover {
        background: linear-gradient(90deg, #ff7eb3 0%, #b23232 100%);
        color: #fffbe7;
        transform: translateY(-2px) scale(1.04);
    }
    .toggle-link {
        text-align: center;
        color: #7d2626;
        margin-top: 22px;
        font-size: 1em;
        opacity: 0.96;
    }
    .toggle-link a {
        color: #ff7eb3;
        cursor: pointer;
        font-weight: 500;
        text-decoration: underline;
        margin-left: 6px;
        transition: color 0.18s;
    }
    .toggle-link a:hover {
        color: #b23232;
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
