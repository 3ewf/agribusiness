<?php
$host = "mysql-2d98edcb-lizwangechi14-4433.g.aivencloud.com";
$username = "avnadmin";
$password = "PASTE_YOUR_AIVEN_PASSWORD_HERE";
$database = "defaultdb";
$port = 24251;

$conn = mysqli_connect($host, $username, $password, $database, $port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
