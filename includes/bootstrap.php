<?php
// Local XAMPP setup: create database with database/Agribusiness.sql, then configure credentials in config.php.
if (session_status() !== PHP_SESSION_ACTIVE) { session_set_cookie_params(['httponly'=>true,'samesite'=>'Lax','secure'=>!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off']); session_start(); }
require_once __DIR__.'/../config.php';
function e($v){return htmlspecialchars((string)$v, ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8');}
function current_user(){return $_SESSION['user']??null;}
function require_login(){if(!current_user()){flash('error','Please log in first.');redirect('login.php');}}
function require_role($role){require_login();if(current_user()['role']!==$role){http_response_code(403);exit('Access denied.');}}
function redirect($path){header('Location: '.$path);exit;}
function csrf_token(){if(empty($_SESSION['csrf']))$_SESSION['csrf']=bin2hex(random_bytes(32));return $_SESSION['csrf'];}
function csrf_field(){return '<input type="hidden" name="csrf" value="'.e(csrf_token()).'">';}
function verify_csrf(){if(!isset($_POST['csrf'])||!hash_equals($_SESSION['csrf']??'',(string)$_POST['csrf'])){http_response_code(419);exit('Invalid form token. Refresh and try again.');}}
function flash($type,$message){$_SESSION['flash'][]=['type'=>$type,'message'=>$message];}
function show_flash(){$all=$_SESSION['flash']??[];unset($_SESSION['flash']);$out='';foreach($all as $f)$out.='<div class="notice '.e($f['type']).'">'.e($f['message']).'</div>';return $out;}
