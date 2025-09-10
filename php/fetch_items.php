<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

// Connect to the database
$mysqli = new mysqli("localhost", "root", "", "treasurehunt");

if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Connection failed: " . $mysqli->connect_error]);
    exit;
}

// Fetch items with category and image URL
$sql = "SELECT 
            Items.ItemID, 
            Items.Name, 
            Items.Description, 
            Items.StartingPrice, 
            Items.imageurl AS ImagePath, 
            Categories.CategoryName 
        FROM Items 
        JOIN Categories ON Items.CategoryID = Categories.CategoryID";

$result = $mysqli->query($sql);

$items = [];

while ($row = $result->fetch_assoc()) {
    $items[] = [
        "ItemID" => $row["ItemID"],
        "Name" => $row["Name"],
        "Description" => $row["Description"],
        "CategoryName" => $row["CategoryName"],
        "StartingPrice" => $row["StartingPrice"],
        "ImagePath" => $row["ImagePath"] // mapped from imageurl
    ];
}

echo json_encode($items);
?>
