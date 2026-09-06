<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['toggle_status'])) {
    $new_status = $_POST['status'];
    $user_id = $_SESSION['user']['id'];

    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $user_id);
    if ($stmt->execute()) {
        $_SESSION['user']['status'] = $new_status;
    }
}

$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Donor Home Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; margin: 0; padding: 0; }
        .navbar { background-color: #dc3545; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar h2 { margin: 0; }
        .logout-btn { color: white; text-decoration: none; border: 1px solid white; padding: 6px 12px; border-radius: 4px; font-weight: bold; }
        .logout-btn:hover { background-color: white; color: #dc3545; }
        .container { max-width: 600px; margin: 40px auto; background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); text-align: center; }
        .blood-badge { background-color: #dc3545; color: white; font-size: 24px; font-weight: bold; padding: 8px 18px; border-radius: 50px; display: inline-block; margin-bottom: 10px; }
        .status-available { color: #28a745; font-weight: bold; }
        .status-unavailable { color: #dc3545; font-weight: bold; }
        .toggle-box { background-color: #e9ecef; padding: 15px; border-radius: 6px; margin-top: 20px; }
        .btn-status { padding: 8px 15px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; color: white; margin: 5px; }
        .btn-avail { background-color: #28a745; }
        .btn-unavail { background-color: #6c757d; }
    </style>
</head>
<body>

<div class="navbar">
    <h2>Blood Donation System</h2>
    <a href="logout.php" class="logout-btn">Logout</a>
</div>

<div class="container">
    <div class="blood-badge"><?= htmlspecialchars($user['blood_group']) ?></div>
    <h2>Welcome, <?= htmlspecialchars($user['full_name']) ?></h2>
    <p style="color: #6c757d;"><?= htmlspecialchars($user['email']) ?> | <?= htmlspecialchars($user['phone']) ?></p>
    <hr>
    
    <p>Current Availability Status: 
        <span class="<?= $user['status'] == 'Available' ? 'status-available' : 'status-unavailable' ?>">
            <?= htmlspecialchars($user['status']) ?>
        </span>
    </p>

    <div class="toggle-box">
        <p style="margin-top: 0; font-weight: bold;">Change Availability Status:</p>
        <form action="home.php" method="POST" style="display: inline;">
            <input type="hidden" name="status" value="Available">
            <button type="submit" name="toggle_status" class="btn-status btn-avail">Set Available</button>
        </form>
        <form action="home.php" method="POST" style="display: inline;">
            <input type="hidden" name="status" value="Unavailable">
            <button type="submit" name="toggle_status" class="btn-status btn-unavail">Set Unavailable</button>
        </form>
    </div>
</div>

</body>
</html>