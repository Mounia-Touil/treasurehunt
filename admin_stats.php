<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "treasurehunt");
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

$res1 = $conn->query("SELECT COUNT(*) AS total_users FROM Users");
$users = ($res1 && $res1->num_rows > 0) ? (int)$res1->fetch_assoc()['total_users'] : 0;

$res2 = $conn->query("SELECT COUNT(*) AS items_on_sale FROM Items WHERE EndTime > NOW()");
$items = ($res2 && $res2->num_rows > 0) ? (int)$res2->fetch_assoc()['items_on_sale'] : 0;

$res3 = $conn->query("SELECT COUNT(*) AS total_bids FROM Bids");
$bids = ($res3 && $res3->num_rows > 0) ? (int)$res3->fetch_assoc()['total_bids'] : 0;

$res4 = $conn->query("SELECT COUNT(*) AS bids_today FROM Bids WHERE DATE(BidTime) = CURDATE()");
$bidsToday = ($res4 && $res4->num_rows > 0) ? (int)$res4->fetch_assoc()['bids_today'] : 0;

$res5 = $conn->query("SELECT COUNT(*) AS users_today FROM Users WHERE DATE(RegistrationDate) = CURDATE()");
$usersToday = ($res5 && $res5->num_rows > 0) ? (int)$res5->fetch_assoc()['users_today'] : 0;

$res6 = $conn->query("SELECT COUNT(*) AS expired_items FROM Items WHERE Status = 'Expired'");
$expired = ($res6 && $res6->num_rows > 0) ? (int)$res6->fetch_assoc()['expired_items'] : 0;

// Output as JSON
echo json_encode([
    'users' => $users,
    'items' => $items,
    'bids' => $bids,
    'bids_today' => $bidsToday,
    'users_today' => $usersToday,
    'expired_items' => $expired
]);

$conn->close();
?>
