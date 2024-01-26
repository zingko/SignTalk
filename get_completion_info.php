<?php
session_start();
include 'dbConn.php';

// Fetch courseID from session or any other method you use
$courseID = isset($_SESSION['courseID']) ? $_SESSION['courseID'] : null;

if (!$courseID) {
    echo json_encode(['error' => 'Invalid course ID']);
    exit;
}

// Fetch updated completed stats
$completedVideosQuery = "
    SELECT COUNT(*) as completedCount
    FROM user_lesson_completion
    WHERE lessonID IN (SELECT lessonID FROM lesson WHERE courseID = ?)
        AND completion_count = 1
        AND userID = ?
";

$stmt = mysqli_prepare($conn, $completedVideosQuery);

if ($stmt) {
    // Replace $userID with the actual user ID you are interested in
    $userID = isset($_SESSION['userID']) ? $_SESSION['userID'] : null;

    if (!$userID) {
        echo json_encode(['error' => 'Invalid user ID']);
        exit;
    }

    mysqli_stmt_bind_param($stmt, "ss", $courseID, $userID);
    mysqli_stmt_execute($stmt);
    $resultCompletedVideos = mysqli_stmt_get_result($stmt);

    if ($resultCompletedVideos) {
        $rowCompletedVideos = mysqli_fetch_assoc($resultCompletedVideos);
        $completedVideos = $rowCompletedVideos['completedCount'];

        // Return JSON response
        echo json_encode(['completedVideos' => $completedVideos]);
    } else {
        // Handle database query error for completed videos
        echo json_encode(['error' => mysqli_error($conn)]);
    }

    mysqli_stmt_close($stmt);
} else {
    // Handle database query preparation error
    echo json_encode(['error' => mysqli_error($conn)]);
}

mysqli_close($conn);
?>
