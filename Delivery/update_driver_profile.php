<!-- filepath: c:\Users\Wrhinnon\OneDrive\Desktop\Xaampp\htdocs\bagdeluxe\Delivery\update_driver_profile.php -->
<?php
include '../components/connect.php';

session_start();

$driver_id = $_SESSION['driver_id'] ?? null;

if (!$driver_id) {
    header('Location: driver_login.php');
    exit;
}

// Fetch driver details
$stmt = $conn->prepare("SELECT * FROM drivers WHERE id = ?");
$stmt->execute([$driver_id]);
$driver = $stmt->fetch(PDO::FETCH_ASSOC);

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $number = $_POST['number'];
    $address = $_POST['address'];
    $gender = $_POST['gender'];

    // Handle profile picture upload
    if (!empty($_FILES['drivers_picture']['name'])) {
        $profile_picture = basename($_FILES['drivers_picture']['name']);
        $profile_picture_tmp = $_FILES['drivers_picture']['tmp_name'];
        $profile_picture_folder = '../drivers_profile/' . $profile_picture;

        // Ensure the directory exists
        if (!is_dir('../drivers_profile')) {
            mkdir('../drivers_profile', 0777, true);
        }

        // Move the uploaded file to the correct folder
        if (move_uploaded_file($profile_picture_tmp, $profile_picture_folder)) {
            $drivers_picture = $profile_picture;
        } else {
            $message = "<span style='color:#b23232;'>Failed to upload profile picture.</span>";
            $drivers_picture = $driver['drivers_picture'];
        }
    } else {
        $drivers_picture = $driver['drivers_picture'] ?? '';
    }

    // Update driver details
    $update_stmt = $conn->prepare("UPDATE drivers SET name = ?, number = ?, address = ?, gender = ?, drivers_picture = ? WHERE id = ?");
    if ($update_stmt->execute([$name, $number, $address, $gender, $drivers_picture, $driver_id])) {
        $message = "<span style='color:green;'>Profile updated successfully!</span>";
        // Refresh driver details
        $stmt->execute([$driver_id]);
        $driver = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $message = "<span style='color:#b23232;'>Failed to update profile.</span>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Driver Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #682020, #b23232);
            color: #7d2626;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background-color: rgba(255, 255, 255, 0.13);
            padding: 24px 24px 18px 24px;
            border-radius: 18px;
            box-shadow: 0 8px 32px 0 rgba(125, 38, 38, 0.18);
            width: 400px;
            text-align: center;
        }

        h3 {
            color: #7d2626;
            margin-bottom: 20px;
            font-size: 2rem;
            font-weight: bold;
            letter-spacing: 1px;
            text-shadow: 0 2px 8px #fff2;
        }

        input[type="text"],
        input[type="password"],
        select {
            width: 100%;
            padding: 13px 16px;
            margin: 10px 0;
            border: 1.5px solid #b23232;
            border-radius: 12px;
            background-color: #fff;
            color: #7d2626;
            box-sizing: border-box;
            font-size: 1em;
            box-shadow: 0 2px 10px #7d262610;
            outline: none;
            transition: border 0.2s, box-shadow 0.2s;
        }

        input[type="text"]:focus,
        input[type="password"]:focus,
        select:focus {
            border: 2px solid #ff7eb3;
            box-shadow: 0 2px 12px #ff7eb344;
        }

        input[type="text"]::placeholder,
        input[type="password"]::placeholder {
            color: #b23232;
            opacity: 0.7;
        }

        select {
            appearance: none;
            background-image: url('data:image/svg+xml;utf8,<svg fill="#b23232" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 4 5"><path d="M2 0L0 2h4zm0 5L0 3h4z"/></svg>');
            background-repeat: no-repeat;
            background-position-x: 95%;
            background-position-y: 50%;
        }

        input[type="file"] {
            background: #fff;
            border-radius: 12px;
            padding: 10px;
            color: #7d2626;
            border: 1.5px solid #b23232;
            font-size: 1em;
            margin: 10px 0;
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
            width: 91%;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #b23232;
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: background 0.2s;
        }
        .back-link:hover {
            background: #7d2626;
            color: #fffbe7;
        }
    </style>
</head>
<body>
    <div class="container">
        <h3>Update Profile</h3>
        <?php if (!empty($message)): ?>
            <div class="message"><?= $message ?></div>
        <?php endif; ?>
        <form method="post" action="update_driver_profile.php" enctype="multipart/form-data">
            <input type="text" name="name" value="<?= htmlspecialchars($driver['name']) ?>" placeholder="Driver Name" required>
            <input type="text" name="number" value="<?= htmlspecialchars($driver['number']) ?>" placeholder="Contact Number" required>
            <input type="text" name="address" value="<?= htmlspecialchars($driver['address']) ?>" placeholder="Address" required>
            <select name="gender" required>
                <option value="Male" <?= $driver['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
                <option value="Female" <?= $driver['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
            </select>
            <input type="file" name="drivers_picture" accept="image/*">
            <button type="submit">Update Profile</button>
        </form>
        <div style="text-align: center; margin-top: 20px;">
            <a href="driver_home.php" class="back-link">Back</a>
        </div>
    </div>
</body>
</html>