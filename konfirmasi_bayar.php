<?php
session_start();
require_once 'config/koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ticket_id = $_POST['ticket_id'];
    
    $conn->prepare("UPDATE tickets SET status = 'paid' WHERE ticket_id = ?")->execute([$ticket_id]);

    $cek = $conn->prepare("SELECT total_harga FROM tickets WHERE ticket_id = ?");
    $cek->execute([$ticket_id]);
    $row = $cek->fetch(PDO::FETCH_ASSOC);
    $nominal = $row['total_harga'];
    
    $metode = "Transfer Bank"; 
    
    $stmt = $conn->prepare("INSERT INTO payment_logs (ticket_id, metode, nominal) VALUES (?, ?, ?)");
    $stmt->execute([$ticket_id, $metode, $nominal]);

    echo "<script>
            alert('Pembayaran berhasil dikonfirmasi! Menunggu verifikasi admin.');
            window.location.href='tiket_saya.php';
          </script>";
} else {
    header("Location: dashboard.php");
}
?>