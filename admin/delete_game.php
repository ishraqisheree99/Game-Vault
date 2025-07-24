<?php
include '../config.php';
if(!isset($_SESSION['admin'])){ header("Location: login.php"); exit; }

$id = (int)$_GET['id'];

// Get game data first to delete associated image
$game_result = $conn->query("SELECT * FROM games WHERE id=$id");
if($game_result->num_rows == 0) {
    header("Location: dashboard.php");
    exit;
}

$game = $game_result->fetch_assoc();

// Delete the game from database
if($conn->query("DELETE FROM games WHERE id=$id")) {
    // Delete associated image file if it exists
    if(!empty($game['image']) && file_exists('../' . $game['image'])) {
        unlink('../' . $game['image']);
    }
    
    // Also clean up any cart entries for this game
    $conn->query("DELETE FROM cart WHERE game_id=$id");
}

header("Location: dashboard.php");
exit;
?>