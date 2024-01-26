<?php
session_start();

// Check if 'signID' is set in the $_POST array
if (isset($_POST['signID'])) {
    $signID = $_POST['signID'];

    if (isset($_POST['delete'])) {
        require "dbConn.php";

        $sql = "DELETE FROM `dic` WHERE signID = $signID";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            $_SESSION['success_message'] = "Record deleted successfully.";
            header("Location: manageDic.php");
            exit();
        } else {
            echo "Failed: " . mysqli_error($conn);
        }
    }
} else {
    echo "Error: 'signID' is not set in the POST request.";
}
?>
