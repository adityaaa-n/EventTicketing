<?php
// --- Koneksi ke database ---
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_event_ticketing";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Koneksi gagal: " . $conn->connect_error);
}

$popup = ""; // untuk SweetAlert

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $nama = trim($_POST["nama"]);
  $email = trim($_POST["email"]);
  $pass = trim($_POST["password"]);

  if (!empty($nama) && !empty($email) && !empty($pass)) {
    $check = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
      $popup = "email_exists";
    } else {
      // Simpan password tanpa hash
      $stmt = $conn->prepare("INSERT INTO users (nama, email, password) VALUES (?, ?, ?)");
      $stmt->bind_param("sss", $nama, $email, $pass);

      if ($stmt->execute()) {
        $popup = "success";
      } else {
        $popup = "error";
      }

      $stmt->close();
    }
    $check->close();
  }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar - EventTix</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      background-color: #f0f2f5;
    }
    .register-card {
      background: #fff;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 400px;
      text-align: center;
    }
    .register-logo {
      font-size: 32px;
      font-weight: bold;
      color: #1a56db;
      margin-bottom: 10px;
      display: inline-block;
    }
    .register-subtitle {
      color: #666;
      margin-bottom: 30px;
      font-size: 14px;
    }
    .form-group {
      margin-bottom: 20px;
      text-align: left;
    }
    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-size: 14px;
      color: #333;
      font-weight: 500;
    }
    .form-control {
      width: 100%;
      padding: 12px 15px;
      border: 1px solid #ddd;
      border-radius: 8px;
      font-size: 14px;
      outline: none;
      transition: 0.3s;
    }
    .form-control:focus {
      border-color: #1a56db;
      box-shadow: 0 0 0 3px rgba(26, 86, 219, 0.1);
    }
    .btn-register {
      width: 100%;
      padding: 12px;
      background-color: #1a56db;
      color: white;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      font-size: 16px;
      cursor: pointer;
      transition: 0.3s;
    }
    .btn-register:hover {
      background-color: #1545b3;
    }
    .register-footer {
      margin-top: 20px;
      font-size: 13px;
      color: #666;
    }
    .register-footer a {
      color: #1a56db;
      text-decoration: none;
      font-weight: 600;
    }
    .register-footer a:hover {
      text-decoration: underline;
    }
    .back-link {
      display: block;
      margin-top: 20px;
      color: #888;
      text-decoration: none;
      font-size: 13px;
    }
  </style>
</head>
<body>

  <div class="register-card">
    <div class="register-logo">
      <i class="fa-solid fa-ticket"></i> EventTix
    </div>
    <p class="register-subtitle">Daftar untuk mulai membeli tiket event favoritmu</p>

    <form action="" method="POST">
      <div class="form-group">
        <label>Nama Lengkap</label>
        <input type="text" name="nama" class="form-control" placeholder="Masukkan nama lengkap" required>
      </div>

      <div class="form-group">
        <label>Email Address</label>
        <input type="email" name="email" class="form-control" placeholder="user@example.com" required>
      </div>

      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
      </div>

      <button type="submit" class="btn-register">Daftar</button>
    </form>

    <div class="register-footer">
      Sudah punya akun? <a href="login.php">Masuk Sekarang</a>
    </div>

    <a href="index.php" class="back-link">&larr; Kembali ke Beranda</a>
  </div>

  <script>
    <?php if ($popup == "success"): ?>
      Swal.fire({
        icon: 'success',
        title: 'PENDAFTARAN AKUN TELAH BERHASIL',
        text: 'Silahkan login untuk melanjutkan.',
        confirmButtonText: 'Masuk Sekarang',
        confirmButtonColor: '#1a56db'
      }).then(() => {
        window.location.href = 'login.php';
      });
    <?php elseif ($popup == "email_exists"): ?>
      Swal.fire({
        icon: 'warning',
        title: 'EMAIL SUDAH TERDAFTAR',
        text: 'Gunakan email lain untuk mendaftar.',
        confirmButtonColor: '#1a56db'
      });
    <?php elseif ($popup == "error"): ?>
      Swal.fire({
        icon: 'error',
        title: 'TERJADI KESALAHAN',
        text: 'Gagal menyimpan data. Coba lagi nanti.',
        confirmButtonColor: '#1a56db'
      });
    <?php endif; ?>
  </script>

</body>
</html>
