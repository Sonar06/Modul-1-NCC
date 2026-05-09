<?php
$host = "mysql-db";     // Sesuai container_name di docker-compose
$user = "root";
$pass = "rootpass";     // SESUAIKAN: tadi di docker-compose kamu pakai 'rootpass'
$db   = "iniberita";    // SESUAIKAN: tadi di docker-compose kamu pakai 'iniberita'

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>