<?php
session_start();
require 'db.php';

if (!isset($_SESSION['userid'])) {
    echo "<script>alert('Please login to add items to your watchlist.'); window.location.href='login.html';</script>";
    exit();
}

$userID = $_SESSION['userid'];
$itemID = $_POST['itemID'] ?? null;

if (!$itemID || !is_numeric($itemID)) {
    echo "<script>alert('Invalid item.'); window.history.back();</script>";
    exit();
}

try {
    $stmt = $conn->prepare("INSERT IGNORE INTO Watchlist (UserID, ItemID) VALUES (?, ?)");
    $stmt->execute([$userID, $itemID]);
    echo "<script>alert(' Item added to your watchlist!'); window.location.href='ViewItem.php';</script>";
} catch (PDOException $e) {
    echo "<script>alert(' Could not add to watchlist.'); window.history.back();</script>";
}
