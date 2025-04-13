<?php
$passwords = ['admin123', 'patient123', 'doctor123'];
foreach ($passwords as $password) {
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    echo "$password: $hashed_password\n";
}
?>