<?php
session_start();
include 'db.php';

if (!isset($_SESSION['temp_email'])) {
    header("Location: register.php");
    exit();
}

$message = '';
$email = $_SESSION['temp_email'];
$demo_otp = $_SESSION['temp_otp'] ?? ''; 

if (isset($_POST['verify_otp'])) {
    $user_otp = trim($_POST['otp']);

    $stmt = $conn->prepare("SELECT id, otp FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if ($row['otp'] === $user_otp) {
            $update = $conn->prepare("UPDATE users SET is_verified = 1, otp = NULL WHERE email = ?");
            $update->bind_param("s", $email);
            $update->execute();

            unset($_SESSION['temp_email']);
            unset($_SESSION['temp_otp']);

            echo "<script>
                alert('Verification Successful! Please log in.');
                window.location.href='login.php?msg=verified';
            </script>";
            exit();
        } else {
            $message = "Invalid OTP! Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>OTP Verification</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; margin: 0; padding: 0; }
        .container { max-width: 400px; margin: 50px auto; background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); text-align: center; }
        h4 { color: #dc3545; margin-bottom: 15px; }
        .alert { padding: 10px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 4px; margin-bottom: 15px; }
        .info-box { padding: 10px; background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; border-radius: 4px; margin-bottom: 15px; }
        input[type="text"] { width: 100%; padding: 12px; font-size: 20px; text-align: center; letter-spacing: 5px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; margin-bottom: 15px; }
        .btn { width: 100%; padding: 10px; background-color: #dc3545; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 16px; }
    </style>
</head>
<body>

<div class="container">
    <h4>OTP Verification</h4>
    <div class="info-box">
        <small>Demo OTP Code: <strong><?= $demo_otp ?></strong></small>
    </div>

    <?php if ($message): ?>
        <div class="alert"><?= $message ?></div>
    <?php endif; ?>

    <form action="verify.php" method="POST">
        <input type="text" name="otp" maxlength="6" required placeholder="123456">
        <button type="submit" name="verify_otp" class="btn">Verify OTP</button>
    </form>
</div>

</body>
</html>