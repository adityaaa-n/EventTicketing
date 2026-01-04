<?php
session_start();
require_once 'config/koneksi.php';

// cek apakah ada ID di URL
if (!isset($_GET['id'])) {
    header("Location: index.php"); // Jika tidak ada ID, kembali ke halaman utama
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

// Tentukan halaman kembali berdasarkan status login
if (isset($_SESSION['user_id'])) {
    $back_link = "dashboard.php";
} else {
    $back_link = "index.php";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($row['nama_event']); ?> - EventTix</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            user-select: none; /* Cegah teks diseleksi */
        }
        body {
            background-color: #f0f2f5;
            font-family: 'Poppins', sans-serif;
            margin: 0;
        }
        .navbar {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 15px 0;
        }
        .nav-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 90%;
            margin: auto;
        }
        .logo {
            color: #1a56db;
            font-size: 24px;
            font-weight: bold;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .container {
            width: 90%;
            margin: 2px auto;
        }
        .detail-container {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .detail-banner img {
            width: 100%;
            height: 350px;
            object-fit: cover;
        }
        .detail-content {
            padding: 30px;
        }
        .detail-title {
            font-size: 26px;
            font-weight: bold;
            color: #1a56db;
            margin-bottom: 20px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 15px;
        }
        .info-item {
            display: flex;
            align-items: center;
            background: #f9fafc;
            border-radius: 8px;
            padding: 10px;
        }
        .info-icon {
            font-size: 20px;
            color: #1a56db;
            margin-right: 10px;
        }
        .info-text label {
            font-size: 13px;
            color: #666;
        }
        .info-text span {
            display: block;
            font-size: 15px;
            color: #333;
            font-weight: 500;
        }
        .desc-section {
            margin-top: 30px;
        }
        .desc-section h3 {
            color: #1a56db;
            margin-bottom: 10px;
        }
        .desc-section p {
            color: #555;
            line-height: 1.6;
            text-align: justify;
        }
        .ticket-box {
            margin-top: 30px;
            background: #f9fafc;
            padding: 20px;
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        .ticket-info h4 {
            margin: 0;
            color: #333;
        }
        .ticket-price {
            color: #1a56db;
            font-size: 20px;
            font-weight: 600;
        }
        .btn-buy {
            background-color: #1a56db;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn-buy:hover {
            background-color: #1545b3;
        }
        .qty-input {
            width: 60px;
            text-align: center;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 6px;
        }
    </style>
</head>
<body oncontextmenu="return false">

    <nav class="navbar">
        <div class="container nav-content">
            <a href="index.php" class="logo">
                <i class="fa-solid fa-ticket"></i> EventTix
            </a>
            <div class="auth-buttons">
                <a href="<?php echo $back_link; ?>" style="text-decoration: none; color: #555;">
                    &larr; Kembali
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="detail-container">
            
            <div class="detail-banner">
                <img src="https://source.unsplash.com/1200x600/?concert,event&sig=<?php echo $row['event_id']; ?>" alt="Banner">
            </div>

            <div class="detail-content">
                <h1 class="detail-title"><?php echo htmlspecialchars($row['nama_event']); ?></h1>

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
                            <span><?php echo htmlspecialchars($row['lokasi']); ?></span>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon"><i class="fa-solid fa-user-tie"></i></div>
                        <div class="info-text">
                            <label>Penyelenggara</label>
                            <span>Sports Indonesia</span>
                        </div>
                    </div>
                </div>

                <div class="desc-section">
                    <h3>Deskripsi Event</h3>
                    <p><?php echo nl2br(htmlspecialchars($row['deskripsi'])); ?></p>
                </div>

                <form action="<?php echo isset($_SESSION['user_id']) ? 'checkout.php' : 'login.php'; ?>" method="GET">
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