<?php
require 'config.php';

$stmt = $pdo -> query("SELECT * FROM users INNER JOIN orders on users.users_id = orders.users_id");
$users = $stmt ->fetchAll(PDO:: FETCH_ASSOC);