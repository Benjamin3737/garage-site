<?php
$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=garage_site;charset=utf8mb4', 'root', '');
$hash = '$2y$13$pMWmnpmqZ0MqOrvfeqdPEe1r1lH1BYkFUXV2nYA0DbY.1IvolSLWS';
$email = 'benjamin.huynh.marais@gmail.com';
$stmt = $pdo->prepare('UPDATE `user` SET `password` = ? WHERE `email` = ?');
$stmt->execute([$hash, $email]);
echo $stmt->rowCount() . " row(s) updated\n";
$stmt = $pdo->prepare('SELECT id, email, password FROM `user` WHERE `email` = ?');
$stmt->execute([$email]);
print_r($stmt->fetch(PDO::FETCH_ASSOC));
