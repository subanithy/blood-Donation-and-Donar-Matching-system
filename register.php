<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'db.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $full_name    = trim($_POST['full_name']);
    $gender       = trim($_POST['gender']);
    $dob          = trim($_POST['dob']);
    $age          = intval($_POST['age']);
    $address      = trim($_POST['address']);
    $email        = trim($_POST['email']);
    $phone        = trim($_POST['phone']);
    $nic_passport = trim($_POST['nic_passport']);
    $blood_group  = trim($_POST['blood_group']);
    $password     = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    $otp = rand(100000, 999999);

    $stmt = $conn->prepare("INSERT INTO users (full_name, gender, dob, age, address, email, phone, nic_passport, blood_group, password, otp, is_verified) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)");
    
    if ($stmt === false) {
        die("Database Error: " . $conn->error);
    }

    $stmt->bind_param("sssisssssss", $full_name, $gender, $dob, $age, $address, $email, $phone, $nic_passport, $blood_group, $password, $otp);

    if ($stmt->execute()) {
        $_SESSION['temp_email'] = $email;
        $_SESSION['temp_otp'] = $otp; 
        
        echo "<script>
            alert('Registration Successful! Redirecting to OTP Verification page.');
            window.location.href='verify.php';
        </script>";
        exit();
    } else {
        if ($conn->errno === 1062) {
            $message = "This email address is already registered!";
        } else {
            $message = "Error: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Donor Registration</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #ffe6ea; margin: 0; padding: 0; }
        .container { max-width: 500px; margin: 30px auto; background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        h3 { text-align: center; color: #dc3545; margin-bottom: 20px; }
        .alert { padding: 10px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 4px; text-align: center; margin-bottom: 15px; }
        .form-group { margin-bottom: 12px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select, textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .btn { width: 100%; padding: 12px; background-color: #dc3545; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 16px; margin-top: 10px; }
        .btn:hover { background-color: #c82333; }
        .footer-text { text-align: center; margin-top: 15px; }
        .footer-text a { color: #dc3545; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <h3>Donor Registration</h3>
    
    <?php if ($message): ?>
        <div class="alert"><?= $message ?></div>
    <?php endif; ?>

    <form action="register.php" method="POST">
        <div class="form-group">
            <label>Full Name:</label>
            <input type="text" name="full_name" required placeholder="John Doe">
        </div>

        <div class="form-group">
            <label>Gender:</label>
            <select name="gender" required>
                <option value="">Select Gender...</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
        </div>

        <div class="form-group">
            <label>Date of Birth:</label>
            <input type="date" name="dob" required>
        </div>

        <div class="form-group">
            <label>Age:</label>
            <input type="number" name="age" min="18" max="65" required placeholder="e.g. 25">
        </div>

        <div class="form-group">
            <label>Address:</label>
            <textarea name="address" rows="3" required placeholder="Enter full address"></textarea>
        </div>

        <div class="form-group">
            <label>Email Address:</label>
            <input type="email" name="email" required placeholder="john@example.com">
        </div>

        <div class="form-group">
            <label>Phone Number:</label>
            <input type="tel" name="phone" required placeholder="+94771234567">
        </div>

        <div class="form-group">
            <label>NIC / Passport Number:</label>
            <input type="text" name="nic_passport" required placeholder="199012345678">
        </div>

        <div class="form-group">
            <label>Blood Group:</label>
            <select name="blood_group" required>
                <option value="">Select Blood Group...</option>
                <option value="A+">A+</option><option value="A-">A-</option>
                <option value="B+">B+</option><option value="B-">B-</option>
                <option value="O+">O+</option><option value="O-">O-</option>
                <option value="AB+">AB+</option><option value="AB-">AB-</option>
            </select>
        </div>

        <div class="form-group">
            <label>Password:</label>
            <input type="password" name="password" required placeholder="••••••••">
        </div>

        <button type="submit" name="register" class="btn">Register</button>
    </form>
    <p class="footer-text">Already have an account? <a href="login.php">Login Here</a></p>
</div>

</body>
</html>