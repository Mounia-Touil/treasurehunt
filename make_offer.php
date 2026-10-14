<?php
session_start();
require 'db.php';

if (!isset($_SESSION['userid'])) {
    echo "<script>alert('You must be logged in to make an offer.'); window.location.href='login.html';</script>";
    exit();
}
$userID = $_SESSION['userid'];
$itemID = $_POST['itemID'] ?? null;
$offerAmount = $_POST['offerAmount'] ?? null;

if (!$itemID || !$offerAmount || !is_numeric($offerAmount)) {
    echo "<script>alert('Invalid offer.'); window.history.back();</script>";
    exit();
}

$stmt = $conn->prepare("INSERT INTO Offers (UserID, ItemID, OfferAmount) VALUES (?, ?, ?)");
$stmt->execute([$userID, $itemID, $offerAmount]);

echo "<script>alert(' Offer submitted! Seller will review.'); window.location.href='ViewItem.php';</script>";
