<?php
include 'config.php';
if(!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$user_id = $_SESSION['user_id'];

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $game_id = (int)$_POST['game_id'];
    $conn->query("INSERT INTO cart (user_id, game_id, quantity) VALUES ($user_id, $game_id, 1)");
}

$cart = $conn->query("SELECT c.id, g.title, g.price FROM cart c JOIN games g ON c.game_id=g.id WHERE c.user_id=$user_id");
?>
<h2>Your Cart</h2>
<ul>
<?php while($item = $cart->fetch_assoc()): ?>
<li><?= htmlspecialchars($item['title']) ?> - $<?= $item['price'] ?></li>
<?php endwhile; ?>
</ul>
<a href="checkout.php">Checkout</a> | <a href="index.php">Back to Shop</a>
