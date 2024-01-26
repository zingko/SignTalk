<?php
session_start(); // Start the session

if (isset($_POST["submit"])) {
    $userName = $_POST["userName"];
    $userEmail = $_POST["userEmail"];
    $userPassword = $_POST["userPassword"];
    $Rpassword = $_POST["Rpassword"];

    $passwordHash = password_hash($userPassword, PASSWORD_DEFAULT);

    $errors = array();

    if (empty($userName) || empty($userEmail) || empty($userPassword) || empty($Rpassword)) {
        array_push($errors, "All fields are required");
    }
    if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
        array_push($errors, "Email is not valid");
    }
    if (strlen($userPassword) < 8) {
        array_push($errors, "Password must be at least 8 characters long");
    }
    if ($userPassword !== $Rpassword) {
        array_push($errors, "Password does not match");
    }

    require_once "dbConn.php";
    $sql = "SELECT * FROM user WHERE userEmail = '$userEmail'";
    $result = mysqli_query($conn, $sql);
    $rowCount = mysqli_num_rows($result);

    if ($rowCount > 0) {
        array_push($errors, "Email already exists!");
    }

    if (count($errors) > 0) {
        // Store the errors in the session variable
        $_SESSION["registration_errors"] = $errors;
    } else {
        // Registration was successful
        $sql = "INSERT INTO user (username, userEmail, password) VALUES (?, ?, ?)";
        $stmt = mysqli_stmt_init($conn);
        $prepareStmt = mysqli_stmt_prepare($stmt, $sql);

        if ($prepareStmt) {
            mysqli_stmt_bind_param($stmt, "sss", $userName, $userEmail, $passwordHash);
            mysqli_stmt_execute($stmt);

            // Check for errors
            if (mysqli_stmt_error($stmt)) {
                die("Error executing statement: " . mysqli_stmt_error($stmt));
            }

            // Set a success message in the session variable
            $_SESSION["registration_success"] = "You are registered successfully";
        } else {
            die("Something went wrong with statement preparation");
        }
    }
}

// Always redirect back to the register.php page
header("Location: register.php");
exit;
?>
