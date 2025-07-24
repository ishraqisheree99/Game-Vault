<?php
include 'config.php';
if(!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
$user_id = $_SESSION['user_id'];
$conn->query("DELETE FROM cart WHERE user_id=$user_id");
echo "Thanks for your purchase!";
?>
<a href="index.php">Back to Shop</a>
