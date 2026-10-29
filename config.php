<?php
$host = "mysql-2d98edcb-lizwangechi14-4433.g.aivencloud.com";
$username = "avnadmin";
$password = "mysql://avnadmin:AVNS_ryirVBZoQS-rByLNV-k@mysql-2d98edcb-lizwangechi14-4433.g.aivencloud.com:24251/defaultdb?ssl-mode=REQUIRED";
$database = "defaultdb";
$port = 24251;

$conn = mysqli_init();
mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);
mysqli_real_connect($conn, $host, $username, $password, $database, $port, NULL, MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
