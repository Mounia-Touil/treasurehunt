<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);


if (!isset($_SESSION['userid'])) {
    echo "<script>
        alert('You must be logged in to sell an item.');
        window.location.href = 'Register.html';
    </script>";
    exit();
}
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "treasurehunt";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$itemName      = trim($_POST['itemName'] ?? '');
$categoryID    = intval($_POST['category'] ?? 0);
$startingPrice = floatval($_POST['price'] ?? 0);
$description   = trim($_POST['description'] ?? '');
$duration      = intval($_POST['duration'] ?? 0);
$userID        = $_SESSION['userid'];

if (empty($itemName) || $categoryID === 0 || $startingPrice <= 0 || $duration <= 0 || empty($description)) {
    echo "<script>alert('⚠️ Please fill all fields with valid data.'); window.history.back();</script>";
    exit();
}
date_default_timezone_set('America/New_York');
$startTime = date("Y-m-d H:i:s");
$endTime   = date("Y-m-d H:i:s", strtotime("+$duration hours"));

$imagePath = "";
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = "uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName  = basename($_FILES["image"]["name"]);
    $safeName  = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", $fileName);
    $imagePath = $uploadDir . $safeName;

    if (!move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath)) {
        echo "<script>alert('⚠️ Image upload failed. Please try again.'); window.history.back();</script>";
        exit();
    }
}

$sql = "INSERT INTO Items 
        (Name, Description, ImageURL, CategoryID, UserID, StartTime, EndTime, StartingPrice) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("sssisssd", $itemName, $description, $imagePath, $categoryID, $userID, $startTime, $endTime, $startingPrice);

if ($stmt->execute()) {
    echo "<script>
        alert('🎉 Your item \"$itemName\" has been listed successfully for $duration hour(s)!');
        window.location.href = 'ViewItem.php';
    </script>";
} else {
    http_response_code(500);
    echo " SQL Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
