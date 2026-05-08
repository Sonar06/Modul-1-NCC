<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editor Berita - BeritaNusantara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        /* Agar Footer selalu di bawah (Sticky Footer) */
        body { 
            background-color: #f3f4f6; 
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        main {
            flex: 1; /* Mengisi ruang kosong agar footer terdorong ke bawah */
        }

        .editor-container { background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        
        /* Area Editor */
        #editor-area { min-height: 400px; resize: vertical; }
        
        /* Image Preview */
        .img-preview {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border: 2px dashed #ddd;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c6b6b;
            background: #fafafa;
            overflow: hidden;
        }
        .img-preview img { width: 100%; height: 100%; object-fit: cover; }

        /* Style Khusus Komentar Admin */
        .comment-item { border-left: 4px solid #ddd; transition: 0.2s; }
        .comment-item:hover { background-color: #f9fafb; }
        .comment-approved { border-left-color: #198754; } /* Hijau */
        .comment-pending { border-left-color: #ffc107; } /* Kuning */
        .avatar-small { width: 40px; height: 40px; border-radius: 50%; background: #e9ecef; display: flex; align-items: center; justify-content: center; }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark mb-4 sticky-top shadow-sm" style="background-color: #b91c1c;">
        <div class="container-fluid px-4">
            <span class="navbar-brand mb-0 h1">Editor <span class="text-white">inIBerita</span></span>
            <div class="d-flex align-items-center gap-3">
               <a href="index.php" class="btn btn-sm btn-light text-danger fw-bold d-none d-md-block">
                    <i class="bi bi-list me-1"></i> Beranda
                </a>
                <span class="text-white small border-start ps-3 ms-2">Halo, Editor</span>
                <div class="dropdown">
                    
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="profile.html">Profil</a></li>
                        <li><a class="dropdown-item" href="setting.html">Pengaturan</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="login.html">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <?php if (isset($_GET['pesan'])): ?>

        <?php if ($_GET['pesan'] == 'uploaded'): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <h4 class="alert-heading"><i class="bi bi-check-circle-fill me-2"></i>Berhasil!</h4>
                <p>Berita baru Anda telah berhasil disimpan dan diterbitkan ke database.</p>
                <hr>
                <p class="mb-0 small">
                    Silakan cek di <a href="index.php" class="alert-link">Halaman Depan</a> untuk melihat hasilnya.
                </p>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>

        <?php elseif ($_GET['pesan'] == 'failed'): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-octagon-fill me-2"></i>
                <strong>Gagal!</strong> Terjadi kesalahan saat menyimpan berita. Coba cek koneksi atau input Anda.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

    <?php endif; ?>
    <main class="container pb-5">

        <form action="../private/proses_tambahberita.php" method="POST" enctype="multipart/form-data">
            <div class="row g-4">
                
                <div class="col-lg-8">
                    <div class="editor-container p-4 mb-4">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Judul Berita</label>
                            <input type="text" name="title" class="form-control form-control-lg" placeholder="Judul..." required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-muted small">Permalink (Slug):</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light">beritanusantara.com/berita/</span>
                                <input type="text" name="slug" class="form-control bg-light text-secondary" placeholder="judul-berita-anda" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Deskripsi Singkat</label>
                            <textarea name="description" class="form-control" rows="3" maxlength="150" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Isi Berita</label>
                            <textarea name="content" class="form-control" id="editor-area" rows="10" required></textarea>
                        </div>
                    </div>
                    
                    </div>

                <div class="col-lg-4">
                    <div class="editor-container p-4 mb-4">
                        <h6 class="fw-bold border-bottom pb-2 mb-3">Penerbitan</h6>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Tanggal</label>
                            <input type="datetime-local" name="published_at" class="form-control" required>
                        </div>
 
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-danger fw-bold"><i class="bi bi-save"></i> Simpan Berita</button>
                        </div>
                    </div>

                    <div class="editor-container p-4 mb-4">
                        <h6 class="fw-bold border-bottom pb-2 mb-3">Kategori</h6>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="kategori" value="general" checked>
                            <label class="form-check-label">General</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="kategori" value="business">
                            <label class="form-check-label">Bisnis</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="kategori" value="sports">
                            <label class="form-check-label">Olahraga</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="kategori" value="health">
                            <label class="form-check-label">Kesehatan</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="kategori" value="technology">
                            <label class="form-check-label">Teknologi</label>
                        </div>
                    </div>

                    <div class="editor-container p-4">
                        <h6 class="fw-bold border-bottom pb-2 mb-3">Gambar Unggulan</h6>
                        <input class="form-control form-control-sm" type="file" name="image" accept="image/*" required>
                    </div>
                </div>
            </div>
        </form>
    </main>

    <footer class="bg-dark text-secondary py-4 mt-5 border-top">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0 fw-bold text-white">Editor BeritaNusantara</p>
                    <small class="text-white" style="font-size: 0.8rem;">&copy; 2024 CMS Panel. All Rights Reserved.</small>
                </div>
                <div class="col-md-6 text-center text-md-end mt-3 mt-md-0">
                    <ul class="list-inline mb-0 small">
                        <li class="list-inline-item text-white"><a href="#" class="text-decoration-none text-secondary hover-danger">Bantuan</a></li>
                        <li class="list-inline-item border-start ps-2 text-white"><a href="#" class="text-decoration-none text-secondary hover-danger">Laporkan Bug</a></li>
                        <li class="list-inline-item border-start ps-2 text-muted text-white">Versi 1.0.2</li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function generateSlug() {
            let title = document.getElementById('titleInput').value;
            let slug = title.toLowerCase().replace(/[^a-z0-9 -]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-');
            document.getElementById('slugInput').value = slug;
        }
        function previewImage() {
            const imageInput = document.getElementById('imageInput');
            const imagePreview = document.getElementById('imagePreview');
            const file = imageInput.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                    imagePreview.style.border = "none";
                }
                reader.readAsDataURL(file);
            }
        }
    </script>
</body>
</html>