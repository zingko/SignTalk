<?php
session_start();

require "dbConn.php";

if (isset($_POST['editQuestion'])) {
    // Edit operation
    $questionID = $_POST['questionID'];
    $quizID = $_POST['quizID'];
    $questionDescription = $_POST['questionDescription'];
    $option_a = $_POST['option_a'];
    $option_b = $_POST['option_b'];
    $option_c = $_POST['option_c'];
    $option_d = $_POST['option_d'];
    $answer = $_POST['answer'];

    // Check if a new questionMedia file is uploaded
    if (!empty($_FILES['questionMedia']['name'])) {
        // File Upload for Media (Image or Video)
        $media_upload_dir = 'media/';
        $media_tmp_name = $_FILES['questionMedia']['tmp_name'];
        $media_name = $_FILES['questionMedia']['name'];
        move_uploaded_file($media_tmp_name, $media_upload_dir . $media_name);
        $questionMedia = $media_upload_dir . $media_name;

        // Determine file type based on file extension
        $allowed_image_types = ['jpg', 'jpeg', 'png', 'gif'];
        $allowed_video_types = ['mp4', 'avi', 'mov', 'mkv'];

        $file_extension = pathinfo($questionMedia, PATHINFO_EXTENSION);

        if (in_array(strtolower($file_extension), $allowed_image_types)) {
            // It's an image
            // Additional image processing code goes here
        } elseif (in_array(strtolower($file_extension), $allowed_video_types)) {
            // It's a video
            // Additional video processing code goes here
        } else {
            // Invalid file type
            echo "Invalid file type. Please upload an image or video.";
            exit();
        }

        // Update the database with the new questionMedia URL
        $sql = "UPDATE `question` SET `quizID`='$quizID', `questionDescription`='$questionDescription', 
                `option_a`='$option_a', `option_b`='$option_b', `option_c`='$option_c', `option_d`='$option_d', 
                `answer`='$answer', `questionMedia`='$questionMedia' WHERE questionID = $questionID";
    } else {
        // Update the database without changing the existing questionMedia URL
        $sql = "UPDATE `question` SET `quizID`='$quizID', `questionDescription`='$questionDescription', 
                `option_a`='$option_a', `option_b`='$option_b', `option_c`='$option_c', `option_d`='$option_d', 
                `answer`='$answer' WHERE questionID = $questionID";
    }

    $result = mysqli_query($conn, $sql);

    if ($result) {
        $_SESSION['success_message'] = "Record edited successfully.";
        header("Location: manageQuestionCourse.php");
        exit();
    } else {
        echo "Failed: " . mysqli_error($conn);
    }
} // Check if the delete button for multiple records is clicked
elseif (isset($_POST['delSelected'])) {
    // Get the selected Lesson IDs to be deleted
    $selectedQuestionIDs = isset($_POST['options']) ? $_POST['options'] : [];

    if (!empty($selectedQuestionIDs)) {
        // Convert array values to a comma-separated string for the SQL query
        $selectedQuestionIDsString = implode(',', $selectedQuestionIDs);

        // Execute the delete query for selected records
        $sqlDeleteSelected = "DELETE FROM `question` WHERE questionID IN ($selectedQuestionIDsString)";
        $resultDeleteSelected = mysqli_query($conn, $sqlDeleteSelected);

        if ($resultDeleteSelected) {
            $_SESSION['success_message'] = "Records deleted successfully.";
            header("Location: manageQuestionCourse.php");
            exit();
        } else {
            echo "Failed to delete selected questions: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['error_message'] = "Error: No records selected for deletion.";
        header("Location: manageQuestionCourse.php");
        exit();    }
}
elseif (isset($_POST['delQuestion'])) {
    // Get the Question ID to be deleted
    $deleteQuestionID = isset($_POST['deleteQuestionID']) ? $_POST['deleteQuestionID'] : null;

    if ($deleteQuestionID) {
        // Execute the delete query
        $sqlDeleteQuestion = "DELETE FROM `question` WHERE questionID = $deleteQuestionID";
        $resultDeleteQuestion = mysqli_query($conn, $sqlDeleteQuestion);

        if ($resultDeleteQuestion) {
            $_SESSION['success_message'] = "Record deleted successfully.";
    header("Location: manageQuestionCourse.php");
    exit();
        } else {
            echo "Failed to delete Question: " . mysqli_error($conn);
        }
    } else {
        echo "Error: 'deleteQuestionID' is not set in the POST request.";
    }
} elseif (isset($_POST['addQuestion'])) {
    // Add operation
    $questionDescription = $_POST['questionDescription'];
    $option_a = $_POST['option_a'];
    $option_b = $_POST['option_b'];
    $option_c = $_POST['option_c'];
    $option_d = $_POST['option_d'];
    $answer = $_POST['answer'];
    $quizID = $_POST['quizID']; 

    // File Upload for Media (Image or Video)
$media_upload_dir = 'media/';
$media_tmp_name = $_FILES['questionMedia']['tmp_name'];
$media_name = $_FILES['questionMedia']['name'];
move_uploaded_file($media_tmp_name, $media_upload_dir . $media_name);
$questionMedia = $media_upload_dir . $media_name;

// Determine file type based on file extension
$allowed_image_types = ['jpg', 'jpeg', 'png', 'gif'];
$allowed_video_types = ['mp4', 'avi', 'mov', 'mkv'];

$file_extension = pathinfo($questionMedia, PATHINFO_EXTENSION);

if (in_array(strtolower($file_extension), $allowed_image_types)) {
    // It's an image
    // Additional image processing code goes here
} elseif (in_array(strtolower($file_extension), $allowed_video_types)) {
    // It's a video
    // Additional video processing code goes here
} else {
    // Invalid file type
    echo "Invalid file type. Please upload an image or video.";
    exit();
}

    // Rest of your code to insert into the database
    $sql = "INSERT INTO `question`(`questionID`, `quizID`, `questionDescription`, `questionMedia`, `option_a`, `option_b`, `option_c`, `option_d`, `answer`) 
            VALUES (NULL, '$quizID', '$questionDescription', '$questionMedia', '$option_a', '$option_b', '$option_c', '$option_d', '$answer')";
    
    $result = mysqli_query($conn, $sql);

    if ($result) {
        $_SESSION['success_message'] = "Records created successfully.";
            header("Location: manageQuestionCourse.php");
            exit();
    } else {
        echo "Failed: " . mysqli_error($conn);
    }
} else {
    echo "Error: Invalid operation.";
}
?>
?>
