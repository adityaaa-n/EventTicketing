<?php
session_start();
require_once 'config/koneksi.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
$user_id = $_SESSION['user_id'];

date_default_timezone_set('Asia/Jakarta');

// Ambil tiket + info event + GAMBAR 
$query = "SELECT t.*, e.nama_event, e.tanggal, e.waktu, e.lokasi, e.gambar 
          FROM tickets t 
          JOIN events e ON t.event_id = e.event_id 
          WHERE t.user_id = $user_id 
          ORDER BY t.tanggal_beli DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tiket Saya - EventTix</title>
    <link rel="stylesheet" href="assets/css/style.css?v=2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Badge Status */
        .status-badge { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; text-transform: uppercase; }
        .status-pending { background: #fff3cd; color: #856404; }   
        .status-paid { background: #d1ecf1; color: #0c5460; }      
        .status-confirmed { background: #d4edda; color: #155724; } 
        .status-rejected { background: #f8d7da; color: #721c24; }  
        
        /* WARNA BARU UNTUK EVENT SELESAI */
        .status-finished { background: #6c757d; color: white; }    
        
        .ticket-card { display: flex; background: white; border-radius: 12px; overflow: hidden; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .ticket-left { width: 120px; background: #eee; }
        .ticket-left img { width: 100%; height: 100%; object-fit: cover; }
        .ticket-right { padding: 20px; flex-grow: 1; display: flex; justify-content: space-between; align-items: center; }
        
        @media (max-width: 600px) {
            .ticket-card { flex-direction: column; }
            .ticket-left { width: 100%; height: 100px; }
            .ticket-right { flex-direction: column; align-items: flex-start; width: 100%; gap: 15px; }
        }
    </style>
</head>
<body style="background-color: #f0f2f5;">

    <nav class="navbar">
        <div class="container nav-content">
            <a href="dashboard.php" class="logo"><i class="fa-solid fa-ticket"></i> EventTix</a>
            <div class="auth-buttons">
                <a href="dashboard.php" style="text-decoration:none; color:#333;">Beranda</a> &nbsp;/&nbsp; <span style="font-weight:bold; color:#1a56db;">Tiket Saya</span>
            </div>
        </div>
    </nav>

    <div class="container" style="margin-top: 40px; margin-bottom: 60px;">
        <h2 style="margin-bottom: 25px;">Riwayat Tiket</h2>

        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                
                <?php
                    $event_datetime = $row['tanggal'] . ' ' . $row['waktu'];
                    $current_datetime = date('Y-m-d H:i:s');
                    $is_finished = ($current_datetime > $event_datetime);

                    $gambar_db = $row['gambar'];
                    $path_gambar = "assets/images/" . $gambar_db;
                    
                    if (!empty($gambar_db) && file_exists($path_gambar)) {
                        $img_src = $path_gambar;
                    } else {
                        $img_src = "https://via.placeholder.com/200x200?text=No+Image";
                    }

                    // --- LOGIKA STATUS ---
                    $status = $row['status'];
                    $badgeClass = '';
                    $statusLabel = '';

                    // Prioritas 1: Jika Dibatalkan atau Ditolak 
                    if ($status == 'rejected' || $status == 'cancelled') {
                        $badgeClass = 'status-rejected';
                        $statusLabel = 'Dibatalkan';
                    } 
                    // Prioritas 2: Jika Waktu Event Sudah Lewat 
                    elseif ($is_finished) {
                        $badgeClass = 'status-finished';
                        $statusLabel = 'Selesai';
                    }
                    // Prioritas 3: Status Normal
                    elseif ($status == 'pending') {
                        $badgeClass = 'status-pending';
                        $statusLabel = 'Menunggu Pembayaran';
                    } elseif ($status == 'paid') {
                        $badgeClass = 'status-paid';
                        $statusLabel = 'Menunggu Verifikasi';
                    } elseif ($status == 'confirmed') {
                        $badgeClass = 'status-confirmed';
                        $statusLabel = 'Tiket Aktif';
                    }
                ?>

                <div class="ticket-card">
                    <div class="ticket-left">
                        <img src="<?= $img_src ?>" alt="<?= htmlspecialchars($row['nama_event']) ?>">
                    </div>
                    <div class="ticket-right">
                        <div>
                            <h3 style="margin-bottom: 5px;"><?= htmlspecialchars($row['nama_event']) ?></h3>
                            <p style="color: #666; font-size: 14px; margin-bottom: 5px;">
                                <i class="fa-regular fa-calendar"></i> <?= date('d F Y', strtotime($row['tanggal'])) ?> 
                                &nbsp;|&nbsp; 
                                <i class="fa-regular fa-clock"></i> <?= date('H:i', strtotime($row['waktu'])) ?>
                            </p>
                            <p style="font-size: 14px;">Total: <strong>Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></strong> (<?= $row['jumlah'] ?> Tiket)</p>
                        </div>
                        
                        <div style="text-align: right; min-width: 150px;">
                            <span class="status-badge <?= $badgeClass ?>"><?= $statusLabel ?></span>
                            
                            <?php if ($status == 'pending' && !$is_finished): ?>
                                <div style="margin-top: 10px;">
                                    <a href="pembayaran.php?id=<?= $row['ticket_id'] ?>" class="btn-buy" style="padding: 8px 15px; font-size: 13px; text-decoration:none; background:#1a56db; color:white; border-radius:6px; font-weight:bold;">Bayar Sekarang</a>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($status == 'confirmed' || ($is_finished && $status != 'rejected' && $status != 'cancelled')): ?>
                                <div style="margin-top: 10px;">
                                    <a href="eticket.php?id=<?= $row['ticket_id'] ?>" target="_blank" 
                                       style="display:inline-block; padding: 8px 15px; background: #333; color: white; border:none; border-radius:6px; text-decoration:none; font-size: 13px; font-weight:600;">
                                        <i class="fa-solid fa-ticket"></i> Lihat E-Ticket
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            <?php endwhile; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 50px; background: white; border-radius: 12px;">
                <p style="color: #888;">Belum ada tiket yang dibeli.</p>
                <a href="dashboard.php" style="color: #1a56db; font-weight: bold; text-decoration: none;">Cari Event Sekarang</a>
            </div>
        <?php endif; ?>
    </div>

<a href="https://wa.me/6281324351763?text=Halo%20Admin..." 
   class="wa-float" 
   target="_blank" 
   title="Hubungi CS">
    <i class="fa-brands fa-whatsapp"></i>
</a>
</body>
</html>