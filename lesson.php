<?php
session_start();
include 'dbConn.php';

// Check if the user is logged in
if (isset($_SESSION['user'])) {
    // Assign the user ID to $userID
    $userID = $_SESSION['user']['userID'];
} else {
    // Handle the case where the user is not logged in
    header("Location: login.php");
    exit;
}
// Check if courseID is set
if (isset($_GET['courseID'])) {
    // Sanitize input to prevent SQL injection
    $courseID = mysqli_real_escape_string($conn, $_GET['courseID']);
} else {
    // Handle case where courseID is not set
    echo "Invalid request.";
    exit;
}

// Fetch the courseName for the specific courseID
$queryCourseName = "SELECT courseName FROM course WHERE courseID = '$courseID'";
$resultCourseName = mysqli_query($conn, $queryCourseName);

if ($resultCourseName) {
    // Check if any rows are returned
    if (mysqli_num_rows($resultCourseName) > 0) {
        // Fetch the courseName
        $rowCourseName = mysqli_fetch_assoc($resultCourseName);
        $courseName = $rowCourseName['courseName'];
    } else {
        // Handle case where no rows are returned (course not found)
        echo "Course not found.";
        exit;
    }
} else {
    // Handle database query error for courseName
    echo "Error: " . mysqli_error($conn);
    exit;
}

// Fetch main video for the specific courseID
$queryMainVideo = "SELECT video_url FROM lesson WHERE courseID = '$courseID' LIMIT 1";
$resultMainVideo = mysqli_query($conn, $queryMainVideo);

if ($resultMainVideo) {
    // Check if any rows are returned
    if (mysqli_num_rows($resultMainVideo) > 0) {
        // Fetch the video path
        $rowMainVideo = mysqli_fetch_assoc($resultMainVideo);
        $mainVideoPath = $rowMainVideo['video_url'];
    } else {
        // Handle case where no rows are returned (no lessons for the course)
        echo "No lessons found for this course.";
        exit;
    }
} else {
    // Handle database query error
    echo "Error: " . mysqli_error($conn);
    exit;
}

// Fetch all videos and titles for the specific courseID
$queryAllVideos = "
    SELECT 
        l.lessonID,
        l.lessonTitle,
        l.video_url,
        COALESCE(ulc.completion_count, 0) AS completion_count
    FROM 
        lesson l
    LEFT JOIN 
        user_lesson_completion ulc ON l.lessonID = ulc.lessonID AND ulc.userID = '$userID'
    WHERE 
        l.courseID = '$courseID';
";
$resultAllVideos = mysqli_query($conn, $queryAllVideos);

// Create an array to store all video paths, titles, and lessonIDs
$allVideoData = array();

if ($resultAllVideos) {
    while ($rowAllVideos = mysqli_fetch_assoc($resultAllVideos)) {
        $lessonID = $rowAllVideos['lessonID'];
        $videoUrl = $rowAllVideos['video_url'];
        $lessonTitle = $rowAllVideos['lessonTitle'];
        $completionCount = $rowAllVideos['completion_count']; // to fetch completion_count

        $allVideoData[] = array(
            'lessonID' => $lessonID,
            'video_url' => $videoUrl,
            'lessonTitle' => $lessonTitle,
            'completion_count' => $completionCount, // to store completion_count
        );

        $allLessonTitles[] = $lessonTitle;
    }
} else {
    // Handle database query error for all videos
    echo "Error: " . mysqli_error($conn);
    exit;
}


// Fetch quiz information based on courseID
$queryQuiz = "SELECT quizID FROM quiz WHERE courseID = '$courseID'";
$resultQuiz = mysqli_query($conn, $queryQuiz);

if ($resultQuiz) {
    // Check if any rows are returned
    if (mysqli_num_rows($resultQuiz) > 0) {
        // Fetch the quizID
        $rowQuiz = mysqli_fetch_assoc($resultQuiz);
        $quizID = $rowQuiz['quizID'];
    } else {
        // Handle case where no rows are returned (quiz not found)
        echo "Quiz not found for this course.";
        exit;
    }
} else {
    // Handle database query error for quiz
    echo "Error: " . mysqli_error($conn);
    exit;
}
// Calculate completed percentage
$completedVideosQuery = "
    SELECT 
        COUNT(*) as completedCount 
    FROM 
        user_lesson_completion ulc
    JOIN 
        lesson l ON ulc.lessonID = l.lessonID
    WHERE 
        l.courseID = '$courseID' AND ulc.completion_count = 1 AND ulc.userID = '$userID';
";
$resultCompletedVideos = mysqli_query($conn, $completedVideosQuery);

if ($resultCompletedVideos) {
    $rowCompletedVideos = mysqli_fetch_assoc($resultCompletedVideos);
    $completedVideos = $rowCompletedVideos['completedCount'];
    $completedPercentage = ($completedVideos / count($allLessonTitles)) * 100;
} else {
    // Handle database query error for completed videos
    echo "Error: " . mysqli_error($conn);
    exit;
}

?>

 <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Alphabet</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <link rel="stylesheet" href="css/lesson.css">
    
    
</head>

<body>
    

<header>

    <a href="index.php"><img src="images/silence.png" alt="" width="200" height="50"></a>
    <div id="menu" class="fas fa-bars"></div>
     <div class="navbar">
    <a href="index.php">Home</a>
    <a href="dic.php">Dictionary</a>
    <div class="dropdown">
    <button class="dropbtn">Course
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-content">
      <a href="beginner.php">Beginner</a>
      <a href="intermediate.php">Intermediate</a>
      <a href="advanced.php">Advanced</a>
    </div>
  </div>
                 <a href="price.php">price</a>
                 <?php
        if (!isset($_SESSION["user"])) {
            echo '<a href="login.php">Log in</a>';
        } else {
            echo '<div class="dropdown">
                    <button class="dropbtn">' . $_SESSION['username'] . '
                        <i class="fa fa-caret-down"></i>
                    </button>
                    <div class="dropdown-content">
                        <a href="userPro.php">Profile</a>
                        <a href="logout.php">Logout</a>
                    </div>
                  </div>';
        }
        ?>
</div>


</header>
<div class="container1">
<form method="post" action="lesson.php" id="lessonForm">
    <main class="container">
        <section class="main-video">
                <h3 class="title" id="lessonTitle"><?php echo $allVideoData[0]['lessonTitle']; ?></h3>
                <div class="progress">
    <div class="skill-box">
        <div class="skill-bar" style="width: <?php echo $completedPercentage; ?>%">
            <span class="skill-per html">
<span class="tooltip" id="completedPercentage"><?php echo round($completedPercentage) . '%'; ?></span>
            </span>
        </div>
    </div>
</div>

<video id="mainVideo" controls autoplay muted width="800" height="450"></video>
        </section>

<!-- Video Playlist -->
<section class="video-playlist">
                <h3 class="title"><?php echo $courseName; ?></h3>
<p><?php echo count($allLessonTitles); ?> lessons &nbsp; . &nbsp; <span id="completedVideosCount"><?php echo $completedVideos; ?></span> of <?php echo count($allLessonTitles); ?> complete</p>
    <div class="videos">
      <?php
foreach ($allVideoData as $index => $video) {
    $lessonID = $video['lessonID'];
    $videoUrl = $video['video_url'];
    $lessonTitle = $video['lessonTitle'];
    $completionCount = $video['completion_count'];

    // Add a class based on the completion_count value
    $completedClass = ($completionCount == 1) ? 'completed' : '';

    echo '<div class="video-title ' . $completedClass . '" id="videoTitle_' . $index . '" onclick="changeMainVideo(' . $index . ')">
            <button type="button" class="mark-complete-button ' . ($completionCount == 1 ? 'completed' : '') . '" data-lesson-id="' . $index . '">' . ($completionCount == 1 ? 'Completed' : 'Mark Complete') . '</button>
            <label class="video-label">' . ($index + 1) . '. ' . $lessonTitle . '</label>
          </div>';

    $allVideoData[$index]['lessonID'] = $lessonID;
}


?>

    </div>
</section>

    </main>
    </form>
            <div class="start_btn"><button>Start Quiz</button></div>
<!-- Info Box -->
    <div class="info_box">
        <div class="info-title"><span>Some Rules of this Quiz</span></div>
        <div class="info-list">
            <div class="info">1. Each question must be answered within <span>15 seconds</span></div>
            <div class="info">2. You have only one chance to select your answer.</div>
            <div class="info">3. Once you start the quiz, you cannot exit until you complete all the questions.</div>
        </div>
        <div class="buttons">
            <button class="quit">Exit Quiz</button>
<a href="quizTimer.php?quizID=<?php echo $quizID; ?>" class="restart">Go to Quiz</a>
        </div>
    </div>

        <!-- footer section  -->
<br>
        <br>

<!-- Footer section -->
        <footer class="footer">

    <div class="box-container">
  
        <div class="box">
            <h3>about us</h3>
            <p>SignTalk is your gateway to <br> mastering MSL.  Let's break down barriers and build bridges of understanding together.</p>
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

    <script src="quizscript.js"></script>

    
<script>
// Function to change the main video
function changeMainVideo(index) {
    // Get the main video element
    var mainVideo = document.getElementById('mainVideo');

    // Get the corresponding lesson title
    var lessonTitle = "<?php echo $allVideoData[0]['lessonTitle']; ?>"; // Set the default title

    // Check if the index is valid
    if (typeof <?php echo json_encode($allVideoData); ?> !== 'undefined' && <?php echo json_encode($allVideoData); ?>.length > index) {
        lessonTitle = <?php echo json_encode($allVideoData); ?>[index]['lessonTitle'];
        mainVideo.src = <?php echo json_encode($allVideoData); ?>[index]['video_url'];

        // Remove the "active" class from all video titles
    var videoTitles = document.querySelectorAll('.video-title');
    videoTitles.forEach(function (title) {
        title.classList.remove('active');
    });

    // Add the "active" class to the clicked video title
    var clickedVideoTitle = document.getElementById('videoTitle_' + index);
    clickedVideoTitle.classList.add('active');
    }

    // Update the displayed lesson title
    document.getElementById('lessonTitle').innerHTML = lessonTitle;

 
}
// Add event listener to mark complete buttons
var markCompleteButtons = document.querySelectorAll('.mark-complete-button');

markCompleteButtons.forEach(function (button, index) {
    button.addEventListener('click', function () {
        var lessonID = <?php echo json_encode($allVideoData); ?>[index]['lessonID'];

        if (this.innerHTML === "Mark Complete") {
            this.innerHTML = "Completed";
            this.classList.add('completed');  // add the 'completed' class
            updateCompletionStatus(lessonID, 1); // Mark lesson as complete
        } else {
            this.innerHTML = "Mark Complete";
            this.classList.remove('completed'); //  remove the 'completed' class
            updateCompletionStatus(lessonID, 0); // Mark lesson as incomplete
        }
        
    });
});



// Function to check if all lessons are marked as complete
function areAllLessonsComplete() {
    var markCompleteButtons = document.querySelectorAll('.mark-complete-button');
    for (var i = 0; i < markCompleteButtons.length; i++) {
        if (!markCompleteButtons[i].classList.contains('completed')) {
            return false;
        }
    }
    return true;
}


// "Go to Quiz" button click event
document.querySelector('.restart').onclick = function() {
    // Check if all lessons are marked as complete
    if (areAllLessonsComplete()) {
        // Redirect to quizTimer.php with quizID
        window.location.href = "quizTimer.php?quizID=<?php echo $quizID; ?>";
    } else {
        // Display an alert if not all lessons are marked as complete
        alert("Please mark all lessons as complete before going to the quiz.");
        return false;
                window.location.href = "lesson.php?courseID=<?php echo $courseID; ?>";
    }
};



// Function to update completion status using AJAX
function updateCompletionStatus(lessonID, completionStatus) {
    // Create FormData object to send data with the request
    var formData = new FormData();
    formData.append('lessonID', lessonID);
    formData.append('completionStatus', completionStatus);

    // Trigger AJAX request for form submission
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'update_progress.php', true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            // Handle the response if needed
            console.log(xhr.responseText);

            // Update completed videos count and percentage immediately on the client side
            var completedVideosCountElement = document.getElementById('completedVideosCount');
            var completedVideos = parseInt(completedVideosCountElement.innerHTML);

            if (completionStatus == 1) {
                // Increment count for completed videos
                completedVideos++;
            } else {
                // Decrement count for completed videos
                completedVideos--;
            }

            // Update the displayed count on the page
            completedVideosCountElement.innerHTML = completedVideos;

            // Recalculate and update completed percentage
            var totalVideos = <?php echo count($allLessonTitles); ?>;
            var completedPercentage = (completedVideos / totalVideos) * 100;
            document.getElementById('completedPercentage').innerHTML = Math.round(completedPercentage) + '%';

            // Update the width of the progress bar
            var skillBar = document.querySelector('.skill-bar');
            skillBar.style.width = completedPercentage + '%';
        }
    };

    // Send the AJAX request with form data
    xhr.send(formData);
}




// Function to update completed percentage and videos count
function updateCompletedStats() {
    // Trigger AJAX request to get updated completed stats
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'get_completion_info.php', true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            // Parse the JSON response
            var response = JSON.parse(xhr.responseText);

            // Update completed percentage and videos count on the page
            document.getElementById('completedVideosCount').innerHTML = response.completedVideos;
            document.getElementById('completedPercentage').innerHTML = response.completedPercentage + '%';
        }
    };

    // Send the AJAX request
    xhr.send();
}




// Call the function with index 0 on page load
window.onload = function () {
    changeMainVideo(0);
};

</script>
</body>
</html>

