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
            mkdir('../drivers_profile', 0777, true); // Create the directory with proper permissions
        }

        // Move the uploaded file to the correct folder
        if (move_uploaded_file($profile_picture_tmp, $profile_picture_folder)) {
            $drivers_picture = $profile_picture; // Use the new uploaded file
        } else {
            echo "<p style='color:red; text-align:center;'>Failed to upload profile picture.</p>";
            $drivers_picture = $driver['drivers_picture']; // Use the existing profile picture
        }
    } else {
        $drivers_picture = $driver['drivers_picture'] ?? ''; // Use the existing profile picture or set to empty
    }

    // Update driver details
    $update_stmt = $conn->prepare("UPDATE drivers SET name = ?, number = ?, address = ?, gender = ?, drivers_picture = ? WHERE id = ?");
    if ($update_stmt->execute([$name, $number, $address, $gender, $drivers_picture, $driver_id])) {
        echo "<p style='color:green; text-align:center;'>Profile updated successfully!</p>";
        // Refresh driver details
        $stmt->execute([$driver_id]);
        $driver = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        echo "<p style='color:red; text-align:center;'>Failed to update profile.</p>";
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
        input[type="password"],
        select {
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
            color: rgba(255, 255, 255, 0.7);
        }

        select {
            appearance: none; /* Remove default arrow */
            background-image: url('data:image/svg+xml;utf8,<svg fill="white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 4 5"><path d="M2 0L0 2h4zm0 5L0 3h4z"/></svg>');
            background-repeat: no-repeat;
            background-position-x: 95%;
            background-position-y: 50%;
        }

        button {
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

        button:hover {
            background-color: #c9302c;
        }

        .login-link {
            display: block;
            margin-top: 20px;
            color: white;
            text-decoration: none;
        }

        .login-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h3>Update Profile</h3>
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
            <a href="driver_home.php" style="padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;">Back</a>
        </div>
    </div>
</body>
</html>