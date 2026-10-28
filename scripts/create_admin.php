<?php
// Run once locally: php scripts/create_admin.php admin@example.com 'ReplaceThisStrongPassword!'
require __DIR__.'/../config.php';
if(PHP_SAPI!=='cli'){http_response_code(403);exit("Run from command line only.
");}
if($argc<3||strlen($argv[2])<12){fwrite(STDERR,"Usage: php scripts/create_admin.php email strong-password (minimum 12 chars)
");exit(1);}
$email=strtolower(trim($argv[1]));if(!filter_var($email,FILTER_VALIDATE_EMAIL)){fwrite(STDERR,"Invalid email
");exit(1);}
$st=$pdo->prepare("INSERT INTO users(name,email,password_hash,role) VALUES('Agrihub Administrator',?,?, 'admin')");$st->execute([$email,password_hash($argv[2],PASSWORD_DEFAULT)]);echo "Admin account created. Remove or keep this script outside public web root.
";
