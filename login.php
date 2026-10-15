<?php
session_start();
include('db.php');

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // የተጠቃሚውን መረጃ መፈለግ
    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Session መመዝገብ
        $_SESSION['username'] = $row['username'];
        $_SESSION['role'] = trim($row['role']);

        // ወደ ተገቢው ገጽ መምራት
        if ($_SESSION['role'] === 'admin') {
            header("Location: admin_dashboard.php");
            exit();
        } else if ($_SESSION['role'] === 'student') {
            header("Location: student_dashboard.php");
            exit();
        } else {
            $error = "ያልታወቀ የተጠቃሚ ሚና (Role)!";
        }
    } else {
        $error = "የተሳሳተ Username ወይም Password!";
    }
}
?>

<!DOCTYPE html>
<html lang="am">
<head>
    <meta charset="UTF-8">
    <title>Login - Student Result System</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f0f2f5; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-card { background: white; padding: 35px; border-radius: 12px; box-shadow: 0 8px 20px rgba(0,0,0,0.1); width: 320px; }
        .login-card h2 { text-align: center; margin-bottom: 25px; color: #1e3c72; font-size: 22px; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; margin-bottom: 6px; font-weight: 600; color: #555; font-size: 14px; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; outline: none; }
        .form-group input:focus { border-color: #2a5298; }
        .btn { width: 100%; background: linear-gradient(135deg, #1e3c72, #2a5298); color: white; border: none; padding: 12px; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 15px; }
        .btn:hover { opacity: 0.95; }
        .error { color: #d9534f; font-size: 14px; text-align: center; margin-bottom: 15px; font-weight: bold; }
    </style>
</head>
<body>

<div class="login-card">
    <h2>ሲስተም መግቢያ</h2>
    <?php if ($error != "") { echo "<p class='error'>$error</p>"; } ?>
    <form method="POST" action="">
        <div class="form-group">
            <label>Username / Student ID</label>
            <input type="text" name="username" placeholder="Username ያስገቡ..." required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="Password ያስገቡ..." required>
        </div>
        <button type="submit" class="btn">Login</button>
    </form>
</div>

</body>
</html>