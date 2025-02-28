<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$database = "libcendana2220";
$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['email'] = $email;
            header("Location: index.html");
            exit();
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "not_registered";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Login</title>
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
            border-radius: 0px;
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
        input {
            width: calc(100% - 30px);
            padding: 12px;
            margin-top: 5px;
            border: 2px solid #007bff;
            border-radius: 0px;
            font-size: 16px;
            box-shadow: inset 0px 0px 5px rgba(0, 0, 0, 0.1);
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 0px;
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
        .register-link {
            margin-top: 15px;
            color: #007bff;
            cursor: pointer;
            font-weight: bold;
        }
        .register-link:hover {
            text-decoration: underline;
        }
        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.3);
            text-align: center;
            border: 2px solid #007bff;
        }
        .popup button {
            width: auto;
            margin: 10px;
            padding: 10px 20px;
            cursor: pointer;
        }
    </style>
    <script>
        function showPopup() {
            document.getElementById("popup").style.display = "block";
        }

        function closePopup() {
            document.getElementById("popup").style.display = "none";
        }

        function redirectToRegister() {
            window.location.href = "register.php";
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <form method="POST">
            <label>Email:</label>
            <input type="email" name="email" required>
            <label>Password:</label>
            <input type="password" name="password" required>
            <button type="submit" name="login">Login</button>
        </form>
        <p class="register-link" onclick="redirectToRegister()">Register</p>
    </div>

    <?php if (isset($error) && $error == "not_registered"): ?>
        <div class="popup" id="popup">
            <p>Your account is not registered yet, create a new account?</p>
            <button onclick="redirectToRegister()">OK</button>
            <button onclick="closePopup()">NO</button>
        </div>
        <script>showPopup();</script>
    <?php endif; ?>
</body>
</html>
