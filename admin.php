<?php
session_start();
require_once 'config/koneksi.php';

// Cek apakah admin sudah login
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Jika sedang edit, ambil data lama
$edit_mode = false;
if (isset($_GET['edit'])) {
    $edit_mode = true;
    $id_edit = $_GET['edit'];
    $query_edit = $conn->prepare("SELECT * FROM events WHERE event_id = ?");
    $query_edit->bind_param("i", $id_edit);
    $query_edit->execute();
    $data_edit = $query_edit->get_result()->fetch_assoc();
}

// Tambah atau update event
if (isset($_POST['simpan'])) {
    $nama_event = $_POST['nama_event'];
    $tanggal = $_POST['tanggal'];
    $waktu = $_POST['waktu'];
    $lokasi = $_POST['lokasi'];
    $harga = $_POST['harga'];
    $deskripsi = $_POST['deskripsi'];

    if (isset($_POST['event_id']) && $_POST['event_id'] != "") {
        // Update event
        $id = $_POST['event_id'];
        $stmt = $conn->prepare("UPDATE events SET nama_event=?, tanggal=?, waktu=?, lokasi=?, harga=?, deskripsi=? WHERE event_id=?");
        $stmt->bind_param("ssssdsi", $nama_event, $tanggal, $waktu, $lokasi, $harga, $deskripsi, $id);
        $stmt->execute();
    } else {
        // Tambah event baru
        $stmt = $conn->prepare("INSERT INTO events (nama_event, tanggal, waktu, lokasi, harga, deskripsi) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssds", $nama_event, $tanggal, $waktu, $lokasi, $harga, $deskripsi);
        $stmt->execute();
    }

    header("Location: admin.php");
    exit;
}

// Hapus event
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $conn->query("DELETE FROM events WHERE event_id = $id");
    header("Location: admin.php");
    exit;
}

// Ambil semua event
$result = $conn->query("SELECT * FROM events ORDER BY tanggal ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - EventTix</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0; padding: 0;
            box-sizing: border-box;
            user-select: none;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5;
        }
        .admin-container {
            max-width: 1000px;
            margin: 50px auto;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        h1 {
            color: #1a56db;
            font-size: 28px;
            text-align: center;
            margin-bottom: 10px;
        }
        h2 {
            color: #333;
            font-size: 20px;
            margin-top: 30px;
            border-left: 4px solid #1a56db;
            padding-left: 10px;
        }
        .logout-btn {
            float: right;
            background: #e3342f;
            color: white;
            padding: 8px 15px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            margin-top: -40px;
        }
        .logout-btn:hover { background: #c53030; }
        form {
            margin-top: 20px;
            background: #f9fafc;
            padding: 20px;
            border-radius: 10px;
        }
        input, textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            margin: 8px 0;
            font-size: 14px;
            transition: 0.3s;
        }
        textarea {
            resize: none;
            height: 80px;
            overflow-y: auto;
        }
        input:focus, textarea:focus {
            border-color: #1a56db;
            box-shadow: 0 0 0 3px rgba(26, 86, 219, 0.1);
            outline: none;
        }
        button {
            background-color: #1a56db;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 15px;
            cursor: pointer;
            transition: 0.3s;
        }
        button:hover { background-color: #1545b3; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            text-align: left;
            font-size: 14px;
        }
        th {
            background-color: #1a56db;
            color: white;
            font-weight: 600;
        }
        tr:hover { background-color: #f7f9fc; }
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 8px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 10px;
            border-radius: 6px;
            text-decoration: none;
            color: white;
            font-size: 13px;
            transition: 0.3s;
        }
        .btn-edit { background: #38c172; }
        .btn-edit:hover { background: #2fa360; }
        .btn-hapus { background: #e3342f; }
        .btn-hapus:hover { background: #c53030; }
        .no-data { text-align: center; color: #888; padding: 20px; }
    </style>
</head>
<body oncontextmenu="return false">

<div class="admin-container">
    <h1><i class="fa-solid fa-user-gear"></i> Panel Admin EventTix</h1>
    <a href="logout.php" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>

    <h2><i class="fa-solid fa-plus-circle"></i> <?= $edit_mode ? 'Edit Event' : 'Tambah Event Baru'; ?></h2>
    <form action="" method="POST">
        <input type="hidden" name="event_id" value="<?= $edit_mode ? $data_edit['event_id'] : ''; ?>">
        <input type="text" name="nama_event" placeholder="Nama Event" required value="<?= $edit_mode ? $data_edit['nama_event'] : ''; ?>">
        <input type="date" name="tanggal" required value="<?= $edit_mode ? $data_edit['tanggal'] : ''; ?>">
        <input type="time" name="waktu" required value="<?= $edit_mode ? $data_edit['waktu'] : ''; ?>">
        <input type="text" name="lokasi" placeholder="Lokasi" required value="<?= $edit_mode ? $data_edit['lokasi'] : ''; ?>">
        <input type="number" name="harga" placeholder="Harga Tiket" required value="<?= $edit_mode ? $data_edit['harga'] : ''; ?>">
        <textarea name="deskripsi" placeholder="Deskripsi Event" required><?= $edit_mode ? $data_edit['deskripsi'] : ''; ?></textarea>

        <button type="submit" name="simpan">
            <i class="fa-solid fa-circle-check"></i> <?= $edit_mode ? 'Simpan Perubahan' : 'Tambah Event'; ?>
        </button>

        <?php if ($edit_mode): ?>
            <a href="admin.php" style="margin-left:10px; background:#6c757d; padding:10px 20px; border-radius:8px; color:white; text-decoration:none;">
                <i class="fa-solid fa-xmark"></i> Batal
            </a>
        <?php endif; ?>
    </form>

    <h2><i class="fa-solid fa-list"></i> Daftar Event</h2>
    <table>
        <tr>
            <th>No</th>
            <th>Nama Event</th>
            <th>Tanggal</th>
            <th>Waktu</th>
            <th>Lokasi</th>
            <th>Harga</th>
            <th style="text-align:center;">Aksi</th>
        </tr>

        <?php
        $no = 1;
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "
                <tr>
                    <td>$no</td>
                    <td>{$row['nama_event']}</td>
                    <td>{$row['tanggal']}</td>
                    <td>{$row['waktu']}</td>
                    <td>{$row['lokasi']}</td>
                    <td>Rp " . number_format($row['harga'], 0, ',', '.') . "</td>
                    <td>
                        <div class='action-buttons'>
                            <a href='admin.php?edit={$row['event_id']}' class='btn btn-edit'><i class='fa-solid fa-pen-to-square'></i> Edit</a>
                            <a href='admin.php?hapus={$row['event_id']}' class='btn btn-hapus' onclick='return confirm(\"Yakin ingin menghapus event ini?\")'><i class='fa-solid fa-trash'></i> Hapus</a>
                        </div>
                    </td>
                </tr>";
                $no++;
            }
        } else {
            echo "<tr><td colspan='7' class='no-data'>Belum ada event yang terdaftar</td></tr>";
        }
        ?>
    </table>
</div>

</body>
</html>
