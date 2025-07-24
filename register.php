<?php
include 'config.php';
if($_SERVER['REQUEST_METHOD']=='POST'){
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $conn->query("INSERT INTO users (username, password, role) VALUES ('$username', '$password', 'user')");
    echo "Registered! <a href='login.php'>Login now</a>";
    exit;
}
?>
<form method="post">
Username: <input name="username">
Password: <input type="password" name="password">
<button type="submit">Register</button>
</form>
