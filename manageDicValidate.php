<?php
session_start();

include "dbConn.php";

if (isset($_POST['add_submit'])) {
    // Check if the necessary fields are set
    if (isset($_POST['sign']) && isset($_FILES['video'])) {
        $sign = $_POST['sign'];
var_dump($_POST);
        var_dump($_FILES);
        // File upload handling
        $targetDirectory = "dic/";
        $targetFile = $targetDirectory . basename($_FILES['video']['name']);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Check if the file already exists
        if (file_exists($targetFile)) {
            echo "Sorry, the file already exists.";
            $uploadOk = 0;
        }

        if ($_FILES['video']['size'] > 50000000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow certain file formats (you can add more formats if needed)
        if ($imageFileType != "mp4" && $imageFileType != "avi" && $imageFileType != "mov") {
            echo "Sorry, only MP4, AVI, and MOV files are allowed.";
            $uploadOk = 0;
        }

        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        } else {
            if (move_uploaded_file($_FILES['video']['tmp_name'], $targetFile)) {
                echo "The file " . htmlspecialchars(basename($_FILES['video']['name'])) . " has been uploaded.";

                // Now, insert data into the database
                $sql = "INSERT INTO `dic`(`signID`, `sign`, `video`) VALUES (NULL,'$sign','$targetFile')";
                $result = mysqli_query($conn, $sql);

                if ($result) {
                         $_SESSION['success_message'] = "New record created successfully.";
            header("Location: manageDic.php");
            exit();
                } else {
                    echo "Failed to insert data into the database: " . mysqli_error($conn);
                }
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    } else {
        echo "Missing required fields.";
    }
}
?>
