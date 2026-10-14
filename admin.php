<?php
session_start();

$conn = new mysqli("localhost", "root", "", "treasurehunt");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$adminID = $_POST['adminID'] ?? '';
$password = $_POST['password'] ?? '';


$sql = "SELECT * FROM Admins WHERE AdminID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $adminID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    if (password_verify($password, $row['Password'])) {
        $_SESSION['admin'] = $adminID;
        $_SESSION['is_admin'] = true; 
        header("Location: AdminPanel.php");
        exit();
    } else {
        echo "<script>
            alert(' Incorrect password.');
            window.location.href = 'Admin.html';
        </script>";
    }
} else {
    echo "<script>
        alert(' Invalid Admin ID.');
        window.location.href = 'Admin.html';
    </script>";
}

$conn->close();
?>
