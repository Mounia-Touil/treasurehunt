<?php
session_start();

// Connect to DB
$conn = new mysqli("localhost", "root", "", "treasurehunt");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Get admin credentials
$adminID = $_POST['adminID'] ?? '';
$password = $_POST['password'] ?? '';

// Validate against Reports table
$sql = "SELECT * FROM Reports WHERE AdminID = ? AND Password = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $adminID, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $_SESSION['admin'] = $adminID;
    header("Location: ../AdminPanel.html"); // Redirect after login
    exit();
} else {
    echo "<script>
        alert('Invalid Admin ID or Password.');
        window.location.href = '../Admin.html';
    </script>";
}

$conn->close();
?>
