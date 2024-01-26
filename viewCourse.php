<?php
session_start();
include 'dbConn.php';

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
}

if (isset($_GET['courseID'])) {
    $courseID = $_GET['courseID'];

    // Fetch Course Details
    $select_course = $conn->prepare("SELECT * FROM course WHERE courseID = ?");
    
    // Check for query preparation error
    if (!$select_course) {
        die('Error in SQL query preparation: ' . mysqli_error($conn));
    }
    
    $select_course->bind_param("i", $courseID);
    $select_course->execute();
    $result = $select_course->get_result();
    
    // Check for query execution error
    if (!$result) {
        die('Error in SQL query execution: ' . mysqli_error($conn));
    }
    
    $course_details = $result->fetch_assoc();

    if (!$course_details) {
        // Handle case when course is not found
        die("Course not found!");
    }

    // Fetch Playlist Videos
    // Fetch Playlist Videos with Thumbnails
    $select_videos = $conn->prepare("SELECT lessonID, lessonTitle, video_url FROM lesson WHERE courseID = ?");
    $select_videos->bind_param("i", $courseID);
    $select_videos->execute();
    $result_videos = $select_videos->get_result();

    // Check for query execution error
    if (!$result_videos) {
        die('Error in SQL query execution for fetching videos: ' . mysqli_error($conn));
    }

    $playlist_videos = [];
    while ($fetch_videos = $result_videos->fetch_assoc()) {
        $playlist_videos[] = $fetch_videos;
    }

    // Get the total count of videos
    $total_videos_count = count($playlist_videos);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Manage Course</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round">
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<link rel="stylesheet" href="css/manageDic.css">

<script>
$(document).ready(function(){
	// Activate tooltip
	$('[data-toggle="tooltip"]').tooltip();
	
	// Select/Deselect checkboxes
	var checkbox = $('table tbody input[type="checkbox"]');
	$("#selectAll").click(function(){
		if(this.checked){
			checkbox.each(function(){
				this.checked = true;                        
			});
		} else{
			checkbox.each(function(){
				this.checked = false;                        
			});
		} 
	});
	checkbox.click(function(){
		if(!this.checked){
			$("#selectAll").prop("checked", false);
		}
	});
	$('#editEmployeeModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var lessonID = button.data('lessonid');
            var lessonTitle = button.data('lessontitle');
            var description = button.data('description');
            var video_url = button.data('video_url');

            $('#edit_lessonID').val(lessonID);
            $('#edit_lessonID_display').val(lessonID);
            $('#edit_lessontitle').val(lessonTitle);
            $('#edit_lessonDescription').val(description);
            $('#edit_video_url').val(video_url);
            });
    
	$('#deleteEmployeeModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var lessonID = button.data('lessonid');
    $('#delete_lessonID').val(lessonID);
});
});
</script>
</head>
<body>

<header>

        <a href="index.php"><img src="images/silence.png" alt="" width="200" height="50"></a>
    <div id="menu" class="fas fa-bars"></div>
    <div class="navbar">
    <a href="adminPro.php">Profile</a>
    <div class="dropdown1">
    <button class="dropbtn1">Management
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-content1">
      <a href="manageUserlists.php">User</a>
      <a href="manageDic.php">Dictionary</a>
      <a href="manageCourse.php">Course</a>
      <a href="LessonManagement.php">Lesson</a>
            <a href="manageQuiz.php">Quiz</a>
      <a href="manageQuestionCourse.php">Question</a>
      <a href="manageSubscription.php">Subscription</a>
    </div>
  </div>
        <?php
        if (!isset($_SESSION["user"])) {
            echo '<a href="login.php">Log in</a>';
        } else {
            echo '<div class="dropdown1">
                    <button class="dropbtn1">' . $_SESSION['username'] . '
                        <i class="fa fa-caret-down"></i>
                    </button>
                    <div class="dropdown-content1">
                        <a href="logout.php">Logout</a>
                    </div>
                  </div>';
        }
        ?>
</div>

</header>
    <div class="container1">

    <div class="container">
    <!-- Display Course Details -->
    <div class="course-details">
        <h1>Course Details</h1>
        <p><strong>Course ID:</strong> <?php echo $course_details['courseID']; ?></p>
        <p><strong>Course Name:</strong> <?php echo $course_details['courseName']; ?></p>
        <p><strong>Course Level:</strong> <?php echo $course_details['courseLevel']; ?></p>
        <p><strong>Course Description:</strong> <?php echo $course_details['courseDescription']; ?></p>
        <p><strong>Type:</strong> <?php echo $course_details['type']; ?></p>
        <p><strong>Duration Hours:</strong> <?php echo $course_details['duration_hours']; ?></p>
        <p><strong>Video Count:</strong> <?php echo $course_details['video_count']; ?></p>
    </div>


<!-- Display Playlist Videos -->
<div class="playlist-videos">
    <h2>Video Playlist</h2>
    <div class="videos-container">
        <?php
        if ($total_videos_count > 0) {
            $index = 1; // Initialize index for numbering
            foreach ($playlist_videos as $video) {
        ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="video-item-container">
                            <h3><?php echo sprintf('%02d. %s', $index++, $video['lessonTitle']); ?></h3>
                        </div>
                    </div>
                </div>
        <?php
            }
        } else {
            echo '<p class="empty">No videos added yet! <a href="LessonManagement.php" class="btn" style="margin-top: 1.5rem;">Add videos</a></p>';
        }
        ?>
    </div>
</div>


        <!-- footer section  -->

<footer class="footer">

    <div class="box-container">

        <div class="box">
            <h3>about us</h3>
            <p>SignTalk is your gateway to mastering MSL. Let's break down barriers and build bridges of understanding together.</p>
        </div>

        <div class="box">
            <h3>quick links</h3>
	        <a href="index.php">home</a>
            <a href="dic.php">dictionary</a>
            <a href="beginner.php">course</a>
            <a href="price.php">price</a>
            <a href="login.php">log in</a>
        </div>

        <div class="box">
            <h3>contact us</h3>
           <p> <i class="fas fa-phone"></i> +123-456-7890 </p>
           <p> <i class="fas fa-envelope"></i> signtalk@gmail.com </p>
        </div>

    </div>

    <div class="credit"> &copy;Copyright <span> SignTalk </span> | all rights reserved | for educational purpose only </div>

</footer>

</div>


</div>

</div>
</body>
</html>