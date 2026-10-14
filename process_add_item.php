<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['itemName']);
    $description = trim($_POST['description']);
    $categoryID = intval($_POST['category']);
    $startingPrice = floatval($_POST['startingPrice']);
    $endTime = $_POST['endTime'];
    $userID = $_SESSION['user_id'];
    $status = 'Active';

    $leafCheck = $conn->prepare("SELECT COUNT(*) FROM Categories WHERE ParentCategoryID = ?");
    $leafCheck->execute([$categoryID]);
    $childCount = $leafCheck->fetchColumn();

    if ($childCount > 0) {
        echo "<p style='color:red;'> Please select a more specific (sub)category. Parent categories like 'Electronics' are not allowed.</p>";
        echo "<a href='add_item.php'>Back to Add Item</a>";
        exit;
    }

    $imageURL = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755);
        $filename = time() . '_' . basename($_FILES['image']['name']);
        $imageURL = $uploadDir . $filename;
        move_uploaded_file($_FILES['image']['tmp_name'], $imageURL);
    }

    $stmt = $conn->prepare("INSERT INTO Items (Name, Description, ImageURL, CategoryID, UserID, StartTime, EndTime, StartingPrice, Status) 
                            VALUES (?, ?, ?, ?, ?, NOW(), ?, ?, ?)");
    $stmt->execute([$name, $description, $imageURL, $categoryID, $userID, $endTime, $startingPrice, $status]);

    echo "<p style='color:green;'> Item successfully added! Redirecting...</p>";
    echo "<script>setTimeout(() => window.location.href = 'ViewItems.php', 2000);</script>";
    exit;
}
?>
