<?php
// 1. Wajib start session
session_start();

// 2. Hubungkan koneksi database
require_once __DIR__ . "/../config/db.php";

// 3. Tangkap data dari form
$username = $_POST['username'];
$password = $_POST['password'];

// 4. Query cek data user (Pakai Prepared Statement biar aman)
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
$stmt->bind_param("ss", $username, $password);
$stmt->execute();
$result = $stmt->get_result();

// 5. Cek apakah data ditemukan
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // -- SUKSES LOGIN --
    // Simpan data user ke sesi
    $_SESSION['username'] = $row['username'];
    $_SESSION['role']     = $row['role']; 
    $_SESSION['status']   = "login";

    // Alihkan ke halaman utama (index)
    header("Location: ../public/index.php");
} else {
    // -- GAGAL LOGIN --
    // Alihkan kembali ke halaman login dengan membawa pesan error
    header("Location: ../public/login.php?pesan=gagal");
}
?>