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
    $profile_picture_folder = '../drivers_profile/' . $profile_picture;

    // Check if the directory exists, if not create it
    if (!is_dir('../drivers_profile/')) {
        mkdir('../drivers_profile/', 0777, true);
    }

    // Check if the number already exists
    $check_number = $conn->prepare("SELECT * FROM drivers WHERE number = ?");
    $check_number->execute([$number]);

    if ($check_number->rowCount() > 0) {
        echo "<p style='color:red; text-align:center;'>Error: This contact number is already registered.</p>";
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
                echo "<p style='color:green; text-align:center;'>Registration successful!</p>";
            } else {
                echo "<p style='color:red; text-align:center;'>Error: Could not register driver.</p>";
            }
        } catch (PDOException $e) {
            echo "<p style='color:red; text-align:center;'>Error: " . $e->getMessage() . "</p>";
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