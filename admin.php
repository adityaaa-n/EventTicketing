<?php
session_start();
require_once 'config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// HITUNG NOTIFIKASI TRANSAKSI BARU (status paid)
$notif_transaksi = 0;
$qNotif = $conn->query("SELECT COUNT(*) AS total FROM tickets WHERE status = 'paid'");
if ($qNotif) {
    $dataNotif = $qNotif->fetch_assoc();
    $notif_transaksi = (int)$dataNotif['total'];
}


// Ambil data lama jika mode Edit
$edit_mode = false;
$data_edit = [];
if (isset($_GET['edit'])) {
    $edit_mode = true;
    $id_edit = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM events WHERE event_id = ?");
    $stmt->bind_param("i", $id_edit);
    $stmt->execute();
    $data_edit = $stmt->get_result()->fetch_assoc();
}

// Simpan Data 
if (isset($_POST['simpan'])) {
    $nama_event = $_POST['nama_event'];
    $tanggal    = $_POST['tanggal'];
    $waktu      = $_POST['waktu'];
    $lokasi     = $_POST['lokasi'];
    $harga      = $_POST['harga'];
    $deskripsi  = $_POST['deskripsi'];
    $kategori   = $_POST['kategori'];
    $kuota      = $_POST['kuota']; 

    // UPLOAD GAMBAR
    $gambar_db = ""; 
    if (!empty($_FILES['gambar']['name'])) {
        $nama_file = $_FILES['gambar']['name'];
        $tmp_file  = $_FILES['gambar']['tmp_name'];
        $ext       = pathinfo($nama_file, PATHINFO_EXTENSION);
        $nama_baru = uniqid() . "." . $ext; 
        $folder    = "assets/images/";

        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        if (move_uploaded_file($tmp_file, $folder . $nama_baru)) {
            $gambar_db = $nama_baru;
        } else {
            echo "<script>alert('Gagal upload gambar!');</script>";
        }
    } else {
        if ($edit_mode) {
            $gambar_db = $_POST['gambar_lama'];
        }
    }

    if (isset($_POST['event_id']) && $_POST['event_id'] != "") {
        // UPDATE DATA
        $id = $_POST['event_id'];
        
        $stmt = $conn->prepare("UPDATE events SET nama_event=?, tanggal=?, waktu=?, lokasi=?, harga=?, deskripsi=?, kategori=?, kuota=?, gambar=? WHERE event_id=?");
        $stmt->bind_param("ssssdssisi", $nama_event, $tanggal, $waktu, $lokasi, $harga, $deskripsi, $kategori, $kuota, $gambar_db, $id);
        
    } else {
        // INSERT DATA BARU
        $stmt = $conn->prepare("INSERT INTO events (nama_event, tanggal, waktu, lokasi, harga, deskripsi, kategori, kuota, gambar) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssdssis", $nama_event, $tanggal, $waktu, $lokasi, $harga, $deskripsi, $kategori, $kuota, $gambar_db);
    }
    
    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil disimpan!'); window.location.href='admin.php';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan data: " . $stmt->error . "');</script>";
    }
}

// Hapus Event
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $q = $conn->query("SELECT gambar FROM events WHERE event_id = $id");
    $img = $q->fetch_assoc();
    if ($img['gambar'] && file_exists("assets/images/" . $img['gambar'])) {
        unlink("assets/images/" . $img['gambar']);
    }

    $conn->query("DELETE FROM events WHERE event_id = $id");
    header("Location: admin.php");
    exit;
}

// Ambil Daftar Event
$result = $conn->query("SELECT * FROM events ORDER BY event_id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Event - AdminPanel</title>
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
        .menu-logout:hover { background: #fee2e2; color: #c53030; }
        .main-content { margin-left: 260px; padding: 40px; width: 100%; }
        .page-title { margin-bottom: 25px; color: #333; font-size: 24px; font-weight: bold; }
        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.03); margin-bottom: 30px; border: 1px solid #eee; }
        .form-title { font-size: 16px; font-weight: bold; margin-bottom: 20px; color: #1a56db; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        input, select, textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; margin-bottom: 15px; font-size: 14px; outline: none; }
        input:focus, select:focus, textarea:focus { border-color: #1a56db; box-shadow: 0 0 0 3px rgba(26, 86, 219, 0.1); }
        .btn-submit { background: #1a56db; color: white; border: none; padding: 12px; border-radius: 8px; cursor: pointer; font-weight: 600; width: 100%; font-size: 14px; }
        .btn-submit:hover { background: #1545b3; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8f9fa; color: #333; padding: 15px; text-align: left; font-size: 14px; font-weight: 600; border-bottom: 2px solid #eee; }
        td { padding: 15px; border-bottom: 1px solid #eee; font-size: 14px; color: #444; vertical-align: middle; }
        .btn-act { padding: 6px 10px; border-radius: 6px; color: white; font-size: 12px; text-decoration: none; margin-right: 5px; display: inline-block; }
        .btn-edit { background: #28a745; }
        .btn-del { background: #dc3545; }
        .badge-stok { background: #e0f2fe; color: #0284c7; padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: bold; }
        .thumb-img { width: 50px; height: 50px; object-fit: cover; border-radius: 6px; border: 1px solid #ddd; }
        .notif-badge { background: #3b82f6; color: white; font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 999px; margin-left: auto; }
        .menu-item { display: flex; align-items: center; }

    </style>
</head>
<body>

<div class="sidebar">
    <h2><i class="fa-solid fa-ticket"></i> AdminPanel</h2>

    <a href="admin.php" class="menu-item active">
        <i class="fa-solid fa-calendar-days"></i>
        <span>Kelola Event</span>
    </a>

    <a href="admin_transaksi.php" class="menu-item">
        <i class="fa-solid fa-money-bill-transfer"></i>
        <span>Transaksi</span>

        <?php if ($notif_transaksi > 0): ?>
            <span class="notif-badge"><?= $notif_transaksi ?></span>
        <?php endif; ?>
    </a>

    <a href="logout.php" class="menu-item menu-logout">
        <i class="fa-solid fa-right-from-bracket"></i>
        <span>Logout</span>
    </a>
</div>


    <div class="main-content">
        <div class="page-title">Manajemen Event</div>

        <div class="card">
            <div class="form-title">
                <i class="fa-solid fa-pen-to-square"></i> <?= $edit_mode ? 'Edit Event' : 'Tambah Event Baru'; ?>
            </div>

            <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="event_id" value="<?= $edit_mode ? $data_edit['event_id'] : ''; ?>">
                <input type="hidden" name="gambar_lama" value="<?= $edit_mode ? $data_edit['gambar'] : ''; ?>">
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div>
                        <label style="font-size:12px; color:#666;">Nama Event</label>
                        <input type="text" name="nama_event" placeholder="Nama Event" required value="<?= $edit_mode ? $data_edit['nama_event'] : ''; ?>">
                    </div>
                    <div>
                        <label style="font-size:12px; color:#666;">Kategori</label>
                        <select name="kategori" required>
                            <option value="" disabled selected>Pilih Kategori</option>
                            <option value="Musik" <?= ($edit_mode && $data_edit['kategori'] == 'Musik') ? 'selected' : ''; ?>>Musik</option>
                            <option value="Olahraga" <?= ($edit_mode && $data_edit['kategori'] == 'Olahraga') ? 'selected' : ''; ?>>Olahraga</option>
                            <option value="Konferensi" <?= ($edit_mode && $data_edit['kategori'] == 'Konferensi') ? 'selected' : ''; ?>>Konferensi</option>
                            <option value="Lainnya" <?= ($edit_mode && $data_edit['kategori'] == 'Lainnya') ? 'selected' : ''; ?>>Lainnya</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div>
                        <label style="font-size:12px; color:#666;">Tanggal</label>
                        <input type="date" name="tanggal" required value="<?= $edit_mode ? $data_edit['tanggal'] : ''; ?>">
                    </div>
                    <div>
                        <label style="font-size:12px; color:#666;">Waktu</label>
                        <input type="time" name="waktu" required value="<?= $edit_mode ? $data_edit['waktu'] : ''; ?>">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div>
                        <label style="font-size:12px; color:#666;">Lokasi</label>
                        <input type="text" name="lokasi" placeholder="Lokasi Venue" required value="<?= $edit_mode ? $data_edit['lokasi'] : ''; ?>">
                    </div>
                    <div>
                        <label style="font-size:12px; color:#666;">Harga (Rp)</label>
                        <input type="number" name="harga" placeholder="Harga Tiket" required value="<?= $edit_mode ? $data_edit['harga'] : ''; ?>">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div>
                        <label style="font-size:12px; color:#666; font-weight:bold;">Kuota Tiket</label>
                        <input type="number" name="kuota" placeholder="Jumlah Tiket" required min="1" value="<?= $edit_mode ? $data_edit['kuota'] : ''; ?>">
                    </div>
                    <div>
                        <label style="font-size:12px; color:#666; font-weight:bold;">Banner Event</label>
                        <input type="file" name="gambar" accept="image/*">
                        <?php if($edit_mode && !empty($data_edit['gambar'])): ?>
                            <small style="color:#28a745;">Gambar saat ini: <?= $data_edit['gambar'] ?></small>
                        <?php endif; ?>
                    </div>
                </div>

                <textarea name="deskripsi" placeholder="Deskripsi Event" rows="3" required><?= $edit_mode ? $data_edit['deskripsi'] : ''; ?></textarea>

                <button type="submit" name="simpan" class="btn-submit">
                    <i class="fa-solid fa-save"></i> <?= $edit_mode ? 'Simpan Perubahan' : 'Terbitkan Event'; ?>
                </button>
                
                <?php if($edit_mode): ?>
                    <a href="admin.php" style="display:block; text-align:center; margin-top:10px; color:#666; text-decoration:none; font-size:14px;">Batal Edit</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Banner</th> <th>Event</th>
                        <th>Jadwal</th>
                        <th>Harga</th>
                        <th>Kuota</th> <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no=1; 
                    while($row = $result->fetch_assoc()) { 
                        $imgSrc = !empty($row['gambar']) ? "assets/images/".$row['gambar'] : "https://via.placeholder.com/50";
                        
                        echo "<tr>
                            <td>$no</td>
                            <td>
                                <img src='$imgSrc' class='thumb-img' alt='Banner'>
                            </td>
                            <td>
                                <strong>{$row['nama_event']}</strong><br>
                                <small style='color:#777'>{$row['kategori']}</small><br>
                                <small><i class='fa-solid fa-map-pin'></i> {$row['lokasi']}</small>
                            </td>
                            <td>
                                ".date('d M Y', strtotime($row['tanggal']))."<br>
                                <small>{$row['waktu']}</small>
                            </td>
                            <td>Rp ".number_format($row['harga'],0,',','.')."</td>
                            <td>
                                <span class='badge-stok'>{$row['kuota']} Tiket</span>
                            </td>
                            <td>
                                <a href='?edit={$row['event_id']}' class='btn-act btn-edit'><i class='fa-solid fa-pen'></i></a>
                                <a href='?hapus={$row['event_id']}' class='btn-act btn-del' onclick='return confirm(\"Hapus?\")'><i class='fa-solid fa-trash'></i></a>
                            </td>
                        </tr>";
                        $no++;
                    } 
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>