<?php
session_start();
require 'db.php';

if (!isset($_SESSION['userid'])) {
    header("Location: login.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];

    $stmt = $conn->prepare("SELECT Password FROM Users WHERE UserID = ?");
    $stmt->execute([$_SESSION['userid']]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row || !password_verify($current, $row['Password'])) {
        echo "<script>alert(' Current password incorrect.'); window.history.back();</script>";
        exit();
    }

    $hashed = password_hash($new, PASSWORD_DEFAULT);
    $update = $conn->prepare("UPDATE Users SET Password = ? WHERE UserID = ?");
    $update->execute([$hashed, $_SESSION['userid']]);

    echo "<script>alert('Password changed successfully.'); window.location.href='UserDashboard.php';</script>";
    exit();
}
?>

<form method="POST">
  <h2>Change Password</h2>
  <label>Current Password:</label><br>
  <input type="password" name="current_password" required><br><br>
  <label>New Password:</label><br>
  <input type="password" name="new_password" required><br><br>
  <button type="submit">Change Password</button>
</form>
