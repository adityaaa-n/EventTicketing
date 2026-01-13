<?php
session_start();
require_once 'config/koneksi.php';

$kategori_pilihan = isset($_GET['kategori']) ? $_GET['kategori'] : 'Semua';
$kata_kunci       = isset($_GET['cari']) ? $_GET['cari'] : '';

$sql = "SELECT * FROM events WHERE 1=1"; 

// Filter Kategori
if ($kategori_pilihan != 'Semua') {
    $kat = mysqli_real_escape_string($conn, $kategori_pilihan);
    $sql .= " AND kategori = '$kat'";
}

// Filter Pencarian
if (!empty($kata_kunci)) {
    $cari = mysqli_real_escape_string($conn, $kata_kunci);
    $sql .= " AND (nama_event LIKE '%$cari%' OR lokasi LIKE '%$cari%')";
}

$sql .= " ORDER BY tanggal ASC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>EventTix - Temukan Event Favoritmu</title>
    <link rel="stylesheet" href="assets/css/style.css?v=4"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .card-image img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="container nav-content">
            <a href="index.php" class="logo">
                <i class="fa-solid fa-ticket"></i> EventTix
            </a>
            <div class="auth-buttons">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="dashboard.php" style="text-decoration: none; color: #333; font-weight: 600;">Dashboard Saya</a>
                <?php else: ?>
                    <a href="login.php" style="text-decoration: none; color: #333; font-weight: 600;">Masuk</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <header class="hero">
        <div class="container">
            <h1>Temukan Event Favoritmu</h1>
            <p>Dapatkan tiket untuk konser, pertandingan olahraga, konferensi, dan berbagai event menarik lainnya</p>
            
            <form action="index.php" method="GET" class="search-box">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" name="cari" placeholder="Cari event atau lokasi..." value="<?php echo htmlspecialchars($kata_kunci); ?>" autocomplete="off">
                
                <?php if($kategori_pilihan != 'Semua'): ?>
                    <input type="hidden" name="kategori" value="<?php echo htmlspecialchars($kategori_pilihan); ?>">
                <?php endif; ?>
            </form>
        </div>
    </header>

    <section class="category-section">
        <div class="container">
            <div class="section-title"><i class="fa-solid fa-filter"></i> Kategori</div>
            <div class="category-pills">
                <?php 
                    $link_cari = !empty($kata_kunci) ? '&cari=' . urlencode($kata_kunci) : ''; 
                ?>
                <a href="index.php?kategori=Semua<?= $link_cari ?>" class="pill <?php echo ($kategori_pilihan == 'Semua') ? 'active' : ''; ?>">Semua</a>
                <a href="index.php?kategori=Musik<?= $link_cari ?>" class="pill <?php echo ($kategori_pilihan == 'Musik') ? 'active' : ''; ?>">Musik</a>
                <a href="index.php?kategori=Olahraga<?= $link_cari ?>" class="pill <?php echo ($kategori_pilihan == 'Olahraga') ? 'active' : ''; ?>">Olahraga</a>
                <a href="index.php?kategori=Konferensi<?= $link_cari ?>" class="pill <?php echo ($kategori_pilihan == 'Konferensi') ? 'active' : ''; ?>">Konferensi</a>
                <a href="index.php?kategori=Lainnya<?= $link_cari ?>" class="pill <?php echo ($kategori_pilihan == 'Lainnya') ? 'active' : ''; ?>">Lainnya</a>
            </div>
        </div>
    </section>

    <section class="event-list">
        <div class="container">
            <div class="section-title">
                <?php 
                    if (!empty($kata_kunci)) {
                        echo "Hasil pencarian: \"<strong>" . htmlspecialchars($kata_kunci) . "</strong>\"";
                    } else {
                        echo ($kategori_pilihan == 'Semua') ? 'Event Terbaru' : 'Kategori: ' . htmlspecialchars($kategori_pilihan);
                    }
                ?>
            </div>
            
            <div class="event-grid">
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        $tanggal = date('d F Y', strtotime($row['tanggal']));
                        $jam = date('H:i', strtotime($row['waktu']));
                        $harga = "Rp " . number_format($row['harga'], 0, ',', '.');
                        
                        $gambar_db = $row['gambar'];
                        $path_gambar = "assets/images/" . $gambar_db;
                        if (!empty($gambar_db) && file_exists($path_gambar)) {
                            $img_src = $path_gambar;
                        } else {
                            $img_src = "https://via.placeholder.com/600x400?text=Event";
                        }
                ?>
                
                <div class="event-card">
                    <div class="card-image">
                        <img src="<?php echo $img_src; ?>" alt="Event">
                        <span class="category-badge"><?php echo $row['kategori']; ?></span>
                    </div>
                    <div class="card-content">
                        <div class="event-title"><?php echo $row['nama_event']; ?></div>
                        <p class="event-desc"><?php echo substr($row['deskripsi'], 0, 100) . '...'; ?></p>
                        
                        <div class="event-meta">
                            <i class="fa-regular fa-calendar"></i> <?php echo $tanggal; ?>
                        </div>
                        <div class="event-meta">
                            <i class="fa-regular fa-clock"></i> <?php echo $jam; ?> WIB
                        </div>
                        <div class="event-meta">
                            <i class="fa-solid fa-location-dot"></i> <?php echo $row['lokasi']; ?>
                        </div>

                        <div class="price-tag"><?php echo $harga; ?></div>
                        
                        <a href="detail.php?id=<?php echo $row['event_id']; ?>" 
                           style="display:block; margin-top:15px; text-align:center; background:#1a56db; color:white; padding:10px; border-radius:8px; text-decoration:none; font-weight: 600;">
                           Lihat Detail
                        </a>
                    </div>

                <?php 
                    }
                } else {
                    // Tampilan jika data kosong (Keren seperti dashboard)
                    echo "<div style='grid-column: 1/-1; text-align: center; padding: 50px; background:white; border-radius:10px;'>
                            <i class='fa-solid fa-magnifying-glass' style='font-size: 40px; color: #ddd; margin-bottom: 15px;'></i>
                            <p style='color:#666;'>Tidak ada event yang ditemukan.</p>
                            <a href='index.php' style='color:#1a56db; font-weight:bold;'>Reset</a>
                          </div>";
                }
                ?>
            </div>
        </div>
    </div>
</section>

    <a href="https://wa.me/6281324351763?text=Halo%20Admin..." 
       class="wa-float" 
       target="_blank" 
       title="Hubungi CS">
        <i class="fa-brands fa-whatsapp"></i>
    </a>

</body>
</html>
