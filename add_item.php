<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require 'db.php';

if (!isset($_SESSION['userid'])) {
    echo "<script>
        alert(' You must be logged in to add an item.');
        window.location.href = 'login.html';
    </script>";
    exit;
}

$userid = $_SESSION['userid'];

$stmt = $conn->query("SELECT CategoryID, CategoryName, ParentCategoryID FROM Categories ORDER BY ParentCategoryID ASC, CategoryName ASC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

function buildCategoryTree($categories) {
    $tree = [];
    foreach ($categories as $category) {
        $tree[$category['ParentCategoryID']][] = $category;
    }
    return $tree;
}

$categoryTree = buildCategoryTree($categories);

function renderCategoryOptions($tree, $parentId = null, $prefix = '') {
    if (!isset($tree[$parentId])) return;
    foreach ($tree[$parentId] as $cat) {
        echo "<option value='{$cat['CategoryID']}'>{$prefix}{$cat['CategoryName']}</option>";
        renderCategoryOptions($tree, $cat['CategoryID'], $prefix . '— ');
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Item - TreasureHunt</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <img src="treasure-hunter.png" alt="Banner" class="banner">
    <div class="header-content">
        <div class="header-left">
            <img src="Logo.png" alt="Logo" class="logo">
            <h1>➕ Add New Item</h1>
        </div>
    </div>
</header>

<main class="main-content">
    <form action="process_add_item.php" method="POST" enctype="multipart/form-data">
        <label for="itemName">Item Name:</label>
        <input type="text" id="itemName" name="itemName" required><br><br>

        <label for="description">Description:</label>
        <textarea id="description" name="description" required></textarea><br><br>

        <label for="category">Category:</label>
        <select id="category" name="category" required>
            <option value="">--Select Category--</option>
            <?php renderCategoryOptions($categoryTree); ?>
        </select><br><br>

        <label for="startingPrice">Starting Price ($):</label>
        <input type="number" id="startingPrice" name="startingPrice" step="0.01" required><br><br>

        <label for="endTime">Auction End Time:</label>
        <input type="datetime-local" id="endTime" name="endTime" required><br><br>

        <label for="image">Upload Image:</label>
        <input type="file" id="image" name="image" accept="image/*" required><br><br>

        <button type="submit" class="nav-button">List Item for Auction</button>
    </form>
</main>

<footer>
    <p>&copy; 2025 TreasureHunt | <a href="Contact.html"> Contact</a> | <a href="Help.html"> Help</a></p>
</footer>

</body>
</html>
