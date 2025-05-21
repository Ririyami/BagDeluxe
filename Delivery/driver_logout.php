<!-- filepath: c:\Users\Wrhinnon\OneDrive\Desktop\Xaampp\htdocs\bagdeluxe\Delivery\logout.php -->
<?php
session_start();
session_unset();
session_destroy();
header('Location: driver_login.php');
exit;
?>