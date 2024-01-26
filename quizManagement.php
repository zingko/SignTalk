<?php
session_start();

require "dbConn.php";

if (isset($_POST['editQuiz'])) {
    // Edit operation
    $courseID = $_POST['courseID'];
    $quizID = $_POST['quizID'];
    $quizTitle = $_POST['quizTitle'];

    $sql = "UPDATE `quiz` SET `quizID`=?, `quizTitle`=?, `courseID`=? WHERE quizID = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "issi", $quizID, $quizTitle, $courseID, $quizID);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_affected_rows($stmt) > 0) {
            $_SESSION['success_message'] = "Record edited successfully.";
            header("Location: manageQuiz.php");
            exit();
        } else {
            echo "Failed to update the quiz.";
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Failed: " . mysqli_error($conn);
    }
}
// Check if the delete button for multiple records is clicked
elseif (isset($_POST['delSelected'])) {
    // Get the selected Lesson IDs to be deleted
    $selectedQuizIDs = isset($_POST['options']) ? $_POST['options'] : [];

    if (!empty($selectedQuizIDs)) {
        // Convert array values to a comma-separated string for the SQL query
        $selectedQuizIDsString = implode(',', $selectedQuizIDs);

        // Execute the delete query for selected records
        $sqlDeleteSelected = "DELETE FROM `quiz` WHERE quizID IN ($selectedQuizIDsString)";
        $resultDeleteSelected = mysqli_query($conn, $sqlDeleteSelected);

        if ($resultDeleteSelected) {
                        $_SESSION['success_message'] = "Records deleted successfully.";
            header("Location: manageQuiz.php");
            exit();
        } else {
            echo "Failed to delete selected quizzes: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['error_message'] = "Error: No records selected for deletion.";
        header("Location: manageQuiz.php");
        exit();    }
}
elseif (isset($_POST['delQuiz'])) {
    // Get the Quiz ID to be deleted
    $deleteQuizID = isset($_POST['deleteQuizID']) ? $_POST['deleteQuizID'] : null;

    if ($deleteQuizID) {
        // Execute the delete query
        $sqlDeleteQuiz = "DELETE FROM `quiz` WHERE quizID = $deleteQuizID";
        $resultDeleteQuiz = mysqli_query($conn, $sqlDeleteQuiz);

        if ($resultDeleteQuiz) {
                        $_SESSION['success_message'] = "Record deleted successfully.";
            header("Location: manageQuiz.php");
            exit();
        } else {
            echo "Failed to delete Quiz: " . mysqli_error($conn);
        }
    } else {
        echo "Error: 'deleteQuizID' is not set in the POST request.";
    }
} elseif (isset($_POST['addQuiz'])) {
    // Add operation
   
    $quizTitle = $_POST['quizTitle'];
    $courseID = $_POST['courseID']; 
    $quizID = $_POST['quizID']; 



    // Rest of your code to insert into the database
    $sql = "INSERT INTO `quiz`(`quizID`, `courseID`, `quizTitle`) 
            VALUES (NULL, '$courseID', '$quizTitle')";
    
    $result = mysqli_query($conn, $sql);

    if ($result) {
                    $_SESSION['success_message'] = "Record added successfully.";
            header("Location: manageQuiz.php");
            exit();
    } else {
        echo "Failed: " . mysqli_error($conn);
    }
} else {
    echo "Error: Invalid operation.";
}
?>
?>
