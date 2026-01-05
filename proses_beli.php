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
    $stmt->bind_param("iiids", $user_id, $event_id, $jumlah, $total_harga, $status);

    if ($stmt->execute()) {
        $ticket_id = $stmt->insert_id; 

        // Kurangi Kuota Event
        $conn->query("UPDATE events SET kuota = kuota - $jumlah WHERE event_id = $event_id");

        // Ke halaman Pembayaran
        header("Location: pembayaran.php?id=" . $ticket_id);
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    header("Location: index.php");
}
?>