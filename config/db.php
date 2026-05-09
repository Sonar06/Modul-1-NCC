<?php
$host = "mysql-db";
$user = "root";
$pass = "root";
$db   = "iniberita";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>