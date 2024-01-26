<?php
include 'dbConn.php';
function hasPremiumSubscription($conn, $userID) {
    $sql = "SELECT * FROM subscriptions WHERE userID = ? AND free = 0 AND endDate >= CURDATE()";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userID); // Use "i" for integer, assuming userID is an integer

    $stmt->execute();

    $result = $stmt->get_result();

    if (!$result) {
        die("Error executing the query: " . $conn->error);
    }
    return $result->num_rows > 0;
}



session_start();
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beginner Course</title>
    <!-- font awesome cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- custom css file link -->
    <link rel="stylesheet" href="css/courseLevel.css">
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
<div class="container">

<h1 class="heading"> Beginner </h1>
<p class="coming-soon-text">More courses coming soon</p>


<!-- course section -->
<section class="course">
    
    <?php
// Fetch courses based on user subscription status
if (isset($_SESSION['user'])) {
    $userID = $_SESSION['user']['userID'];
    $subscriptionType = hasPremiumSubscription($conn, $userID) ? "Premium" : "Free";

} else {
    $userID = 0;
    $subscriptionType = "Free"; // Default to Free if the user is not logged in
}

$sql = "SELECT * FROM course WHERE courseLevel = 'Beginner'";
$result = $conn->query($sql);

if ($result === false) {
    die("Error executing course query: " . $conn->error);
}

if ($result->num_rows > 0) {
// Fetch and display progress for each course
$progressSql = "SELECT c.courseID, c.courseName, 
                SUM(ulc.completion_count) AS totalProgress,
                COUNT(DISTINCT l.lessonID) AS totalLessons
                FROM lesson l
                LEFT JOIN user_lesson_completion ulc ON ulc.lessonID = l.lessonID AND ulc.userID = $userID
                LEFT JOIN course c ON l.courseID = c.courseID
                WHERE c.courseLevel = 'Beginner'
                GROUP BY c.courseID, c.courseName";

$progressResult = $conn->query($progressSql);

if ($progressResult === false) {
    // Handle the SQL query error
    echo "Error executing the SQL query: " . $conn->error;
} else {
    // Fetch courses
    $courses = [];
    $courseSql = "SELECT * FROM course WHERE courseLevel = 'Beginner'";
    $courseResult = $conn->query($courseSql);

    if ($courseResult === false) {
        die("Error executing course query: " . $conn->error);
    }

    while ($row = $courseResult->fetch_assoc()) {
        $courses[$row['courseID']] = $row;
    }

    // Display courses and progress
    foreach ($courses as $courseID => $courseDetails) {
        echo '<div class="box">';
        echo '<span class="amount">' . ($courseDetails["type"] === "Premium" && $subscriptionType !== "Premium" ? "Locked" : $courseDetails["type"]) . '</span>';
        echo '<img src="images/course-1.svg" alt="">';
        echo '<h3>' . $courseDetails["courseName"] . '</h3>';
        echo '<p>' . $courseDetails["courseDescription"] . '</p>';
// Check user subscription and display appropriate buttons/messages
    if ($courseDetails["type"] === "Free") {
        echo '<a href="lesson.php?courseID=' . $courseID . '" class="btn">Start Now</a>';
    } elseif ($courseDetails["type"] === "Premium" && $subscriptionType !== "Premium") {
        echo '<p style="color: red;">Upgrade to Premium to access this course.</p>';
        echo '<a href="price.php" class="btn">Upgrade Now</a>';
    } else {
        echo '<a href="lesson.php?courseID=' . $courseID . '" class="btn">Start Now</a>';
    }

    echo '<div class="icons">';
    echo '<p> <i class="far fa-clock"></i> ' . $courseDetails["duration_hours"] . ' min </p>';
    echo '<p> <i class="fas fa-book"></i> ' . $courseDetails["video_count"] . ' videos </p>';
    echo '</div>';
        $progressRow = $progressResult->fetch_assoc();

        if ($progressRow && $progressRow['courseID'] == $courseID) {
            $totalProgress = $progressRow['totalProgress'];
            $totalLessons = $progressRow['totalLessons'];

            $overallProgress = ($totalLessons > 0) ? round(($totalProgress / $totalLessons) * 100) : 0;

         if ($overallProgress > 0) {
            echo '<div class="progress-bar" role="progressbar" style="width: ' . $overallProgress . '%" aria-valuenow="' . $overallProgress . '" aria-valuemin="0" aria-valuemax="100">' . $overallProgress . '%</div>';
            echo '<p> <i class="fas fa-tasks"></i> Progress: ' . $overallProgress . '% </p>';
        }
        }

    echo '</div>';
}
        echo '</div>';
    }
} else {
    echo '<p>No courses available for this level.</p>';
}


// Close the database connection
$conn->close();
?>

</section>

<!-- footer section -->
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

    <div class="credit"> &copy;Copyright <span> SignTalk </span> | all rights reserved | for educational purposes only </div>

</footer>

</div>

<!-- custom js file link -->
<script src="js/script.js"></script>

</body>
</html>

