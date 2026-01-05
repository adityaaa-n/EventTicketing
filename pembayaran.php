<?php
session_start();
require_once 'config/koneksi.php';

// 1. CEK LOGIN 
if (!isset($_SESSION['user_id']) && !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

// 2. Cek ID Tiket
if (!isset($_GET['id'])) { header("Location: index.php"); exit; }
$ticket_id = $_GET['id'];

// 3. Ambil Detail Tiket
$query = "SELECT t.*, e.nama_event FROM tickets t 
          JOIN events e ON t.event_id = e.event_id 
          WHERE t.ticket_id = $ticket_id";
$result = $conn->query($query);
$data = $result->fetch_assoc();

if (isset($_POST['upload'])) {
    $nama_file   = $_FILES['bukti']['name'];
    $tmp_name    = $_FILES['bukti']['tmp_name'];
    
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }

    $ekstensiValid = ['jpg', 'jpeg', 'png'];
    $ekstensiFile  = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));

    if (!in_array($ekstensiFile, $ekstensiValid)) {
        echo "<script>alert('Format file harus JPG atau PNG!');</script>";
    } else {
        $nama_baru = uniqid() . '.' . $ekstensiFile;
        $target_file = $target_dir . $nama_baru;

        if (move_uploaded_file($tmp_name, $target_file)) {
            $cek = $conn->query("SELECT * FROM payment_logs WHERE ticket_id = $ticket_id");
            
            if ($cek->num_rows > 0) {
                // UPDATE
                $stmt = $conn->prepare("UPDATE payment_logs SET bukti_pembayaran = ?, waktu_bayar = NOW() WHERE ticket_id = ?");
                $stmt->bind_param("si", $nama_baru, $ticket_id);
            } else {
                // INSERT
                $stmt = $conn->prepare("INSERT INTO payment_logs (ticket_id, jumlah_bayar, waktu_bayar, bukti_pembayaran) VALUES (?, ?, NOW(), ?)");
                $stmt->bind_param("ids", $ticket_id, $data['total_harga'], $nama_baru);
            }

            if ($stmt->execute()) {
                $conn->query("UPDATE tickets SET status = 'paid' WHERE ticket_id = $ticket_id");
                
                echo "<script>
                        alert('Berhasil! Bukti pembayaran terkirim.'); 
                        window.location.href='tiket_saya.php'; 
                      </script>";
            } else {
                echo "<script>alert('Gagal update database.');</script>";
            }
        } else {
            echo "<script>alert('Gagal Upload File.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pembayaran</title>
    <style>
        body { font-family: sans-serif; background: #f4f4f4; display: flex; justify-content: center; padding-top: 50px; }
        .card { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 400px; text-align: center; }
        .btn { background: #1a56db; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; width: 100%; margin-top: 10px; font-weight: bold;}
        .btn:hover { background: #0d3c9e; }
        input[type=file] { margin: 20px 0; }
    </style>
</head>
<body>
    <div class="card">
        <h3>Pembayaran Tiket #<?= $ticket_id ?></h3>
        <p>Event: <strong><?= htmlspecialchars($data['nama_event']) ?></strong></p>
        <h2 style="color: #1a56db;">Rp <?= number_format($data['total_harga']) ?></h2>
        <hr>
        
        <form method="POST" enctype="multipart/form-data">
            <label>Upload Bukti Transfer:</label><br>
            <input type="file" name="bukti" required>
            <button type="submit" name="upload" class="btn">Kirim Bukti</button>
        </form>

        <br>
        <a href="tiket_saya.php" style="text-decoration: none; color: #666; font-size: 14px;">Kembali</a>
    </div>
</body>
</html>