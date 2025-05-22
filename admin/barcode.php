<?php
require 'vendor/autoload.php';

// Database connection
// $host = 'localhost'; // Change to your database host
// $user = 'root'; // Change to your database username
// $pass = ''; // Change to your database password
// $dbname = 'your_database_name'; // Change to your database name

$host = 'localhost';
$user = 'u866427573_bagdeluxe';
$pass = '@Qetu1357';
$dbname = 'u866427573_bagdeluxe';

$conn = new mysqli($host, $user, $pass, $dbname);
 
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

use Picqer\Barcode\BarcodeGeneratorPNG;

$generator = new BarcodeGeneratorPNG();
$barcodeValue = '123456789'; // Change this to any unique value
$barcode = $generator->getBarcode($barcodeValue, $generator::TYPE_CODE_128);

$imagePath = 'barcodes/' . $barcodeValue . '.png';

// Ensure the barcodes directory exists
if (!file_exists('barcodes')) {
    mkdir('barcodes', 0777, true);
}

// Save the barcode as an image
file_put_contents($imagePath, $barcode);

// Insert barcode into database
$stmt = $conn->prepare("INSERT INTO barcodes (barcode_value, barcode_image) VALUES (?, ?)");
$stmt->bind_param("ss", $barcodeValue, $imagePath);
$stmt->execute();
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barcode Generator</title>
</head>
<body>
    <h2>Generated Barcode</h2>
    <p>Barcode Value: <strong><?php echo $barcodeValue; ?></strong></p>
    <img src="<?php echo $imagePath; ?>" alt="Barcode">
</body>
</html>
