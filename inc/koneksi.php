<?php
$host = 'localhost';
$user = 'root';
$password = '';
$db = 'kasir';

$conn = mysqli_connect($host,$user,$password,$db);

error_reporting(E_ALL ^ E_WARNING);
?>