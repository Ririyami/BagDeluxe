<?php
// $host = 'localhost';
// $user = 'root';
// $pass = '';
// $dbname = 'bag_db';

$host = 'localhost';
$user = 'u866427573_bagdeluxe';
$pass = '@Qetu1357';
$dbname = 'u866427573_bagdeluxe';
 
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM barcodes";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stored Barcodes</title>
</head>
<body>
    <h2>Stored Barcodes</h2>
    <?php while ($row = $result->fetch_assoc()) { ?>
        <p>Barcode Value: <strong><?php echo $row['barcode_value']; ?></strong></p>
        <img src="<?php echo $row['barcode_image']; ?>" alt="Barcode">
        <hr>
    <?php } ?>
</body>
</html>

<?php $conn->close(); ?>
