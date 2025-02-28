<?php
session_start();
session_destroy();
session_start();
$_SESSION['step'] = 0;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$servername = "localhost";
$username = "root";
$password = "";
$database = "libcendana2220";
$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if (!isset($_SESSION['step']) || $_SESSION['step'] == 0) {
    $_SESSION['step'] = 0;
}

if (isset($_POST['send_code']) && $_SESSION['step'] == 0) {
    $email = $_POST['email'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Email tidak valid.");
    }

    $_SESSION['email'] = $email;
    $_SESSION['verification_code'] = rand(1000, 9999);

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'kiinstore48@gmail.com';
        $mail->Password = 'erigjdrddtxrnupd';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        
        $mail->setFrom('kiinstore48@gmail.com', 'Bapakmu');
        $mail->addAddress($email);
        $mail->Subject = "Kode Verifikasi";
        $mail->Body = "Kode verifikasi Anda adalah: " . $_SESSION['verification_code'];
        
        $mail->send();
        $_SESSION['step'] = 1; 
    } catch (Exception $e) {
        die("Email gagal dikirim. Error: {$mail->ErrorInfo}");
    }
}

if (isset($_POST['verify_code']) && $_SESSION['step'] == 1) {
    if ($_POST['verification_code'] == $_SESSION['verification_code']) {
        $_SESSION['step'] = 2; 
    } else {
        die("Kode verifikasi salah, coba lagi.");
    }
}

if (isset($_POST['register']) && $_SESSION['step'] == 2) {
    $username = $_POST['username'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $email = $_SESSION['email'];

    $stmt = $conn->prepare("INSERT INTO users (email, username, phone, address, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $email, $username, $phone, $address, $password);

    if ($stmt->execute()) {
        echo "Registrasi berhasil!";
        session_destroy();
        exit();
    } else {
        die("Gagal menyimpan data: " . $stmt->error);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Registrasi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e3e3e3;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: #ffffff;
            padding: 25px;
            border-radius: 5px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.2);
            width: 400px;
            text-align: center;
            border: 2px solid #007bff;
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        label {
            text-align: left;
            margin-top: 10px;
            font-weight: bold;
            color: #555;
        }
        input, textarea {
            width: calc(100% - 30px);
            padding: 12px;
            margin-top: 5px;
            border: 2px solid #007bff;
            border-radius: 5px;
            font-size: 16px;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            margin-top: 20px;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
            width: 100%;
        }
        button:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($_SESSION['step'] == 0): ?>
            <h2>Registrasi</h2>
            <form method="POST">
                <label>Email:</label>
                <input type="email" name="email" required>
                <button type="submit" name="send_code">Kirim Kode</button>
            </form>
        <?php elseif ($_SESSION['step'] == 1): ?>
            <h2>Masukkan Kode Verifikasi</h2>
            <form method="POST">
                <label>Kode:</label>
                <input type="text" name="verification_code" required>
                <button type="submit" name="verify_code">Verifikasi</button>
            </form>
        <?php elseif ($_SESSION['step'] == 2): ?>
            <h2>Lengkapi Data</h2>
            <form method="POST">
                <label>Username:</label>
                <input type="text" name="username" required>
                <label>No HP:</label>
                <input type="text" name="phone" required>
                <label>Alamat:</label>
                <textarea name="address" required></textarea>
                <label>Password:</label>
                <input type="password" name="password" required>
                <button type="submit" name="register">Simpan</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
