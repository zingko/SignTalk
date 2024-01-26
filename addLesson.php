<?php
session_start();

require "dbConn.php";

if (isset($_POST['editLesson'])) {
    // Edit operation
    $lessonID = $_POST['lessonID'];
    $lessonTitle = $_POST['lessonTitle'];
    $courseID = $_POST['courseID']; // Add this line to get the selected courseID

    // Check if a new video file is uploaded
    if (!empty($_FILES['video_url']['name'])) {
        // File Upload for Video
        $video_upload_dir = 'videos/';
        $video_tmp_name = $_FILES['video_url']['tmp_name'];
        $video_name = $_FILES['video_url']['name'];
        move_uploaded_file($video_tmp_name, $video_upload_dir . $video_name);
        $video_url = $video_upload_dir . $video_name;

        // Update the database with the new video URL
        $sql = "UPDATE `lesson` SET `courseID`='$courseID', `lessonTitle`='$lessonTitle', 
                `video_url`='$video_url' WHERE lessonID = $lessonID";
    } else {
        // Update the database without changing the existing video URL
        $sql = "UPDATE `lesson` SET `courseID`='$courseID', `lessonTitle`='$lessonTitle'
                WHERE lessonID = $lessonID";
    }

    $result = mysqli_query($conn, $sql);

    if ($result) {
        $_SESSION['success_message'] = "Record updated successfully.";
        header("Location: LessonManagement.php");
        exit();
    } else {
        echo "Failed: " . mysqli_error($conn);
    }
}// Check if the delete button for multiple records is clicked
elseif (isset($_POST['delSelected'])) {
    // Get the selected Lesson IDs to be deleted
    $selectedLessonIDs = isset($_POST['options']) ? $_POST['options'] : [];

    if (!empty($selectedLessonIDs)) {
        // Convert array values to a comma-separated string for the SQL query
        $selectedLessonIDsString = implode(',', $selectedLessonIDs);

        // Execute the delete query for selected records
        $sqlDeleteSelected = "DELETE FROM `lesson` WHERE lessonID IN ($selectedLessonIDsString)";
        $resultDeleteSelected = mysqli_query($conn, $sqlDeleteSelected);

        if ($resultDeleteSelected) {
                        $_SESSION['success_message'] = "Records deleted successfully.";
            header("Location: LessonManagement.php");
            exit();
        } else {
            echo "Failed to delete selected lessons: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['error_message'] = "Error: No records selected for deletion.";
        header("Location: LessonManagement.php");
        exit();    }
}
elseif (isset($_POST['delLesson'])) {
    // Get the Lesson ID to be deleted
    $deleteLessonID = isset($_POST['deleteLessonID']) ? $_POST['deleteLessonID'] : null;

    if ($deleteLessonID) {
        // Execute the delete query
        $sqlDeleteLesson = "DELETE FROM `lesson` WHERE lessonID = $deleteLessonID";
        $resultDeleteLesson = mysqli_query($conn, $sqlDeleteLesson);

        if ($resultDeleteLesson) {
             $_SESSION['success_message'] = "Record deleted successfully.";
        header("Location: LessonManagement.php");
        exit();
        } else {
            echo "Failed to delete Lesson: " . mysqli_error($conn);
        }
    } else {
        echo "Error: 'deleteLessonID' is not set in the POST request.";
    }
} elseif (isset($_POST['addLesson'])) {
    // Add operation
    $lessonTitle = $_POST['lessonTitle'];
    $courseID = $_POST['courseID'];

    // File Upload for Video
    $video_upload_dir = 'videos/';
    $video_tmp_name = $_FILES['video_url']['tmp_name'];
    $video_name = $_FILES['video_url']['name'];
    move_uploaded_file($video_tmp_name, $video_upload_dir . $video_name);
    $video_url = $video_upload_dir . $video_name;

    // Use prepared statement to insert data
    $sql = "INSERT INTO `lesson`(`lessonID`, `courseID`, `lessonTitle`,  `video_url`) VALUES (NULL, ?, ?, ?)";
    
    $stmt = mysqli_prepare($conn, $sql);

    // Bind parameters
    mysqli_stmt_bind_param($stmt, "sss", $courseID, $lessonTitle, $video_url);

    // Execute the statement
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        $_SESSION['success_message'] = "Records created successfully.";
        header("Location: LessonManagement.php");
        exit();
    } else {
        echo "Failed: " . mysqli_error($conn);
    }

    // Close the statement
    mysqli_stmt_close($stmt);
}
