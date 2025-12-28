<?php
session_start();
require_once 'config/koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$query = "SELECT * FROM events ORDER BY tanggal ASC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - EventTix</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="container nav-content">
            <a href="dashboard.php" class="logo">
                <i class="fa-solid fa-ticket"></i> EventTix
            </a>
            <div class="auth-buttons">
                <span style="margin-right: 15px; color:#333;">
                    <i class="fa-solid fa-user"></i> 
                    <?php echo htmlspecialchars($_SESSION['nama']); ?>
                </span>
                <a href="logout.php" 
                   style="text-decoration:none; background:#1a56db; color:white; padding:8px 15px; border-radius:6px; font-weight:600;">
                   Keluar
                </a>
            </div>
        </div>
    </nav>

    <!-- Header -->
    <header class="hero">
        <div class="container">
            <h1>Hai, <?php echo htmlspecialchars($_SESSION['nama']); ?></h1>
            <p>Temukan dan beli tiket event favoritmu hanya di <strong>EventTix</strong>!</p>
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" placeholder="Cari event atau lokasi...">
            </div>
        </div>
    </header>

    <!-- Kategori -->
    <section class="category-section">
        <div class="container">
            <div class="section-title"><i class="fa-solid fa-filter"></i> Kategori</div>
            <div class="category-pills">
                <a href="#" class="pill active">Semua</a>
                <a href="#" class="pill">Musik</a>
                <a href="#" class="pill">Olahraga</a>
                <a href="#" class="pill">Konferensi</a>
                <a href="#" class="pill">Lainnya</a>
            </div>
        </div>
    </section>

    <!-- Daftar Event -->
    <section class="event-list">
        <div class="container">
            <div class="section-title">Event Tersedia</div>
            
            <div class="event-grid">
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $tanggal = date('d F Y', strtotime($row['tanggal']));
                        $jam = date('H:i', strtotime($row['waktu']));
                        $harga = "Rp " . number_format($row['harga'], 0, ',', '.');
                ?>
                
                <div class="event-card">
                    <div class="card-image">
                        <img src="https://source.unsplash.com/600x400/?event,concert&sig=<?php echo $row['event_id']; ?>" alt="Event">
                        <span class="category-badge">Event</span>
                    </div>
                    <div class="card-content">
                        <div class="event-title"><?php echo htmlspecialchars($row['nama_event']); ?></div>
                        <p class="event-desc"><?php echo htmlspecialchars($row['deskripsi']); ?></p>
                        
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
                           style="display:block; margin-top:10px; text-align:center; background:#1a56db; color:white; padding:10px; border-radius:8px; text-decoration:none;">
                           Lihat Detail
                        </a>
                    </div>
                </div>

                <?php 
                    }
                } else {
                    echo "<p>Belum ada event yang tersedia.</p>";
                }
                ?>
            </div>
        </div>
    </section>

</body>
</html>
