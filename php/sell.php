<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// DB connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "treasurehunt";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Get form data
$itemName = $_POST['itemName'];
$categoryID = intval($_POST['category']);
$startingPrice = floatval($_POST['price']);
$description = $_POST['description'];
$duration = intval($_POST['duration']);

// Assume seller ID = 1 (replace with session value later)
$sellerID = 1;

// Auction start and end time
date_default_timezone_set('America/New_York');
$startTime = date("Y-m-d H:i:s");
$endTime = date("Y-m-d H:i:s", strtotime("+$duration hours"));

// Image upload
$imagePath = "";
if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $uploadDir = "../uploads/";
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    $imagePath = $uploadDir . basename($_FILES["image"]["name"]);
    move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath);
}

// Insert into DB
$sql = "INSERT INTO Items (Name, Description, ImageURL, CategoryID, SellerID, StartTime, EndTime, StartingPrice) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("sssisssd", $itemName, $description, $imagePath, $categoryID, $sellerID, $startTime, $endTime, $startingPrice);

if ($stmt->execute()) {
    echo "<script>
            alert('Item listed successfully for $duration hours!');
            window.location.href = '../ViewItem.html';
          </script>";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
