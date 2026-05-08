<?php
session_start();
require_once __DIR__ . "/../config/db.php";
$id = $_GET['id'] ?? 0;

// Ambil berita dari database
$stmt = $conn->prepare("SELECT * FROM articles WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("<h2>Berita tidak ditemukan.</h2>");
}
$news = $result->fetch_assoc();

$article_id = $_GET['id']; 

$sql_komen = "SELECT comments.*, users.username 
              FROM comments 
              JOIN users ON comments.user_id = users.id 
              WHERE comments.article_id = ? 
              ORDER BY comments.created_at DESC";

$stmt = $conn->prepare($sql_komen);
$stmt->bind_param("i", $article_id);
$stmt->execute();
$result_komen = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($news['title']) ?> - inIBerita</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        body {
            font-family: system-ui, -apple-system, sans-serif;
            background-color: #f7f7f7;
        }
        .related-news-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }
        .article-body {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #212529;
        }
        .navbar-brand span {
            color: #ffffffff; /* ganti dengan warna yang kamu mau */
        }
</style>
</head>

<body>
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top shadow-sm" style="background-color: #b91c1c;">
        <div class="container">
            <a class="navbar-brand fw-bold fs-3" href="index.php">
                <span>inIBerita</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 fw-medium">
                    <li class="nav-item"><a class="nav-link active text-white" href="index.php">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="index.php?category=business">Bisnis</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="index.php?category=sports">Olahraga</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="index.php?category=health">Kesehatan</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="index.php?category=technology">Teknologi</a></li>
                </ul>
                
                <form class="d-flex ms-lg-3">
                    <div class="input-group">
                        <input class="form-control rounded-start-pill" placeholder="Cari berita...">
                        <button class="btn btn-light rounded-end-pill" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>
                    
                <?php if (isset($_SESSION['username'])): ?>

                    <a href="logout.php" class="btn btn-link text-decoration-none text-white ms-2 fw-bold">Keluar</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-link text-decoration-none text-white ms-2 fw-bold">Masuk</a>     

                <?php endif; ?>
            </div>
        </div>
    </nav>

<div class="container py-5">

    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-danger">Beranda</a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($news['title']) ?></li>
        </ol>
    </nav>

    <div class="row g-5">
        <div class="col-lg-8">
            <article class="bg-white p-4 rounded shadow-sm border mb-5">
                
                <span class="badge bg-danger mb-2"><?= strtoupper($news['category']) ?></span>
                <h1 class="fw-bold mb-3 display-5"><?= htmlspecialchars($news['title']) ?></h1>

                <div class="d-flex align-items-center mb-4 article-meta border-bottom pb-3">
                    <div class="text-secondary border-start ps-3 ms-1">
                        <i class="bi bi-calendar3 me-1"></i> 
                        <?= date("d F Y", strtotime($news['published_at'])) ?><br>
                    </div>
                </div>

                <?php if (!empty($news['image'])): ?>
                    <img src="<?= $news['image'] ?>" class="img-fluid rounded mb-4 w-100">
                <?php endif; ?>

                <div class="article-body mt-4">
                    <?= nl2br($news['content']) ?>
                </div>

            </article>
        </div>

        <!-- SIDEBAR -->
        <div class="col-lg-4">
            <div class="bg-white p-4 rounded shadow-sm border"  >
                <h5 class="fw-bold mb-4 border-start border-4 border-danger ps-3">Berita Terkait</h5>

                <?php
                $stmt2 = $conn->prepare("SELECT id, title, image, category FROM articles WHERE category = ? AND id != ? LIMIT 4");
                $stmt2->bind_param("si", $news['category'], $news['id']);
                $stmt2->execute();
                $related = $stmt2->get_result();

                while ($r = $related->fetch_assoc()):
                ?>
                <div class="d-flex gap-3 related-news-item mb-3">
                    <img src="<?= $r['image'] ?>">
                    <div>
                        <small class="text-danger fw-bold" style="font-size: 0.7rem;">
                            <?= strtoupper($r['category']) ?>
                        </small>
                        <h6 class="mb-0 mt-1 line-clamp-2">
                            <a href="article.php?id=<?= $r['id'] ?>" class="text-decoration-none text-dark fw-bold">
                                <?= htmlspecialchars($r['title']) ?>
                            </a>
                        </h6>
                    </div>
                </div>
                <hr>
                <?php endwhile; ?>
                
            </div>
            <div class="bg-white p-4 rounded shadow-sm border mt-5" id="comment-section" style="max-height: 800px; overflow-y: auto;">
    
                <h5 class="fw-bold mb-4 border-start border-4 border-danger ps-3">Komentar (<?= $result_komen->num_rows ?>)</h5>

                <?php if(isset($_SESSION['username'])): ?>
                    
                    <form action="../private/proses_komentar.php" method="POST">
                        <input type="hidden" name="article_id" value="<?= $article_id ?>">

                        <div class="mb-3">
                            <label class="form-label small text-muted">Komentar sebagai: <strong><?= $_SESSION['username'] ?></strong></label>
                            <textarea name="isi_komentar" class="form-control" rows="4" placeholder="Tulis komentar..." required></textarea>
                        </div>
                        
                        <div class="text-end">
                            <button type="submit" class="btn btn-danger">Kirim Komentar</button>
                        </div>
                    </form>

                <?php else: ?>
                    
                    <div class="alert alert-light border text-center">
                        Silakan <a href="login.php" class="fw-bold text-danger text-decoration-none">Masuk (Login)</a> untuk menulis komentar.
                    </div>

                <?php endif; ?>


                <div id="commentList" class="mt-4 d-flex flex-column gap-3" style="max-height: 200px; overflow-y: auto;">
                    
                    <?php if($result_komen->num_rows > 0): ?>
                        <?php while($komen = $result_komen->fetch_assoc()): ?>
                            
                            <div class="d-flex border-bottom pb-3">
                                <div class="flex-shrink-0 me-3">
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center text-secondary fw-bold" style="width: 40px; height: 40px;">
                                        <?= strtoupper(substr($komen['username'], 0, 1)) ?>
                                    </div>
                                </div>
                                
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-1">
                                        <?= htmlspecialchars($komen['username']) ?>
                                        <small class="text-muted fw-normal ms-2" style="font-size: 0.8rem;">
                                            <?= date('d M Y, H:i', strtotime($komen['created_at'])) ?>
                                        </small>
                                    </h6>
                                    <p class="mb-0 text-secondary" style="font-size: 0.95rem;">
                                        <?= nl2br(htmlspecialchars($komen['comment_text'])) ?>
                                    </p>
                                </div>
                            </div>

                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-center text-muted small py-3">Belum ada komentar. Jadilah yang pertama berkomentar!</p>
                    <?php endif; ?>

                </div>
            </div>
        </div>
        
    </div>
</div>

<!-- FOOTER -->
    <footer class="bg-dark text-white pt-5 pb-3 mt-5">
        <div class="container">

            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <h4 class="fw-bold mb-3">BeritaNusantara</h4>
                    <p class="text-secondary small">Menyajikan berita terkini dan akurat dari seluruh negeri.</p>
                </div>

                <div class="col-lg-2 col-md-6">
                    <h5 class="fw-bold mb-3">Kategori</h5>
                    <ul class="list-unstyled text-secondary small">
                        <li><a href="index.php?category=nation" class="text-decoration-none text-secondary">Nasional</a></li>
                        <li><a href="#" class="text-decoration-none text-secondary">Ekonomi</a></li>
                        <li><a href="#" class="text-decoration-none text-secondary">Teknologi</a></li>
                    </ul>
                </div>

            </div>

            <hr class="border-secondary mt-4">

            <div class="text-center text-secondary small">
                © 2024 BeritaNusantara
            </div>

        </div>
    </footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function postComment(event) {
    event.preventDefault(); // mencegah reload halaman

    const name = document.getElementById('commentName').value.trim();
    const text = document.getElementById('commentText').value.trim();

    if(!name || !text) return;

    const list = document.getElementById('commentList');
    const comment = document.createElement('div');
    comment.className = 'd-flex gap-3';
    comment.innerHTML = `
        <div class="flex-shrink-0 rounded-circle bg-light d-flex align-items-center justify-content-center" 
             style="width:50px; height:50px; font-size:1.2rem; color:#6c757d;">
            <i class="bi bi-person-fill"></i>
        </div>
        <div>
            <h6 class="fw-bold mb-1">${name}</h6>
            <p class="mb-0 text-secondary">${text}</p>
        </div>
    `;
    list.prepend(comment);

    document.getElementById('commentForm').reset();
}
</script>

</body>
</html>

