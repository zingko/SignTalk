<?php
session_start();
include 'dbConn.php';

if (isset($_SESSION['user'])) {
    // User is logged in
    $userID = $_SESSION['user']['userID'];
} else {
    // User is not logged in, use a temporary identifier (e.g., session ID)
    $userID = session_id();
}

if (isset($_POST['quizID']) && isset($_POST['marks'])) {
    $quizID = mysqli_real_escape_string($conn, $_POST['quizID']);
    $marks = mysqli_real_escape_string($conn, $_POST['marks']);

    // Check if the user already has a record for the given quiz
    $existingRecordQuery = "SELECT * FROM marks WHERE quizID = '$quizID' AND userID = '$userID'";
    $existingRecordResult = mysqli_query($conn, $existingRecordQuery);

    if (mysqli_num_rows($existingRecordResult) > 0) {
        // Update existing record
        $updateQuery = "UPDATE marks SET marks = '$marks' WHERE quizID = '$quizID' AND userID = '$userID'";
        mysqli_query($conn, $updateQuery);
    } else {
        // Insert new record
        $insertQuery = "INSERT INTO marks (quizID, userID, marks) VALUES ('$quizID', '$userID', '$marks')";
        mysqli_query($conn, $insertQuery);
    }
} else {
    // Handle invalid request
    echo "Invalid request.";
}

$conn->close();
?>
