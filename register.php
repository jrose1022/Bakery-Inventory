<?php
require_once "dbConfig.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $fname = $_POST['first_name'];
    $lname = $_POST['last_name'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = $pdo->prepare("SELECT * FROM users WHERE username=?");
    $check->execute([$username]);

    if ($check->rowCount() > 0) {
        echo "Username already exists";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, username, password) VALUES (?, ?, ?, ?)");
        $stmt->execute([$fname, $lname, $username, $password]);

        echo "success";
    }
}
?>