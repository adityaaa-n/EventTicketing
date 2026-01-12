<?php
session_start();
require_once 'config/koneksi.php';

// ===============================
// LOGIKA FILTER & SEARCH
// ===============================
$kategori_pilihan = isset($_GET['kategori']) ? $_GET['kategori'] : 'Semua';
$kata_kunci       = isset($_GET['cari']) ? $_GET['cari'] : '';

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
    <title>EventTix - Temukan Event Favoritmu</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

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

<!-- ===============================
 NAVBAR (PUBLIK)
================================ -->
<nav class="navbar">
    <div class="container nav-content">
        <a href="index.php" class="logo">
            <i class="fa-solid fa-ticket"></i> EventTix
        </a>

        <div class="auth-buttons">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="dashboard.php" style="font-weight:600;">Dashboard</a>
            <?php else: ?>
                <a href="login.php" style="text-decoration: none; color:#444; font-weight:600;">Masuk</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- ===============================
 HERO + SEARCH
================================ -->
<header class="hero">
    <div class="container">
        <h1>Temukan Event Favoritmu</h1>
        <p>Beli tiket konser, olahraga, dan event menarik lainnya</p>

        <form action="index.php" method="GET" class="search-box">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <input type="text"
                   name="cari"
                   placeholder="Cari event atau lokasi..."
                   value="<?= htmlspecialchars($kata_kunci); ?>"
                   autocomplete="off">

            <?php if ($kategori_pilihan != 'Semua'): ?>
                <input type="hidden" name="kategori" value="<?= htmlspecialchars($kategori_pilihan); ?>">
            <?php endif; ?>
        </form>
    </div>
</header>

<!-- ===============================
 KATEGORI
================================ -->
<section class="category-section">
    <div class="container">
        <div class="section-title">
            <i class="fa-solid fa-filter"></i> Kategori
        </div>

        <?php
        $link_cari = !empty($kata_kunci) ? '&cari=' . urlencode($kata_kunci) : '';
        ?>

        <div class="category-pills">
            <?php
            $kategori_list = ['Semua', 'Musik', 'Olahraga', 'Konferensi', 'Lainnya'];
            foreach ($kategori_list as $kat) {
                $active = ($kategori_pilihan == $kat) ? 'active' : '';
                echo "<a href='index.php?kategori=$kat$link_cari' class='pill $active'>$kat</a>";
            }
            ?>
        </div>
    </div>
</section>

<!-- ===============================
 DAFTAR EVENT
================================ -->
<section class="event-list">
    <div class="container">
        <div class="section-title">
            <?php
            if (!empty($kata_kunci)) {
                echo "Hasil pencarian: <strong>" . htmlspecialchars($kata_kunci) . "</strong>";
            } else {
                echo ($kategori_pilihan == 'Semua')
                    ? 'Event Terbaru'
                    : 'Kategori: ' . htmlspecialchars($kategori_pilihan);
            }
            ?>
        </div>

        <div class="event-grid">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <?php
                    $tanggal = date('d F Y', strtotime($row['tanggal']));
                    $jam     = date('H:i', strtotime($row['waktu']));
                    $harga   = "Rp " . number_format($row['harga'], 0, ',', '.');

                    $gambar = (!empty($row['gambar']) && file_exists("assets/images/" . $row['gambar']))
                        ? "assets/images/" . $row['gambar']
                        : "https://via.placeholder.com/600x400?text=Event";
                    ?>

                    <div class="event-card">
                        <div class="card-image">
                            <img src="<?= $gambar ?>" alt="<?= htmlspecialchars($row['nama_event']); ?>">
                            <span class="category-badge"><?= htmlspecialchars($row['kategori']); ?></span>
                        </div>

                        <div class="card-content">
                            <div class="event-title"><?= htmlspecialchars($row['nama_event']); ?></div>
                            <p class="event-desc"><?= substr(htmlspecialchars($row['deskripsi']), 0, 100); ?>...</p>

                            <div class="event-meta"><i class="fa-regular fa-calendar"></i> <?= $tanggal; ?></div>
                            <div class="event-meta"><i class="fa-regular fa-clock"></i> <?= $jam; ?> WIB</div>
                            <div class="event-meta"><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($row['lokasi']); ?></div>

                            <div class="price-tag"><?= $harga; ?></div>

                            <a href="detail.php?id=<?= $row['event_id']; ?>"
                               style="display:block; margin-top:15px; text-align:center; background:#1a56db; color:white; padding:10px; border-radius:8px; text-decoration:none; font-weight:600;">
                                Lihat Detail
                            </a>
                        </div>
                    </div>

                <?php endwhile; ?>
            <?php else: ?>
                <div style="grid-column:1/-1; text-align:center; padding:50px; background:white; border-radius:10px;">
                    <i class="fa-solid fa-magnifying-glass" style="font-size:40px; color:#ddd;"></i>
                    <p style="color:#666;">Tidak ada event yang ditemukan.</p>
                    <a href="index.php" style="color:#1a56db; font-weight:bold;">Reset Pencarian</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ===============================
 WHATSAPP
================================ -->
<a href="https://wa.me/6281324351763?text=Halo%20Admin..."
   class="wa-float"
   target="_blank">
    <i class="fa-brands fa-whatsapp"></i>
</a>

</body>
</html>
