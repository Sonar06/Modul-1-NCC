<?php
// Hubungkan database
require_once __DIR__ . "/../config/db.php";

// Tangkap data dari form
$username = $_POST['username'];
$password = $_POST['password'];
$confirm  = $_POST['password_confirm'];
$role     = $_POST['role'];

// 1. Validasi: Cek apakah Password dan Konfirmasi sama?
if ($password !== $confirm) {
    header("Location: ../public/daftar.php?pesan=gagal_pass");
    exit;
}

// 2. Validasi: Cek apakah Username sudah ada di database?
// Kita tidak mau ada 2 user dengan username yang sama
$checkQuery = $conn->prepare("SELECT username FROM users WHERE username = ?");
$checkQuery->bind_param("s", $username);
$checkQuery->execute();
$result = $checkQuery->get_result();

if ($result->num_rows > 0) {
    // Jika username sudah ada
    header("Location: ../public/daftar.php?pesan=gagal_user");
    exit;
}

// 3. Simpan ke Database
// (Password disimpan plain text sesuai request sebelumnya)
$stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $username, $password, $role);

if ($stmt->execute()) {
    session_start();

    // B. Langsung set data user ke dalam sesi (Auto Login)
    $_SESSION['username'] = $username;
    $_SESSION['role']     = $role;
    $_SESSION['status']   = "login";
    // Berhasil daftar, arahkan ke login atau tampilkan sukses
    header("Location: ../public/index.php");
    exit;
} else {
    echo "Error: " . $conn->error;
}
?>