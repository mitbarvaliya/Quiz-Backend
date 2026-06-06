<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=quiz_app', 'root', '');
$r = $pdo->query("SELECT otp, used_at, expires_at FROM otps WHERE email = 'test@example.com' AND type = 'registration' ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
echo json_encode($r ?: 'none', JSON_PRETTY_PRINT) . "\n";
