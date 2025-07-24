<?php
include '../config.php';
if($_SERVER['REQUEST_METHOD']=='POST'){
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $result = $conn->query("SELECT * FROM users WHERE username='$username' AND password='$password' AND role='admin'");
    if($result->num_rows){
        $_SESSION['admin'] = true;
        header("Location: dashboard.php");
        exit;
    } else echo "Invalid admin credentials!";
}
?>
<form method="post">
Username: <input name="username">
Password: <input type="password" name="password">
<button type="submit">Login</button>
</form>
