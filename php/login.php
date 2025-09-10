<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "treasurehunt";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$email = $_POST['email'];
$pass = $_POST['password'];

// Simple check (no hashing)
$sql = "SELECT * FROM users WHERE email='$email' AND password='$pass'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    // Store user details in session
    $_SESSION['email'] = $user['email'];
    $_SESSION['userid'] = $user['UserID']; // This is important for placing bids

    header("Location: ../Index.html");
    exit();
} else {
    echo "<script>
            alert('Invalid email or password');
            window.location.href = '../login.html';
          </script>";
}

$conn->close();
?>
