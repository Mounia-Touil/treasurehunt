<?php
session_start();
require 'db.php'; 

if (!isset($_SESSION['userid'])) {
    echo "<script>alert(' You must be logged in to place a bid.'); window.location.href = 'login.html';</script>";
    exit();
}

$userID     = $_SESSION['userid'];
$itemID     = $_POST['itemID'] ?? '';
$bidAmount  = $_POST['bidAmount'] ?? '';
$fullName   = trim($_POST['fullName'] ?? '');
$address    = trim($_POST['shippingAddress'] ?? '');
$saveAddr   = isset($_POST['saveAddress']); 


if (!$itemID || !$bidAmount || !$fullName || !$address || !is_numeric($bidAmount)) {
    echo "<script>alert('⚠️ Please fill all fields correctly.'); history.back();</script>";
    exit();
}


$stmt = $conn->prepare("SELECT StartingPrice, EndTime FROM Items WHERE ItemID = ?");
$stmt->execute([$itemID]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    echo "<script>alert(' Item not found.'); history.back();</script>";
    exit();
}

if (strtotime($item['EndTime']) < time()) {
    echo "<script>alert('⚠️ Sorry, the auction has already ended.'); history.back();</script>";
    exit();
}

$stmt = $conn->prepare("SELECT MAX(BidAmount) FROM Bids WHERE ItemID = ?");
$stmt->execute([$itemID]);
$currentHigh = (float)($stmt->fetchColumn() ?? 0);

$minBid = max($item['StartingPrice'], $currentHigh);
if ($bidAmount <= $minBid) {
    echo "<script>alert('⚠️ Your bid must be greater than $" . number_format($minBid, 2) . "'); history.back();</script>";
    exit();
}

$insert = $conn->prepare("INSERT INTO Bids (UserID, ItemID, BidAmount, BidTime) VALUES (?, ?, ?, NOW())");
$insert->execute([$userID, $itemID, $bidAmount]);


if ($saveAddr) {
    $update = $conn->prepare("UPDATE Users SET FullName = ?, ShippingAddress = ? WHERE UserID = ?");
    $update->execute([$fullName, $address, $userID]);
}


echo "<script>alert(' Your bid of $$bidAmount has been placed successfully!'); window.location.href = 'ViewItem.php';</script>";
exit();
?>
