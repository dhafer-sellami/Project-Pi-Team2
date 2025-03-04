<?php
$dsn = 'mysql:host=127.0.0.1;dbname=meditrackdb';
$user = 'root';
$password = '';

try {
    $pdo = new PDO($dsn, $user, $password);
    echo "Connected successfully!";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>