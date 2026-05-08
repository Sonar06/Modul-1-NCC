<?php
session_start();
require_once __DIR__ . "/../config/db.php";

// 1. Cek Hak Akses (Wajib Login & Role Author/Admin)
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'author' && $_SESSION['role'] != 'admin')) {
    die("Akses ditolak!");
}

// 2. Tangkap Data Text
$title       = $_POST['title'];
$slug        = $_POST['slug'];
$desc        = $_POST['description'];
$content     = $_POST['content'];
$category    = $_POST['kategori'];
$published_at = $_POST['published_at'];
$author_name = $_SESSION['username']; // Ambil nama penulis dari sesi login

// 3. Proses Upload Gambar
$imagePath = "";

if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
    // Siapkan folder (pastikan folder 'uploads' sudah dibuat di proyekmu)
    $targetDir = "uploads/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    // Ganti nama file biar unik (time + nama asli)
    $fileName = time() . "_" . basename($_FILES['image']['name']);
    $targetFilePath = $targetDir . $fileName;
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

    // Validasi ekstensi
    $allowedTypes = array('jpg', 'jpeg', 'png', 'gif', 'webp');
    if (in_array($fileType, $allowedTypes)) {
        // Pindahkan file
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
            $imagePath = $targetFilePath; // Path ini yang masuk database
        } else {
            die("Gagal mengupload gambar.");
        }
    } else {
        die("Format file tidak didukung (Hanya JPG, JPEG, PNG, GIF, WEBP).");
    }
} else {
    die("Harap pilih gambar unggulan.");
}

// 4. Simpan ke Database
// Gunakan Prepared Statement
$sql = "INSERT INTO articles (title, description, content, image, url, source_name, published_at, category) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

// s = string
// Urutan: title, desc, content, image, url(slug), source, published, category, status
$stmt->bind_param("ssssssss", 
    $title, 
    $desc, 
    $content, 
    $imagePath, 
    $slug, 
    $author_name, 
    $published_at, 
    $category, 
);

if ($stmt->execute()) {
    // Redirect Sukses
    header("Location: ../public/halaman_penulis.php?pesan=uploaded");
} else {
    header("Location: ../public/halaman_penulis.php?pesan=failed");
    echo "Error Database: " . $conn->error;
}
?>