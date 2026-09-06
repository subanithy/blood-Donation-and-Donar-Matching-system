<?php
session_start();
include 'db.php';

$message = '';
$alert_class = 'alert-danger';

if (isset($_GET['msg']) && $_GET['msg'] === 'verified') {
    $message = "OTP verification successful! You can now log in.";
    $alert_class = 'alert-success';
}

if (isset($_POST['login'])) {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, full_name, email, phone, blood_group, status, is_verified, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if ($user['is_verified'] == 0) {
            $message = "Your account is not verified yet! Please complete OTP verification.";
            $alert_class = 'alert-danger';
        } elseif (password_verify($password, $user['password'])) {
            unset($user['password']);
            $_SESSION['user'] = $user;
            header("Location: home.php");
            exit();
        } else {
            $message = "Invalid Password!";
            $alert_class = 'alert-danger';
        }
    } else {
        $message = "No account found with this email!";
        $alert_class = 'alert-danger';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Donor Login</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; margin: 0; padding: 0; }
        .container { max-width: 400px; margin: 50px auto; background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        h3 { text-align: center; color: #dc3545; margin-bottom: 20px; }
        .alert { padding: 10px; border-radius: 4px; text-align: center; margin-bottom: 15px; }
        .alert-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .btn { width: 100%; padding: 10px; background-color: #dc3545; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 16px; }
        .footer-text { text-align: center; margin-top: 15px; }
        .footer-text a { color: #dc3545; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <h3>Donor Login</h3>

    <?php if ($message): ?>
        <div class="alert <?= $alert_class ?>"><?= $message ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <div class="form-group">
            <label>Email Address:</label>
            <input type="email" name="email" required placeholder="john@example.com">
        </div>
        <div class="form-group">
            <label>Password:</label>
            <input type="password" name="password" required placeholder="••••••••">
        </div>
        <button type="submit" name="login" class="btn">Login</button>
    </form>
    <p class="footer-text">Don't have an account? <a href="register.php">Register Here</a></p>
</div>

</body>
</html>