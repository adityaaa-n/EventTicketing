<?php
session_start();
require_once 'config/koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}


$kategori_pilihan = isset($_GET['kategori']) ? $_GET['kategori'] : 'Semua';
$kata_kunci     = isset($_GET['cari']) ? $_GET['cari'] : '';

$sql = "SELECT * FROM events WHERE 1=1"; 

if ($kategori_pilihan != 'Semua') {
    $kat = mysqli_real_escape_string($conn, $kategori_pilihan);
    $sql .= " AND kategori = '$kat'";
}

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - EventTix</title>
    <link rel="stylesheet" href="assets/css/style.css?v=3">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .card-image img {
            width: 100%;
            height: 200px; 
            object-fit: cover; 
            object-position: center;
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="container nav-content">
            <a href="dashboard.php" class="logo">
                <i class="fa-solid fa-ticket"></i> EventTix
            </a>
            
            <div class="auth-buttons" style="display: flex; align-items: center;">
                <a href="tiket_saya.php" style="margin-right: 20px; text-decoration: none; color: #1a56db; font-weight: 600;">
                    <i class="fa-solid fa-receipt"></i> Tiket Saya
                </a>
                <span style="margin-right: 15px; color:#333; border-left: 1px solid #ccc; padding-left: 15px;">
                    <i class="fa-solid fa-user"></i> 
                    <?php echo htmlspecialchars($_SESSION['nama']); ?>
                </span>
                <a href="logout.php" style="text-decoration:none; background:#e3342f; color:white; padding:8px 15px; border-radius:6px; font-weight:600; font-size: 14px;">
                   Keluar
                </a>
            </div>
        </div>
    </nav>

    <header class="hero">
        <div class="container">
            <h1>Hai, <?php echo htmlspecialchars($_SESSION['nama']); ?></h1>
            <p>Temukan dan beli tiket event favoritmu hanya di <strong>EventTix</strong>!</p>
            
            <form action="dashboard.php" method="GET" class="search-box">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                
                <input type="text" name="cari" placeholder="Cari event..." value="<?php echo htmlspecialchars($kata_kunci); ?>" autocomplete="off">
                
                <?php if($kategori_pilihan != 'Semua'): ?>
                    <input type="hidden" name="kategori" value="<?php echo htmlspecialchars($kategori_pilihan); ?>">
                <?php endif; ?>
                
                <button type="submit" style="display:none;"></button>
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
                <a href="dashboard.php?kategori=Semua<?= $link_cari ?>" class="pill <?php echo ($kategori_pilihan == 'Semua') ? 'active' : ''; ?>">Semua</a>
                <a href="dashboard.php?kategori=Musik<?= $link_cari ?>" class="pill <?php echo ($kategori_pilihan == 'Musik') ? 'active' : ''; ?>">Musik</a>
                <a href="dashboard.php?kategori=Olahraga<?= $link_cari ?>" class="pill <?php echo ($kategori_pilihan == 'Olahraga') ? 'active' : ''; ?>">Olahraga</a>
                <a href="dashboard.php?kategori=Konferensi<?= $link_cari ?>" class="pill <?php echo ($kategori_pilihan == 'Konferensi') ? 'active' : ''; ?>">Konferensi</a>
                <a href="dashboard.php?kategori=Lainnya<?= $link_cari ?>" class="pill <?php echo ($kategori_pilihan == 'Lainnya') ? 'active' : ''; ?>">Lainnya</a>
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
                        echo ($kategori_pilihan == 'Semua') ? 'Semua Event' : 'Kategori: ' . htmlspecialchars($kategori_pilihan);
                    }
                ?>
            </div>
            
            <div class="event-grid">
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $tanggal = date('d F Y', strtotime($row['tanggal']));
                        $jam = date('H:i', strtotime($row['waktu']));
                        $harga = "Rp " . number_format($row['harga'], 0, ',', '.');
                        
                        $gambar_db = $row['gambar'];
                        $path_gambar = "assets/images/" . $gambar_db;
                        
                        if (!empty($gambar_db) && file_exists($path_gambar)) {
                            $img_src = $path_gambar;
                        } else {
                            $img_src = "https://via.placeholder.com/600x400?text=No+Image";
                        }
                ?>
                
                <div class="event-card">
                    <div class="card-image">
                        <img src="<?php echo $img_src; ?>" alt="<?php echo htmlspecialchars($row['nama_event']); ?>">
                        
                        <span class="category-badge">
                            <?php echo !empty($row['kategori']) ? htmlspecialchars($row['kategori']) : 'Event'; ?>
                        </span>
                    </div>
                    <div class="card-content">
                        <div class="event-title"><?php echo htmlspecialchars($row['nama_event']); ?></div>
                        <p class="event-desc">
                            <?php echo substr(htmlspecialchars($row['deskripsi']), 0, 100) . '...'; ?>
                        </p>
                        
                        <div class="event-meta">
                            <i class="fa-regular fa-calendar"></i> <?php echo $tanggal; ?>
                        </div>
                        <div class="event-meta">
                            <i class="fa-regular fa-clock"></i> <?php echo $jam; ?> WIB
                        </div>
                        <div class="event-meta">
                            <i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($row['lokasi']); ?>
                        </div>

                        <div class="price-tag"><?php echo $harga; ?></div>
                        
                        <a href="detail.php?id=<?php echo $row['event_id']; ?>" 
                           style="display:block; margin-top:15px; text-align:center; background:#1a56db; color:white; padding:10px; border-radius:8px; text-decoration:none; font-weight: 600;">
                           Lihat Detail
                        </a>
                    </div>
                </div>

                <?php 
                    }
                } else {
                    echo "<div style='grid-column: 1/-1; text-align: center; padding: 50px; background:white; border-radius:10px; box-shadow:0 2px 10px rgba(0,0,0,0.05);'>
                            <i class='fa-solid fa-magnifying-glass' style='font-size: 40px; color: #ddd; margin-bottom: 15px;'></i>
                            <p style='color:#666; font-size:16px;'>Tidak ada event yang ditemukan.</p>
                            <a href='dashboard.php' style='display:inline-block; margin-top:10px; color:#1a56db; text-decoration:none; font-weight:bold;'>Reset Pencarian</a>
                          </div>";
                }
                ?>
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

// Update tampilan dashboard user oleh Aditya