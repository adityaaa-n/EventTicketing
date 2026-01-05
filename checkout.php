<?php
session_start();
require_once 'config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['event_id']) || !isset($_GET['jumlah'])) {
    header("Location: index.php");
    exit;
}

$event_id = $_GET['event_id'];
$jumlah = (int)$_GET['jumlah'];

$stmt = $conn->prepare("SELECT * FROM events WHERE event_id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();

if (!$event) { echo "Event tidak valid"; exit; }

$gambar_db = $event['gambar'];
$path_gambar = "assets/images/" . $gambar_db;

if (!empty($gambar_db) && file_exists($path_gambar)) {
    $img_src = $path_gambar;
} else {
    $img_src = "https://via.placeholder.com/150x150?text=No+Image";
}

$total_harga = $event['harga'] * $jumlah;
$biaya_admin = 5000; 
$grand_total = $total_harga + $biaya_admin;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Checkout - EventTix</title>
    <link rel="stylesheet" href="assets/css/style.css?v=2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f0f2f5; }
        .checkout-container {
            max-width: 1000px;
            margin: 40px auto;
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 25px;
            padding: 0 20px;
        }
        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
        }
        .section-header { 
            font-size: 18px; 
            font-weight: bold; 
            margin-bottom: 20px; 
            border-bottom: 1px solid #eee; 
            padding-bottom: 10px; 
        }
        .info-row { 
            display: flex; 
            justify-content: space-between; 
            margin-bottom: 10px; 
            color: #555; 
            font-size: 14px; 
        }
        .total-row { 
            display: flex; 
            justify-content: space-between; 
            margin-top: 20px; 
            padding-top: 15px; 
            border-top: 2px dashed #eee; 
            font-weight: bold; 
            font-size: 18px; 
            color: #1a56db; 
        }
        .event-summary { display: flex; gap: 15px; margin-bottom: 20px; }
        .event-summary img { width: 80px; height: 80px; border-radius: 8px; object-fit: cover; }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="container nav-content">
        <a href="dashboard.php" class="logo"><i class="fa-solid fa-ticket"></i> EventTix</a>
    </div>
</nav>

<div class="checkout-container">
    <form action="proses_beli.php" method="POST" style="display: contents;">
        <input type="hidden" name="event_id" value="<?= $event_id ?>">
        <input type="hidden" name="jumlah" value="<?= $jumlah ?>">
        <input type="hidden" name="total_bayar" value="<?= $grand_total ?>">

        <div class="card">
            <div class="section-header">Review Pesanan</div>
            
            <div class="event-summary">
                <img src="<?= $img_src ?>" alt="<?= htmlspecialchars($event['nama_event']) ?>">
                
                <div>
                    <h3 style="font-size: 18px; margin-bottom: 5px;"><?= htmlspecialchars($event['nama_event']) ?></h3>
                    <p style="color: #666; font-size: 13px;">
                        <i class="fa-regular fa-calendar"></i> <?= date('d F Y', strtotime($event['tanggal'])) ?>
                        &nbsp; <i class="fa-regular fa-clock"></i> <?= date('H:i', strtotime($event['waktu'])) ?>
                    </p>
                    <p style="color: #666; font-size: 13px;"><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($event['lokasi']) ?></p>
                </div>
            </div>

            <div class="section-header" style="margin-top: 30px;">Data Pemesan</div>
            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                <p><strong>Nama:</strong> <?= $_SESSION['nama'] ?></p>
                <p><strong>Email:</strong> <?= $_SESSION['email'] ?></p>
            </div>
        </div>

        <div class="card">
            <div class="section-header">Rincian Pembayaran</div>
            
            <div class="info-row">
                <span>Harga Tiket (x<?= $jumlah ?>)</span>
                <span>Rp <?= number_format($total_harga, 0, ',', '.') ?></span>
            </div>
            <div class="info-row">
                <span>Biaya Layanan</span>
                <span>Rp <?= number_format($biaya_admin, 0, ',', '.') ?></span>
            </div>
            
            <div class="total-row">
                <span>Total Bayar</span>
                <span>Rp <?= number_format($grand_total, 0, ',', '.') ?></span>
            </div>

            <button type="submit" class="btn-buy" style="width: 100%; margin-top: 25px; padding: 15px; font-size: 16px; background-color: #1a56db; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;">
                Lanjut Pembayaran <i class="fa-solid fa-arrow-right"></i>
            </button>
        </div>
    </form>
</div>

<script src="assets/js/script.js"></script>

<a href="https://wa.me/6281324351763?text=Halo%20Admin..." 
   class="wa-float" 
   target="_blank" 
   title="Hubungi CS">
    <i class="fa-brands fa-whatsapp"></i>
</a>
</body>
</html>