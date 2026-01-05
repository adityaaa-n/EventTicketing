<?php
session_start();
require_once 'config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    echo "Tiket tidak ditemukan.";
    exit;
}

$ticket_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

$query = "SELECT t.*, e.nama_event, e.tanggal, e.waktu, e.lokasi, u.nama as nama_user, u.email
          FROM tickets t 
          JOIN events e ON t.event_id = e.event_id
          JOIN users u ON t.user_id = u.user_id
          WHERE t.ticket_id = ? AND t.user_id = ? AND t.status = 'confirmed'";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $ticket_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    echo "<div style='text-align:center; padding:50px;'>
            <h2>Akses Ditolak</h2>
            <p>Tiket tidak ditemukan atau belum terverifikasi.</p>
            <a href='tiket_saya.php'>Kembali</a>
          </div>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>E-Ticket #<?= $ticket_id ?> - EventTix</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #555;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 40px 0;
            display: flex;
            justify-content: center;
            min-height: 100vh;
        }

        .ticket-container {
            background: white;
            width: 700px;
            max-width: 90%;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            display: flex;
            flex-direction: column;
        }

        .ticket-header {
            background: #1a56db;
            color: white;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .app-logo { font-size: 24px; font-weight: bold; display: flex; align-items: center; gap: 10px; }
        .ticket-id { font-size: 14px; opacity: 0.8; }

        .ticket-body {
            padding: 30px;
            display: flex;
            gap: 30px;
        }

        .ticket-details { flex: 2; border-right: 2px dashed #eee; padding-right: 30px; }
        
        .event-name { font-size: 28px; font-weight: 800; color: #333; margin-bottom: 5px; line-height: 1.2; }
        .event-cat { color: #1a56db; font-weight: 600; font-size: 14px; text-transform: uppercase; margin-bottom: 20px; display: block; }

        .info-row { display: flex; gap: 40px; margin-bottom: 20px; }
        .info-box label { display: block; font-size: 12px; color: #888; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px; }
        .info-box span { font-size: 16px; font-weight: 600; color: #333; }

        .ticket-qr {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        .qr-img { width: 150px; height: 150px; margin-bottom: 10px; }
        .qr-text { font-size: 12px; color: #666; }

        .ticket-footer {
            background: #f8f9fa;
            padding: 15px 30px;
            border-top: 1px solid #eee;
            text-align: center;
            font-size: 12px;
            color: #888;
        }

        .action-bar {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            background: white;
            padding: 15px 25px;
            border-radius: 50px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
            display: flex;
            gap: 15px;
        }
        .btn {
            padding: 10px 20px;
            border-radius: 25px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }
        .btn-print { background: #1a56db; color: white; }
        .btn-home { background: #e9ecef; color: #333; }

        @media print {
            body { background: white; padding: 0; }
            .ticket-container { box-shadow: none; border: 1px solid #ddd; width: 100%; max-width: 100%; }
            .action-bar { display: none; } 
        }
        
        @media (max-width: 600px) {
            .ticket-body { flex-direction: column-reverse; }
            .ticket-details { border-right: none; border-top: 2px dashed #eee; padding-right: 0; padding-top: 20px; }
        }
    </style>
</head>
<body>

    <div class="ticket-container">
        <div class="ticket-header">
            <div class="app-logo"><i class="fa-solid fa-ticket"></i> EventTix</div>
            <div class="ticket-id">ID: #<?= str_pad($ticket_id, 6, '0', STR_PAD_LEFT); ?></div>
        </div>

        <div class="ticket-body">
            <div class="ticket-details">
                <span class="event-cat">OFFICIAL E-TICKET</span>
                <div class="event-name"><?= htmlspecialchars($data['nama_event']) ?></div>
                
                <div style="margin: 20px 0; border-top: 1px solid #eee; padding-top: 20px;">
                    <div class="info-row">
                        <div class="info-box">
                            <label>Nama Pemilik</label>
                            <span><?= htmlspecialchars($data['nama_user']) ?></span>
                        </div>
                        <div class="info-box">
                            <label>Jumlah Tiket</label>
                            <span><?= $data['jumlah'] ?> Pax</span>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-box">
                            <label>Tanggal</label>
                            <span><?= date('d F Y', strtotime($data['tanggal'])) ?></span>
                        </div>
                        <div class="info-box">
                            <label>Waktu</label>
                            <span><?= date('H:i', strtotime($data['waktu'])) ?> WIB</span>
                        </div>
                    </div>

                    <div class="info-box">
                        <label>Lokasi</label>
                        <span><?= htmlspecialchars($data['lokasi']) ?></span>
                    </div>
                </div>
            </div>

            <div class="ticket-qr">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=TICKET-<?= $ticket_id ?>-User-<?= $user_id ?>" 
                     alt="QR Code" class="qr-img">
                <div class="qr-text">Scan di pintu masuk</div>
                <div style="margin-top: 10px; font-weight: bold; color: #1a56db; font-size: 18px;">
                    PAID
                </div>
            </div>
        </div>

        <div class="ticket-footer">
            Harap tunjukkan E-Ticket ini kepada petugas di lokasi acara. Dilarang menggandakan tiket.
        </div>
    </div>

    <div class="action-bar">
        <a href="tiket_saya.php" class="btn btn-home"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
        <button onclick="window.print()" class="btn btn-print"><i class="fa-solid fa-print"></i> Cetak Tiket</button>
    </div>

</body>
</html>