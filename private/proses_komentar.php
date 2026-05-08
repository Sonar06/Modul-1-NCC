<?php
session_start();
require_once __DIR__ . "/../config/db.php";

// Pastikan user login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Ambil ID User dari database berdasarkan username di session
// (Karena session kamu menyimpan username, kita butuh ID-nya buat disimpan di tabel comments)
$username = $_SESSION['username'];
$queryUser = $conn->query("SELECT id FROM users WHERE username = '$username'");
$userData = $queryUser->fetch_assoc();
$user_id = $userData['id'];

// Ambil data dari form
$article_id = $_POST['article_id'];
$isi_komentar = $_POST['isi_komentar'];

// Simpan ke database
$stmt = $conn->prepare("INSERT INTO comments (article_id, user_id, comment_text) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $article_id, $user_id, $isi_komentar);

if ($stmt->execute()) {
    // Balik ke halaman artikel tadi
    header("Location: ../public/article.php?id=" . $article_id);
} else {
    echo "Gagal kirim komentar.";
}
?>