<?php
session_start();

$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "treasurehunt";

if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

if ($_SESSION['login_attempts'] >= 3) {
    echo "<script>
        alert(' Too many failed login attempts. Redirecting to Home page.');
        window.location.href = 'TreasureHunt.php';  //  fixed spelling
    </script>";
    session_destroy();
    exit();
}

$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);
if ($conn->connect_error) {
    die(" Connection failed: " . $conn->connect_error);
}

$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$pass  = $_POST['password'] ?? '';

if (empty($email) || empty($pass)) {
    echo "<script>
        alert(' Email and password are required.');
        window.location.href = 'login.html';
    </script>";
    exit();
}
$stmt = $conn->prepare("SELECT * FROM Users WHERE Email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();

    if (password_verify($pass, $user['Password'])) {
        
        $_SESSION['user']     = $user['Username'];
        $_SESSION['userid']   = $user['UserID'];
        $_SESSION['isAdmin']  = $user['IsAdmin'];
        unset($_SESSION['login_attempts']);

        echo "<script>
            alert('Welcome back, " . addslashes($user['FullName']) . "!');
            window.location.href = 'UserDashboard.php';
        </script>";
        exit();
    } else {
       
        $_SESSION['login_attempts']++;
        echo "<script>
            alert(' Incorrect password. Attempt {$_SESSION['login_attempts']} of 3');
            window.location.href = 'login.html';
        </script>";
        exit();
    }
} else {
    
    $_SESSION['login_attempts']++;
    echo "<script>
        alert(' Email not found. Attempt {$_SESSION['login_attempts']} of 3');
        window.location.href = 'login.html';
    </script>";
    exit();
}

$stmt->close();
$conn->close();
?>
