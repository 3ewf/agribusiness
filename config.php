<?php
$host = "mysql-2d98edcb-lizwangechi14-4433.g.aivencloud.com";
$username = "avnadmin";
$password = "mysql://avnadmin:AVNS_ryirVBZoQS-rByLNV-k@mysql-2d98edcb-lizwangechi14-4433.g.aivencloud.com:24251/defaultdb?ssl-mode=REQUIRED;
$database = "defaultdb";
$port = 24251;

$conn = mysqli_connect($host, $username, $password, $database, $port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
