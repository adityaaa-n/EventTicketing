<?php
ob_start();
session_start();
require_once 'config/koneksi.php';

if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin.php");
    } elseif ($_SESSION['role'] === 'user') {
        header("Location: dashboard.php");
    }
    exit;
}

$error = "";

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {

        $stmt = $conn->prepare("SELECT admin_id, nama, password FROM admins WHERE email = ?");
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result_admin = $stmt->get_result();

            if ($result_admin && $result_admin->num_rows === 1) {
                $admin = $result_admin->fetch_assoc();

                if ($password === $admin['password']) {
                    $_SESSION['admin_id'] = $admin['admin_id'];
                    $_SESSION['nama'] = $admin['nama'];
                    $_SESSION['email'] = $email;
                    $_SESSION['role'] = 'admin';

                    header("Location: admin.php");
                    exit;
                } else {
                    $error = "Password salah!";
                }
            } else {
                $stmt = $conn->prepare("SELECT user_id, nama, password FROM users WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result_user = $stmt->get_result();

                if ($result_user && $result_user->num_rows === 1) {
                    $user = $result_user->fetch_assoc();

                    if ($password === $user['password']) {
                        $_SESSION['user_id'] = $user['user_id'];
                        $_SESSION['nama'] = $user['nama'];
                        $_SESSION['email'] = $email;
                        $_SESSION['role'] = 'user';

                        header("Location: dashboard.php");
                        exit;
                    } else {
                        $error = "Password salah!";
                    }
                } else {
                    $error = "Email tidak ditemukan!";
                }
            }

            $stmt->close();
        } else {
            $error = "Terjadi kesalahan pada query database.";
        }
    } else {
        $error = "Semua kolom wajib diisi!";
    }
}

ob_end_flush();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EventTix</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-card {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .login-logo {
            font-size: 32px;
            font-weight: bold;
            color: #1a56db;
            margin-bottom: 10px;
            display: inline-block;
        }
        .login-subtitle {
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
        .btn-login {
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
        .btn-login:hover {
            background-color: #1545b3;
        }
        .login-footer {
            margin-top: 20px;
            font-size: 13px;
            color: #666;
        }
        .login-footer a {
            color: #1a56db;
            text-decoration: none;
            font-weight: 600;
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
<!-- Debug login -->
 
    <div class="login-card">
        <div class="login-logo">
            <i class="fa-solid fa-ticket"></i> EventTix
        </div>
        <p class="login-subtitle">Masuk untuk membeli tiket event favoritmu</p>

        <form action="" method="POST">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="user@example.com" required>
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" name="login" class="btn-login">Masuk</button>
        </form>

        <div class="login-footer">
            Belum punya akun? <a href="register.php">Daftar Sekarang</a>
        </div>

        <a href="index.php" class="back-link">&larr; Kembali ke Beranda</a>
    </div>

    <?php if (!empty($error)): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Login Gagal',
            text: '<?php echo $error; ?>',
            confirmButtonColor: '#1a56db'
        });
    </script>
    <?php endif; ?>

</body>
</html>
