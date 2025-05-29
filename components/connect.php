<?php

//  $db_name = 'mysql:host=localhost;dbname=deluxee_db';
//  $user_name = 'root';
//  $user_password = '';

$host = 'localhost';
$db_name = 'u866427573_bagdeluxe';
$user_name = 'u866427573_bagdeluxe';
$user_password = '@Qetu1357';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $user_name, $user_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

?>
 