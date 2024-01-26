<?php
if (isset($_POST["login"])) {
    $userEmail = $_POST["userEmail"];
    $userPassword = $_POST["userPassword"];
    require_once "dbConn.php";
    $sql = "SELECT * FROM user WHERE userEmail = '$userEmail'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_array($result, MYSQLI_ASSOC);

    session_start();

    // Initialize error message variables
    $emailError = "";
    $passwordError = "";

    if ($user) {
        if (password_verify($userPassword, $user["password"])) {
            $_SESSION['userID'] = $user['userID'];
            $_SESSION["user"] = $user;
            $_SESSION['userType'] = $user['userType']; // Set the user type in the session
            $_SESSION['username'] = $user['username'];
        
            if ($user['userType'] == 'A') {
                header("Location: adminPro.php");
                die();
            } elseif ($user['userType'] == 'U') {
                header("Location: index.php");
                die();
            }
        } else {
            // Password does not match
            $passwordError = "Password does not match";
        }
    } else {
        // Email does not match
        $emailError = "Email does not match";
    }

    // Check for both email and password errors here
    if (!empty($emailError) && !empty($passwordError)) {
        $_SESSION["login_error"] = "$emailError<br>$passwordError";
    } elseif (!empty($emailError)) {
        $_SESSION["login_error"] = $emailError;
    } elseif (!empty($passwordError)) {
        $_SESSION["login_error"] = $passwordError;
    }

    header("Location: login.php");
    die();
}
?>
