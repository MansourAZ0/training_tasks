<?php
// Copy this file to db.php on the server and replace the placeholders.
$host = 'sqlXXX.infinityfree.com';
$user = 'epiz_XXXXXXXX';
$pass = 'your_password_here';
$dbname = 'epiz_XXXXXXXX_control_db';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_errno) {
    throw new RuntimeException('Database connection failed');
}
$conn->set_charset('utf8mb4');
