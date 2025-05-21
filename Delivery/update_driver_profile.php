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

<form method="post" action="update_driver_profile.php" enctype="multipart/form-data" style="max-width: 400px; margin: auto;">
    <h3>Update Profile</h3>
    <input type="text" name="name" value="<?= htmlspecialchars($driver['name']) ?>" placeholder="Driver Name" required style="width:100%; padding:10px; margin-bottom:10px;">
    <input type="text" name="number" value="<?= htmlspecialchars($driver['number']) ?>" placeholder="Contact Number" required style="width:100%; padding:10px; margin-bottom:10px;">
    <input type="text" name="address" value="<?= htmlspecialchars($driver['address']) ?>" placeholder="Address" required style="width:100%; padding:10px; margin-bottom:10px;">
    <select name="gender" required style="width:100%; padding:10px; margin-bottom:10px;">
        <option value="Male" <?= $driver['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
        <option value="Female" <?= $driver['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
    </select>
    <input type="file" name="drivers_picture" accept="image/*" style="width:100%; padding:10px; margin-bottom:10px;">
    <button type="submit" style="padding:10px; width:100%;">Update Profile</button>
</form>

<!-- Back Button -->
<div style="text-align: center; margin-top: 20px;">
    <a href="driver_home.php" style="padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;">Back</a>
</div>