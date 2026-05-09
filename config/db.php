<?php
$host = "mysql-db";
$user = "root";
$pass = "root";
$db   = "newsdb";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>