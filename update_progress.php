<?php
session_start();
include 'dbConn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input to prevent SQL injection
    $lessonID = mysqli_real_escape_string($conn, $_POST['lessonID']);
    $completionStatus = mysqli_real_escape_string($conn, $_POST['completionStatus']);

    // Retrieve user ID from session or any other method you use
    $userID = isset($_SESSION['user']['userID']) ? $_SESSION['user']['userID'] : null;

    if (!$userID) {
        echo "Error: User not authenticated.";
        exit;
    }

    // Check if the record already exists
    $checkQuery = "SELECT * FROM user_lesson_completion 
                   WHERE userID = '$userID' AND lessonID = '$lessonID'";
    $checkResult = mysqli_query($conn, $checkQuery);

    if ($checkResult) {
        if (mysqli_num_rows($checkResult) > 0) {
            // Update existing record
            $updateQuery = "UPDATE user_lesson_completion 
                            SET completion_count = '$completionStatus' 
                            WHERE userID = '$userID' AND lessonID = '$lessonID'";
            $result = mysqli_query($conn, $updateQuery);
        } else {
            // Insert new record
            $insertQuery = "INSERT INTO user_lesson_completion (userID, lessonID, completion_count) 
                            VALUES ('$userID', '$lessonID', '$completionStatus')";
            $result = mysqli_query($conn, $insertQuery);
        }

        if ($result) {
            echo "Completion status updated successfully";
        } else {
            echo "Error updating completion status: " . mysqli_error($conn);
        }
    } else {
        echo "Error checking existing record: " . mysqli_error($conn);
    }
} else {
    echo "Invalid request.";
}
?>
