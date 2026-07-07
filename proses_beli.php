<?php
session_start();
require_once 'config/koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil data dari form checkout
    $user_id = $_SESSION['user_id'];
    $event_id = $_POST['event_id'];
    $jumlah = $_POST['jumlah'];
    $total_harga = $_POST['total_bayar'];
    
    $status = 'pending'; 

    // Insert ke Tabel Tickets
    $stmt = $conn->prepare("INSERT INTO tickets (user_id, event_id, jumlah, total_harga, status) VALUES (?, ?, ?, ?, ?)");

    if ($stmt->execute([$user_id, $event_id, $jumlah, $total_harga, $status])) {
        $ticket_id = $conn->lastInsertId();

        // Kurangi Kuota Event
        $conn->prepare("UPDATE events SET kuota = kuota - ? WHERE event_id = ?")->execute([$jumlah, $event_id]);

        // Ke halaman Pembayaran
        header("Location: pembayaran.php?id=" . $ticket_id);
    } else {
        echo "Error: Gagal menyimpan tiket.";
    }
} else {
    header("Location: index.php");
}
?>