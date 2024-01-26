<?php
session_start();
$signID = $_POST['signID'];

if (isset($_POST["edit"])) {
    require "dbConn.php";

    $signID = $_POST['signID'];
    $sign = $_POST['sign'];

    if (!empty($_FILES['video']['name'])) {
        $target_dir = "./videos/";
        $target_file = $target_dir . basename($_FILES['video']['name']);
        $videoFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if the file is a valid video file
        if (in_array($videoFileType, array("mp4", "avi", "mov", "mkv", "wmv"))) {
            move_uploaded_file($_FILES['video']['tmp_name'], $target_file);

            $updateVideoSql = "UPDATE dic SET video = '$target_file' WHERE signID = $signID";
            $conn->query($updateVideoSql);
        } else {
            echo "Invalid video file format. Please upload a valid video.";
        }
    }

    $sql = "UPDATE `dic` SET `sign`='$sign' WHERE signID = $signID";
    $result = mysqli_query($conn, $sql);

    if ($result) {
            $_SESSION['success_message'] = "Record edited successfully.";
            header("Location: manageDic.php");
            exit();
    } else {
        echo "Failed: " . mysqli_error($conn);
    }
}
?>
