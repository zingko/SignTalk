<?php
session_start();

require "dbConn.php";

if (isset($_POST['editUser'])) {
    // Edit operation
    $userID = $_POST['userID'];
    $username = $_POST['username'];
    $userEmail = $_POST['userEmail'];

    $sql = "UPDATE `user` SET `username`='$username', `userEmail`='$userEmail' WHERE userID = $userID";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        $_SESSION['success_message'] = "Records added successfully.";
            header("Location: manageUserlists.php");
            exit();
    } else {
        $_SESSION['error_message'] = "Failed to add user.";
        header("Location: manageUserlists.php");
        exit();       }
} // Check if the delete button for multiple records is clicked
elseif (isset($_POST['delSelected'])) {
    // Get the selected user IDs to be deleted
    $selectedUserIDs = isset($_POST['options']) ? $_POST['options'] : [];

    if (!empty($selectedUserIDs)) {
        // Convert array values to a comma-separated string for the SQL query
        $selectedUserIDsString = implode(',', $selectedUserIDs);

        // Execute the delete query for selected records
        $sqlDeleteSelected = "DELETE FROM `user` WHERE userID IN ($selectedUserIDsString)";
        $resultDeleteSelected = mysqli_query($conn, $sqlDeleteSelected);

        if ($resultDeleteSelected) {
            $_SESSION['success_message'] = "Records deleted successfully.";
            header("Location: manageUserlists.php");
            exit();
        } else {
            echo "Failed to delete selected users: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['error_message'] = "Error: No records selected for deletion.";
        header("Location: manageUserlists.php");
        exit();    
        
    }
}
elseif (isset($_POST['delUser'])) {
    // Get the user ID to be deleted
    $deleteUserID = isset($_POST['deleteUserID']) ? $_POST['deleteUserID'] : null;

    if ($deleteUserID) {
        // Execute the delete query
        $sqlDeleteUser = "DELETE FROM `user` WHERE userID = $deleteUserID";
        $resultDeleteUser = mysqli_query($conn, $sqlDeleteUser);

        if ($resultDeleteUser) {
            header("Location: manageUserlists.php");
            exit();
        } else {
            echo "Failed to delete user: " . mysqli_error($conn);
        }
    } else {
        echo "Error: 'deleteUserID' is not set in the POST request.";
    }
} elseif (isset($_POST['addUser'])) {
    // Add operation
    $username = $_POST['username'];
    $userEmail = $_POST['userEmail'];
echo "Username: $username<br>";
echo "User Email: $userEmail<br>";
    // Default values for userType, userPic, and password
    $userType = 'U';
    $userPic = 'NULL';
    $password = $username; // Set password to the username

    // Insert query with default values
    $sql = "INSERT INTO `user`(`userID`, `username`, `userType`, `userPic`, `userEmail`, `password`) VALUES (NULL,'$username','$userType','$userPic','$userEmail','$password')";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        $_SESSION['success_message'] = "Records added successfully.";
            header("Location: manageUserlists.php");
            exit();
    } else {
        $_SESSION['error_message'] = "Failed to add user.";
        header("Location: manageUserlists.php");
        exit(); 
    }
} else {
    echo "Error: Invalid operation.";
}
?>
