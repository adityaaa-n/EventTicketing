<?php
require_once 'config/koneksi.php';

// cek kalo ada ID di URL
if (!isset($_GET['id'])) {
    header("Location: index.php"); // Jika tidak ada ID, kembalikan ke home
    exit();
}

$event_id = $_GET['id'];

// ambil detail event berdasarkan ID
$query = "SELECT * FROM events WHERE event_id = '$event_id'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

if (!$row) {
    echo "Event tidak ditemukan.";
    exit();
}

// Format data untuk tampilan
$tanggal = date('d F Y', strtotime($row['tanggal']));
$waktu = date('H:i', strtotime($row['waktu']));
$harga = "Rp " . number_format($row['harga'], 0, ',', '.');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $row['nama_event']; ?> - EventTix</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <nav class="navbar">
        <div class="container nav-content">
            <a href="index.php" class="logo">
                <i class="fa-solid fa-ticket"></i> EventTix
            </a>
            <div class="auth-buttons">
                <a href="index.php" style="text-decoration: none; color: #555;">&larr; Kembali</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="detail-container">
            
            <div class="detail-banner">
                <img src="https://source.unsplash.com/1200x600/?concert,event&sig=<?php echo $row['event_id']; ?>" alt="Banner">
            </div>

            <div class="detail-content">
                <h1 class="detail-title"><?php echo $row['nama_event']; ?></h1>

                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-icon"><i class="fa-regular fa-calendar"></i></div>
                        <div class="info-text">
                            <label>Tanggal</label>
                            <span><?php echo $tanggal; ?></span>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon"><i class="fa-regular fa-clock"></i></div>
                        <div class="info-text">
                            <label>Waktu</label>
                            <span><?php echo $waktu; ?> WIB</span>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon"><i class="fa-solid fa-location-dot"></i></div>
                        <div class="info-text">
                            <label>Lokasi</label>
                            <span><?php echo $row['lokasi']; ?></span>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon"><i class="fa-solid fa-user-tie"></i></div>
                        <div class="info-text">
                            <label>Penyelenggara</label>
                            <span>Sports Indonesia</span> </div>
                    </div>
                </div>

                <div class="desc-section">
                    <h3>Deskripsi Event</h3>
                    <p><?php echo nl2br($row['deskripsi']); ?></p>
                </div>

                <form action="checkout.php" method="GET">
                    <input type="hidden" name="event_id" value="<?php echo $row['event_id']; ?>">
                    
                    <div class="ticket-box">
                        <div class="ticket-info">
                            <h4>Tiket Reguler</h4>
                            <div class="ticket-price"><?php echo $harga; ?></div>
                            <small style="color: #888;">Sisa Kuota: <?php echo $row['kuota']; ?></small>
                        </div>
                        
                        <div class="ticket-action">
                            <label>Jumlah:</label>
                            <input type="number" name="jumlah" class="qty-input" value="1" min="1" max="<?php echo $row['kuota']; ?>">
                            <button type="submit" class="btn-buy">
                                Beli Tiket
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

</body>
</html>