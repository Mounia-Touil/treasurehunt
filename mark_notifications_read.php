<?php
session_start();
require 'db.php';

if (isset($_SESSION['userid'])) {
    $stmt = $conn->prepare("UPDATE Notifications SET IsRead = 1 WHERE UserID = ?");
    $stmt->execute([$_SESSION['userid']]);
}
header("Location: UserDashboard.php");
exit();
