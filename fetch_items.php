<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

$mysqli = new mysqli("localhost", "root", "", "treasurehunt");

if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Connection failed: " . $mysqli->connect_error]);
    exit;
}

$sql = "SELECT 
            Items.ItemID, 
            Items.Name, 
            Items.Description, 
            Items.StartingPrice, 
            Items.imageurl AS ImagePath, 
            Items.EndTime,
            Categories.CategoryName 
        FROM Items 
        JOIN Categories ON Items.CategoryID = Categories.CategoryID
        WHERE Items.Status = 'Active'
        ORDER BY Items.EndTime ASC";

$result = $mysqli->query($sql);

$items = [];

while ($row = $result->fetch_assoc()) {
    $items[] = [
        "ItemID" => $row["ItemID"],
        "Name" => $row["Name"],
        "Description" => $row["Description"],
        "CategoryName" => $row["CategoryName"],
        "StartingPrice" => $row["StartingPrice"],
        "ImagePath" => $row["ImagePath"],
        "EndTime" => $row["EndTime"] 
    ];
}
echo json_encode($items);
?>
