<?php
session_start();
require_once 'config/koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ticket_id = $_POST['ticket_id'];
    
    $conn->query("UPDATE tickets SET status = 'paid' WHERE ticket_id = '$ticket_id'");

    $cek = $conn->query("SELECT total_harga FROM tickets WHERE ticket_id = '$ticket_id'");
    $row = $cek->fetch_assoc();
    $nominal = $row['total_harga'];
    
    $metode = "Transfer Bank"; 
    
    $stmt = $conn->prepare("INSERT INTO payment_logs (ticket_id, metode, nominal) VALUES (?, ?, ?)");
    $stmt->bind_param("isd", $ticket_id, $metode, $nominal);
    $stmt->execute();

    echo "<script>
            alert('Pembayaran berhasil dikonfirmasi! Menunggu verifikasi admin.');
            window.location.href='tiket_saya.php';
          </script>";
} else {
    header("Location: dashboard.php");
}
?>