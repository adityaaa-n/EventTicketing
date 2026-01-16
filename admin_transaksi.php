<?php
session_start();
require_once 'config/koneksi.php';

// Cek Role Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Set Timezone
date_default_timezone_set('Asia/Jakarta');
$sekarang = date('Y-m-d H:i:s');

// --- BAGIAN 1: LOGIKA HAPUS DATA (DIPERBARUI) ---
if (isset($_GET['hapus_id'])) {
    $id_hapus = intval($_GET['hapus_id']);

    // Cek dulu datanya
    $cek_query = "SELECT t.status, CONCAT(e.tanggal, ' ', e.waktu) as waktu_event 
                  FROM tickets t 
                  JOIN events e ON t.event_id = e.event_id 
                  WHERE t.ticket_id = $id_hapus";
    $cek_result = $conn->query($cek_query);
    
    if ($cek_result->num_rows > 0) {
        $data_cek = $cek_result->fetch_assoc();
        $is_expired = ($sekarang > $data_cek['waktu_event']);

        // SYARAT HAPUS DIPERLUAS: 
        // Boleh hapus jika: Ditolak (Rejected) ATAU Selesai (Confirmed) ATAU Expired
        if ($data_cek['status'] == 'rejected' || $data_cek['status'] == 'confirmed' || $is_expired) {
            
            // Hapus Payment Log dulu (agar tidak error foreign key)
            $conn->query("DELETE FROM payment_logs WHERE ticket_id = $id_hapus");
            
            // Hapus Tiket
            $stmt = $conn->prepare("DELETE FROM tickets WHERE ticket_id = ?");
            $stmt->bind_param("i", $id_hapus);
            
            if ($stmt->execute()) {
                $_SESSION['flash_message'] = "Data riwayat transaksi berhasil dihapus.";
                $_SESSION['flash_type'] = "success";
            } else {
                $_SESSION['flash_message'] = "Gagal menghapus data.";
                $_SESSION['flash_type'] = "error";
            }
            $stmt->close();
        } else {
            $_SESSION['flash_message'] = "Data yang sedang diproses (Paid/Pending) tidak boleh dihapus.";
            $_SESSION['flash_type'] = "error";
        }
    }
    header("Location: admin_transaksi.php");
    exit;
}

// --- BAGIAN 2: LOGIKA UPDATE STATUS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['ticket_id'])) {
    $id = intval($_POST['ticket_id']);
    $action = $_POST['action'];
    $status_baru = '';

    if ($action === 'terima') $status_baru = 'confirmed';
    elseif ($action === 'tolak') $status_baru = 'rejected';

    if ($status_baru) {
        $stmt = $conn->prepare("UPDATE tickets SET status = ? WHERE ticket_id = ?");
        $stmt->bind_param("si", $status_baru, $id);
        $stmt->execute();
        $stmt->close();
        
        $_SESSION['flash_message'] = "Status berhasil diperbarui.";
        $_SESSION['flash_type'] = "success";
    }
    header("Location: admin_transaksi.php");
    exit;
}

// --- BAGIAN 3: QUERY DATA ---
$query = "SELECT t.*, u.nama as nama_user, e.nama_event, e.tanggal, e.waktu, p.waktu_bayar, p.bukti_pembayaran 
          FROM tickets t 
          JOIN users u ON t.user_id = u.user_id 
          JOIN events e ON t.event_id = e.event_id
          LEFT JOIN payment_logs p ON t.ticket_id = p.ticket_id
          ORDER BY FIELD(t.status, 'paid', 'pending', 'confirmed', 'rejected'), t.tanggal_beli DESC";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Transaksi - AdminPanel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background-color: #f0f2f5; display: flex; min-height: 100vh; }
        
        .sidebar { width: 260px; background: white; padding: 30px 20px; position: fixed; top: 0; left: 0; height: 100%; border-right: 1px solid #e0e0e0; z-index: 100; }
        .sidebar h2 { color: #1a56db; font-size: 22px; font-weight: 700; margin-bottom: 40px; display: flex; align-items: center; gap: 10px; padding-left: 10px; }
        .menu-item { display: flex; align-items: center; gap: 12px; padding: 12px 20px; color: #555; text-decoration: none; margin-bottom: 8px; border-radius: 8px; font-size: 14px; font-weight: 500; transition: all 0.2s ease; }
        .menu-item:hover { background: #f0f4ff; color: #1a56db; }
        .menu-item.active { background: #eef2ff; color: #1a56db; font-weight: 600; border-left: 4px solid #1a56db; }
        .menu-logout { margin-top: 50px; color: #e3342f; }
        
        .main-content { margin-left: 260px; padding: 40px; width: 100%; }
        .page-title { margin-bottom: 25px; color: #333; font-size: 24px; font-weight: bold; }
        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.03); border: 1px solid #eee; overflow-x: auto; }

        table { width: 100%; border-collapse: collapse; min-width: 900px; }
        th { background: #f8f9fa; color: #333; padding: 15px; text-align: left; font-size: 14px; font-weight: 600; border-bottom: 2px solid #eee; }
        td { padding: 15px; border-bottom: 1px solid #eee; font-size: 14px; color: #444; vertical-align: middle; }
        
        .badge { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; text-transform: uppercase; display: inline-block; }
        .bg-paid { background: #d1ecf1; color: #0c5460; }
        .bg-confirmed { background: #d4edda; color: #155724; }
        .bg-pending { background: #fff3cd; color: #856404; }
        .bg-rejected { background: #f8d7da; color: #721c24; }
        .bg-expired { background: #e2e3e5; color: #383d41; border: 1px solid #d6d8db; }

        .action-form { display: inline-block; margin-right: 5px; }
        .btn-act { border: none; padding: 8px 14px; border-radius: 6px; color: white; font-size: 12px; cursor: pointer; font-weight: 600; display: inline-flex; align-items: center; gap: 5px; transition: 0.2s; text-decoration: none;}
        .btn-acc { background: #28a745; } .btn-acc:hover { background: #218838; }
        .btn-tolak { background: #dc3545; } .btn-tolak:hover { background: #c82333; }
        
        .btn-hapus { background: #6c757d; color: white; padding: 8px 14px; border-radius: 6px; font-size: 12px; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; }
        .btn-hapus:hover { background: #5a6268; }
        
        .btn-lihat { background: #3b82f6; color: white; padding: 6px 12px; border-radius: 6px; font-size: 12px; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; }
        .btn-lihat:hover { background: #2563eb; }

        .alert { padding: 15px; margin-bottom: 20px; border-radius: 8px; font-size: 14px; display: flex; align-items: center; justify-content: space-between; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2><i class="fa-solid fa-ticket"></i> AdminPanel</h2>
        <a href="admin.php" class="menu-item"><i class="fa-solid fa-calendar-days"></i> Kelola Event</a>
        <a href="admin_transaksi.php" class="menu-item active"><i class="fa-solid fa-money-bill-transfer"></i> Transaksi</a>
        <a href="logout.php" class="menu-item menu-logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </div>

    <div class="main-content">
        <div class="page-title">Daftar Transaksi Masuk</div>

        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="alert <?= $_SESSION['flash_type'] == 'success' ? 'alert-success' : 'alert-error' ?>">
                <span><i class="fa-solid fa-circle-info"></i> <?= $_SESSION['flash_message'] ?></span>
                <?php unset($_SESSION['flash_message']); unset($_SESSION['flash_type']); ?>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Event</th>
                        <th>Bukti</th> <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <?php 
                            $event_time = $row['tanggal'] . ' ' . $row['waktu'];
                            $is_expired = ($sekarang > $event_time);
                        ?>
                        <tr>
                            <td>#<?= $row['ticket_id'] ?></td>
                            <td>
                                <strong><?= htmlspecialchars($row['nama_user']) ?></strong><br>
                                <small style="color:#888;">
                                    <?= isset($row['waktu_bayar']) ? date('d M H:i', strtotime($row['waktu_bayar'])) : '-' ?>
                                </small>
                            </td>
                            <td>
                                <?= htmlspecialchars($row['nama_event']) ?><br>
                                <?php if($is_expired): ?>
                                    <small style="color:red; font-style:italic;">(Event Selesai)</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($row['bukti_pembayaran'])): ?>
                                    <a href="uploads/<?= $row['bukti_pembayaran'] ?>" target="_blank" class="btn-lihat"><i class="fa-solid fa-image"></i> Lihat</a>
                                <?php else: ?>
                                    <span style="color:#ccc; font-size:12px;">-</span>
                                <?php endif; ?>
                            </td>
                            <td style="font-family: monospace;">Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                            <td>
                                <?php 
                                    $s = $row['status'];
                                    $cls = $s=='paid'?'bg-paid':($s=='confirmed'?'bg-confirmed':($s=='rejected'?'bg-rejected':'bg-pending'));
                                    if ($s == 'pending' && $is_expired) { $label = 'Kadaluarsa'; $cls = 'bg-expired'; }
                                    else { $label = ($s=='paid') ? 'Menunggu' : ucfirst($s); }
                                ?>
                                <span class="badge <?= $cls ?>"><?= $label ?></span>
                            </td>
                            <td>
                                <?php if ($row['status'] == 'paid'): ?>
                                    <form method="POST" class="action-form" onsubmit="return confirm('Terima pembayaran?');">
                                        <input type="hidden" name="ticket_id" value="<?= $row['ticket_id'] ?>">
                                        <input type="hidden" name="action" value="terima">
                                        <button type="submit" class="btn-act btn-acc" title="Terima"><i class="fa-solid fa-check"></i></button>
                                    </form>
                                    <form method="POST" class="action-form" onsubmit="return confirm('Tolak pembayaran?');">
                                        <input type="hidden" name="ticket_id" value="<?= $row['ticket_id'] ?>">
                                        <input type="hidden" name="action" value="tolak">
                                        <button type="submit" class="btn-act btn-tolak" title="Tolak"><i class="fa-solid fa-xmark"></i></button>
                                    </form>

                                <?php elseif ($row['status'] == 'confirmed'): ?>
                                    <i class="fa-solid fa-check" style="color:green; font-size: 16px; margin-right: 8px;"></i>
                                    <a href="admin_transaksi.php?hapus_id=<?= $row['ticket_id'] ?>" class="btn-hapus" onclick="return confirm('Hapus riwayat sukses ini?');" title="Hapus Riwayat">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>

                                <?php elseif ($row['status'] == 'rejected'): ?>
                                    <a href="admin_transaksi.php?hapus_id=<?= $row['ticket_id'] ?>" class="btn-hapus" onclick="return confirm('Hapus data ditolak ini?');" title="Hapus Data">
                                        <i class="fa-solid fa-trash"></i> Hapus
                                    </a>

                                <?php elseif ($is_expired): ?>
                                    <a href="admin_transaksi.php?hapus_id=<?= $row['ticket_id'] ?>" class="btn-hapus" onclick="return confirm('Hapus data kadaluarsa ini?');" title="Hapus Data">
                                        <i class="fa-solid fa-trash"></i> Hapus
                                    </a>
                                <?php else: ?>
                                    <span style="color:#aaa;">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" style="text-align:center;">Belum ada data.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>