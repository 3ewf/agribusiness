<?php
// Local XAMPP defaults. For hosting, set real credentials using environment variables or edit this file privately.
$dbHost = getenv('AGRIBUSINESS_DB_HOST') ?: '127.0.0.1';
$dbName = getenv('AGRIBUSINESS_DB_NAME') ?: 'Agribusiness';
$dbUser = getenv('AGRIBUSINESS_DB_USER') ?: 'root';
$dbPass = getenv('AGRIBUSINESS_DB_PASS') ?: '';
try { $pdo = new PDO("mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4", $dbUser, $dbPass, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES=>false]); }
catch(PDOException $ex) { http_response_code(500); error_log($ex->getMessage()); exit('Agribusiness cannot connect to its database. Check config.php and import database/agrihub.sql.'); }
