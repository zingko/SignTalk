<?php
session_start();

require "dbConn.php";

if (isset($_POST['editCourse'])) {
    // Edit operation
    $courseID = $_POST['courseID'];
    $courseName = $_POST['courseName'];
    $courseLevel = $_POST['courseLevel'];
    $courseDescription = $_POST['courseDescription'];
    $type = $_POST['type'];
    $duration_hours = $_POST['duration_hours'];
    $video_count = $_POST['video_count'];

    $sql = "UPDATE `course` SET 
            `courseName`='$courseName', 
            `courseLevel`='$courseLevel', 
            `courseDescription`='$courseDescription', 
            `type`='$type', 
            `duration_hours`='$duration_hours', 
            `video_count`='$video_count' 
            WHERE `courseID`='$courseID'";
    
    $result = mysqli_query($conn, $sql);

    if ($result) {
            $_SESSION['success_message'] = "Record edited successfully.";
            header("Location: manageCourse.php");
            exit();
    } else {
        echo "Failed: " . mysqli_error($conn);
    }
}// Check if the delete button for multiple records is clicked
elseif (isset($_POST['delSelected'])) {
    // Get the selected Course IDs to be deleted
    $selectedCourseIDs = isset($_POST['options']) ? $_POST['options'] : [];

    if (!empty($selectedCourseIDs)) {
        // Convert array values to a comma-separated string for the SQL query
        $selectedCourseIDsString = implode(',', $selectedCourseIDs);

        // Execute the delete query for selected records
        $sqlDeleteSelected = "DELETE FROM `course` WHERE courseID IN ($selectedCourseIDsString)";
        $resultDeleteSelected = mysqli_query($conn, $sqlDeleteSelected);

        if ($resultDeleteSelected) {
            $_SESSION['success_message'] = "Records deleted successfully.";
            header("Location: manageCourse.php");
            exit();
        } else {
            echo "Failed to delete selected courses: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['error_message'] = "Error: No records selected for deletion.";
        header("Location: manageCourse.php");
        exit();    
        
    }
}
elseif (isset($_POST['delCourse'])) {
    // Get the course ID to be deleted
    $deleteCourseID = isset($_POST['deleteCourseID']) ? $_POST['deleteCourseID'] : null;

    if ($deleteCourseID) {
        // Execute the delete query
        $sqlDeleteCourse = "DELETE FROM `course` WHERE courseID = $deleteCourseID";
        $resultDeleteCourse = mysqli_query($conn, $sqlDeleteCourse);

        if ($resultDeleteCourse) {
            $_SESSION['success_message'] = "Records deleted successfully.";
            header("Location: manageCourse.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Error: Failed to delete Course.";
            header("Location: manageCourse.php");
            exit();
        }
    } else {
        echo "Error: 'deleteCourseID' is not set in the POST request.";
    }
} elseif (isset($_POST['addCourse'])) {
    // Add operation
    $courseName = $_POST['courseName'];
    $courseLevel = $_POST['courseLevel'];
    $courseDescription = $_POST['courseDescription'];
    $type = $_POST['type'];
    $duration_hours = $_POST['duration_hours'];
    $video_count = $_POST['video_count'];

    // Get the maximum courseID from the course table
    $maxCourseIDQuery = "SELECT MAX(courseID) AS maxCourseID FROM course";
    $maxCourseIDResult = mysqli_query($conn, $maxCourseIDQuery);
    $row = mysqli_fetch_assoc($maxCourseIDResult);
    $maxCourseID = $row['maxCourseID'];

    // Calculate the new courseID
    $newCourseID = $maxCourseID + 1;

    // Insert the new row with the calculated courseID
    $sql = "INSERT INTO `course` (`courseID`, `courseName`, `courseLevel`, `courseDescription`, `type`, `duration_hours`, `video_count`) 
            VALUES ('$newCourseID', '$courseName', '$courseLevel', '$courseDescription', '$type', '$duration_hours', '$video_count')";
    $result = mysqli_query($conn, $sql);

if ($result) {
     $_SESSION['success_message'] = "Records created successfully.";
            header("Location: manageCourse.php");
            exit();

} else {
    $_SESSION['error_message'] = "Error: Failed to add Course.";
            header("Location: manageCourse.php");
            exit();
}

} else {
    echo "Error: Invalid operation.";
}
?>
