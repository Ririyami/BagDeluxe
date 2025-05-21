<?php
include '../components/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $number = $_POST['number'];
    $address = $_POST['address'];
    $gender = $_POST['gender'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Handle profile picture upload
    $profile_picture = $_FILES['profile_picture']['name'];
    $profile_picture_tmp = $_FILES['profile_picture']['tmp_name'];
    $profile_picture_folder = '../driver\'s profile/drivers/' . $profile_picture;

    try {
        $stmt = $conn->prepare("INSERT INTO drivers (name, number, address, gender, password, profile_picture) VALUES (:name, :number, :address, :gender, :password, :profile_picture)");
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':number', $number);
        $stmt->bindValue(':address', $address);
        $stmt->bindValue(':gender', $gender);
        $stmt->bindValue(':password', $password);
        $stmt->bindValue(':profile_picture', $profile_picture);

        if ($stmt->execute()) {
            move_uploaded_file($profile_picture_tmp, $profile_picture_folder);
            echo "<p style='color:green; text-align:center;'>Registration successful!</p>";
        } else {
            echo "<p style='color:red; text-align:center;'>Error: Could not register driver.</p>";
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
    <title>Driver Registration</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <h3>Driver Registration</h3>
        <form method="post" action="driver_register.php" enctype="multipart/form-data">
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
        <a href="driver_login.php" class="login-link">Already have an account? Log In</a>
    </div>
</body>
</html>