<?php
$host = 'localhost';
$user = 'root';
$password = '';
$db = 'kasir';

$conn = mysqli_connect($host,$user,$password,$db);
date_default_timezone_set('Asia/Jakarta');

error_reporting(E_ALL ^ E_WARNING);
?>