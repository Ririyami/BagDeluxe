<?php
include '../components/connect.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $number = $_POST['number'];
    $address = $_POST['address'];
    $gender = $_POST['gender'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Handle profile picture upload
    $profile_picture = $_FILES['profile_picture']['name'];
    $profile_picture_tmp = $_FILES['profile_picture']['tmp_name'];
    $profile_picture_folder = '../drivers_profile/' . $profile_picture;

    // Check if the directory exists, if not create it
    if (!is_dir('../drivers_profile/')) {
        mkdir('../drivers_profile/', 0777, true);
    }

    // Check if the number already exists
    $check_number = $conn->prepare("SELECT * FROM drivers WHERE number = ?");
    $check_number->execute([$number]);

    if ($check_number->rowCount() > 0) {
        $message = "Error: This contact number is already registered.";
    } else {
        try {
            $stmt = $conn->prepare("INSERT INTO drivers (name, number, gender, address, password, drivers_picture) VALUES (:name, :number, :gender, :address, :password, :drivers_picture)");
            $stmt->bindValue(':name', $name);
            $stmt->bindValue(':number', $number);
            $stmt->bindValue(':gender', $gender);
            $stmt->bindValue(':address', $address);
            $stmt->bindValue(':password', $password);
            $stmt->bindValue(':drivers_picture', $profile_picture);

            if ($stmt->execute()) {
                move_uploaded_file($profile_picture_tmp, $profile_picture_folder);
                $message = "Registration successful!";
            } else {
                $message = "Error: Could not register driver.";
            }
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Registration</title>
    <style>
        body {
            font-family: 'Montserrat', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #682020;
            color: #7d2626;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
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
            margin: 0 auto;
            position: relative;
            z-index: 2;
            overflow: hidden;
            transition: box-shadow 0.3s;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h3 {
            color: #7d2626;
            font-size: 2rem;
            font-weight: bold;
            letter-spacing: 1px;
            margin-bottom: 18px;
            text-shadow: 0 2px 8px #fff2;
        }

        form {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        input[type="text"],
        input[type="password"],
        select {
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

        form input[type="text"],
        form input[type="password"],
        form select,
        form input[type="file"] {
            width: 100%;
            box-sizing: border-box;
            display: block;
            margin: 0;
        }

        input[type="file"] {
            background: #fff;
            border-radius: 12px;
            padding: 10px;
            color: #7d2626;
            border: 1.5px solid #b23232;
            font-size: 1em;
        }

        form input[type="file"] {
            padding: 10px 0 10px 0;
            background: #fff;
            border-radius: 12px;
            color: #7d2626;
            border: 1.5px solid #b23232;
            font-size: 1em;
            font-family: inherit;
        }

        input[type="text"]:focus,
        input[type="password"]:focus,
        select:focus {
            border: 2px solid #ff7eb3;
            box-shadow: 0 2px 12px #ff7eb344;
        }

        select {
            appearance: none;
            background-image: url('data:image/svg+xml;utf8,<svg fill="#b23232" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 4 5"><path d="M2 0L0 2h4zm0 5L0 3h4z"/></svg>');
            background-repeat: no-repeat;
            background-position-x: 95%;
            background-position-y: 50%;
        }

        button {
            width: 100%;
            padding: 13px;
            margin-top: 10px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(90deg, #b23232 0%, #7d2626 100%);
            color: #fff;
            font-size: 1.15em;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 2px 12px #7d262655;
            transition: background 0.2s, transform 0.13s;
            letter-spacing: 1px;
        }

        button:hover {
            background: linear-gradient(90deg, #ff7eb3 0%, #b23232 100%);
            color: #fffbe7;
            transform: translateY(-2px) scale(1.04);
        }

        .login-link {
            display: block;
            margin-top: 20px;
            color: #7d2626;
            text-decoration: none;
            font-weight: 500;
            text-align: center;
            transition: color 0.2s;
        }

        .login-link:hover {
            color: #b23232;
            text-decoration: underline;
        }

        @media (max-width: 600px) {
            .container {
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
        width: 100%;
    }
    @media (max-width: 500px) {
        .container { width: 98vw; padding: 30px 6px 26px 6px;}
        .form-title { font-size: 1.5rem; }
    }
    </style>
</head>
<body>
    <div class="container">
        <h3>Driver Registration</h3>
        <?php if (!empty($message)): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data">
            <input type="text" name="name" placeholder="Driver Name" required>
            <input type="text" name="number" placeholder="Contact Number" required>
            <input type="text" name="address" placeholder="Address" required>
            <select name="gender" required>
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
            <input type="password" name="password" placeholder="Password" required>
            <input type="file" name="profile_picture" accept="image/*" required>
            <button type="submit">Register</button>
        </form>
        <div class="login-link">
            Already have an account? <a href="driver_login.php">Log In</a>
        </div>
    </div>
</body>
</html>