<?php
session_start();
include 'dbConn.php';

// Check if 'userID' is set in the session
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION['userID'];

// Fetch user data from the database
$sql = "SELECT * FROM user WHERE userID = $userID";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $userData = $result->fetch_assoc();

    // Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Update username, email, and profile picture
    if (isset($_POST['update_profile'])) {
        $newName = isset($_POST['newName']) ? $_POST['newName'] : '';
        $newEmail = isset($_POST['newEmail']) ? $_POST['newEmail'] : '';

        // Update profile picture
        if (!empty($_FILES['update_image']['name'])) {
            $target_dir = "images/";
            $target_file = $target_dir . basename($_FILES['update_image']['name']);
            move_uploaded_file($_FILES['update_image']['tmp_name'], $target_file);

            $updateImageSql = "UPDATE user SET userPic = '$target_file' WHERE userID = $userID";
            $conn->query($updateImageSql);
        }

        // Update username and email
        $updateUserSql = "UPDATE user SET username = '$newName', userEmail = '$newEmail' WHERE userID = $userID";
        $conn->query($updateUserSql);
        // Update the session variable with the new username
            $_SESSION['username'] = $newName;
        $_SESSION['success_message'] = 'Updated successfully.';
            header("Location: userPro.php");
            exit();
    }

// Update password
if (isset($_POST['update_password'])) {
    $oldPassword = $_POST['oldPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    // Verify old password
    if (password_verify($oldPassword, $userData['password'])) {
        // Check if new password and confirm password match
        if ($newPassword === $confirmPassword) {
            // Check if the new password meets the minimum length requirement
            if (strlen($newPassword) >= 8) {
                // Update password
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $updatePasswordSql = "UPDATE user SET password = '$hashedPassword' WHERE userID = $userID";
                $conn->query($updatePasswordSql);
                
                $_SESSION['success_message'] = 'Updated successfully.';
                header("Location: userPro.php");
                exit();
            } else {
                echo '<div class="alert alert-danger" role="alert">New password must have at least 8 characters.</div>';
            }
        } else {
            echo '<div class="alert alert-danger" role="alert">New password and confirm password do not match.</div>';
        }
    } else {
        echo '<div class="alert alert-danger" role="alert">Incorrect old password. Please enter the correct old password.</div>';
    }
}
}

} else {
    // Handle the case where user data is not found
    echo '<div class="alert alert-danger" role="alert">
            User data not found. Please contact support.
          </div>';
    // Optionally, you can redirect the user to an error page
    // header("Location: error.php");
    // exit();
}
// Fetch subscription data from the subscriptions table
$subscriptionSql = "SELECT * FROM subscriptions WHERE userID = $userID";
$subscriptionResult = $conn->query($subscriptionSql);

// Initialize variables
$isFreeSubscription = false;
$subscriptionType = '';

// Check if the query was successful
if ($subscriptionResult === false) {
    echo "Error executing the SQL query: " . $conn->error;
} else {
    // Check if any subscription data is found
    if ($subscriptionResult->num_rows > 0) {
        $subscriptionData = $subscriptionResult->fetch_assoc();
        $isFreeSubscription = $subscriptionData['free'];
        $subscriptionType = $subscriptionData['type'];
                $endDate = $subscriptionData['endDate'];

    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="css/style.css">
    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link href="js/bootstrap.min.css" rel="stylesheet">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/userPro.css"></head>
<body>

<header>

        <a href="index.php"><img src="images/silence.png" alt="" width="200" height="50"></a>

    <div id="menu" class="fas fa-bars"></div>
     <div class="navbar">
  <a href="index.php">Home</a>
  <a href="dic.php">Dictionary</a>
  <div class="dropdown1">
    <button class="dropbtn1">Course
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-content1">
      <a href="beginner.php">Beginner</a>
      <a href="intermediate.php">Intermediate</a>
      <a href="advanced.php">Advanced</a>
    </div>
  </div>
                 <a href="price.php">price</a>
 <?php
      
            echo '<div class="dropdown1">
                    <button class="dropbtn1">' . $_SESSION['username'] . '
                        <i class="fa fa-caret-down"></i>
                    </button>
                    <div class="dropdown-content1">
                        <a href="userPro.php">Profile</a>
                        <a href="logout.php">Logout</a>
                    </div>
                  </div>';
        
        ?>

</header>
<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success" role="alert">
        <?php echo $_SESSION['success_message']; ?>
    </div>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<div class="container1">
    <section class="py-5 my-5" height="80%">
            <h1 class="mb-5">User Profile</h1>
            <div class="bg-white shadow rounded-lg d-block d-sm-flex">
                <div class="profile-tab-nav border-right">
<div class="p-4">
						<div class="img-circle text-center mb-3">
							<img src="<?php echo $userData['userPic']; ?>" alt="Image" class="shadow">
						</div>
<?php echo '<h4 class="text-center">' . $_SESSION['username'] . '</h4>'; ?>
					</div>
					<div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical"style="font-size: 20px;">
						<a class="nav-link active" id="account-tab" data-toggle="pill" href="#account" role="tab" aria-controls="account" aria-selected="true">
							<i class="fa fa-home text-center mr-1"></i> 
							Account
						</a>
						<a class="nav-link" id="password-tab" data-toggle="pill" href="#password" role="tab" aria-controls="password" aria-selected="false">
							<i class="fa fa-key text-center mr-1"></i> 
							Password
						</a>
					<a class="nav-link" id="security-tab" data-toggle="pill" href="#security" role="tab" aria-controls="security" aria-selected="false">
							<i class="fa fa-user text-center mr-1"></i> 
							Course
						</a>
					</div>               
					</div>
                <div class="tab-content p-4 p-md-5" id="v-pills-tabContent">
                    <div class="tab-pane fade show active" id="account" role="tabpanel" aria-labelledby="account-tab">
                        <h3 class="mb-4">Account Settings</h3>
                        <form method="post" action="" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>User Name</label>
                                        <input type="text" name="newName" value="<?php echo ($userData['username']); ?>" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Email</label>
<input type="email" class="form-control" name="newEmail" value="<?php echo ($userData['userEmail']); ?>">
                                    
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Update Picture</label>
                                        <input type="file" name="update_image" class="form-control" accept="image/jpg, image/jpeg, image/png">
                                    </div>
                                </div>
                            </div>
                            <div>
                                <button type="submit" name="update_profile" class="btn btn-primary">Update</button>
                                <button class="btn btn-light">Cancel</button>
                            </div>
                        </form>
                    </div>

<div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
						<h3 class="mb-4">Password Settings</h3>
						    <form method="post" action="" enctype="multipart/form-data">

						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
								  	<label>Old password</label>
								  	<input type="password" name="oldPassword" class="form-control" required>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
								  	<label>New password</label>
								  	<input type="password" name="newPassword" class="form-control" required>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
								  	<label>Confirm new password</label>
								  	<input type="password" name="confirmPassword" class="form-control" required>
								</div>
							</div>
						</div>
						<div>
							<button class="btn btn-primary" type="submit" name="update_password" >Update</button>
							<button class="btn btn-light">Cancel</button>
						</div>
						</form>
					</div>
					<div class="tab-pane fade" id="security" role="tabpanel" aria-labelledby="security-tab">
					 <section class="main">
      <div class="main-top">
        <h1>Course</h1>
       <!-- <i class="fas fa-user-cog"></i>-->
      </div>
      <div class="main-skills">
        <div class="card">
    <h3>Subscription</h3>
    <?php
    if ($isFreeSubscription == 1 || $subscriptionType == '') {
    echo '<p>Free</p>';
    echo '<a href="price.php"><button>Subscribe</button></a>';
} else {
    echo '<p>' . $subscriptionType . '</p>';
                                echo '<p>Renews at ' . date('Y-m-d', strtotime($endDate)) . '</p>';
    echo '<a href="https://www.sandbox.paypal.com/signin" target="_blank"><button>Cancel</button></a>';
}
    ?>
</div>
      </div>

      <section class="main-course">
        <h1>My courses</h1>
    <main id="main" class="main">
    <section class="section profile">
      <div class="row">
        <div class="col-xl-16">

          <div class="card">
            <div class="card-body pt-5">
              <!-- Bordered Tabs -->
              <ul class="nav nav-tabs nav-tabs-bordered">

                <li class="nav-item">
                  <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview">In Progress</button>
                </li>

                <li class="nav-item">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit">Quiz Marks</button>
                </li>

                <li class="nav-item">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-settings">Finished</button>
                </li>
              </ul>
              <div class="tab-content pt-2">
                <div class="tab-pane fade show active profile-overview" id="profile-overview">

                 <!-- Start #main -->
      <div class="row mb-3">
        <div class="col-md-8 col-lg-9">

          <div class="card">
            <!-- Fetch and display progress for each lesson -->
<?php
$progressSql = "SELECT  c.courseLevel, c.courseName, 
                SUM(ulc.completion_count) AS totalProgress,
                COUNT(DISTINCT l.lessonID) AS totalLessons
                FROM lesson l
                LEFT JOIN user_lesson_completion ulc ON ulc.lessonID = l.lessonID AND ulc.userID = $userID
                LEFT JOIN course c ON l.courseID = c.courseID
                GROUP BY  c.courseLevel, c.courseName
                HAVING totalProgress > 0 AND totalProgress < totalLessons"; // Filter out 100% progress
$progressResult = $conn->query($progressSql);

if ($progressResult === false) {
    // Handle the SQL query error
    echo "Error executing the SQL query: " . $conn->error;
} else {
    if ($progressResult->num_rows > 0) {
        while ($row = $progressResult->fetch_assoc()) {
            $courseLevel = $row['courseLevel'];
            $courseName = $row['courseName'];
            $totalProgress = $row['totalProgress'];
            $totalLessons = $row['totalLessons'];

            if ($totalLessons > 0) {
                $overallProgress = round(($totalProgress / $totalLessons) * 100);
            } else {
                $overallProgress = 0;
            }

            echo '<div class="card-body">';
            echo '<h5 class="card-title">' . $courseLevel . ' - ' . $courseName . '</h5>';
            echo '<div class="progress">';
            echo '<div class="progress-bar" role="progressbar" style="width: ' . $overallProgress . '%" aria-valuenow="' . $overallProgress . '" aria-valuemin="0" aria-valuemax="100">' . $overallProgress . '%</div>';
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo "<p>No progress data available.</p>";
    }
}
?>



<!-- End Fetch and display progress for each lesson -->

          </div>
        </div>
      </div>
    

 <!-- End #main -->

                </div>

                <div class="tab-pane fade profile-edit pt-3" id="profile-edit">
<!-- Fetch and display progress for each quiz -->
    <?php
    $progressSql = "SELECT q.quizTitle, m.marks, m.quizID FROM marks m
                    JOIN quiz q ON m.quizID = q.quizID
                    WHERE m.userID = $userID";
    $progressResult = $conn->query($progressSql);

    if ($progressResult->num_rows > 0) {
        while ($row = $progressResult->fetch_assoc()) {
            $quizTitle = $row['quizTitle'];
            $marks = $row['marks'];
            $quizID = $row['quizID'];

            // Retrieve the total number of questions for the current quiz
            $totalQuestionsSql = "SELECT COUNT(*) AS totalQuestions FROM question WHERE quizID = $quizID";
            $totalQuestionsResult = $conn->query($totalQuestionsSql);

            if ($totalQuestionsResult && $totalQuestionsResult->num_rows > 0) {
                $totalQuestionsRow = $totalQuestionsResult->fetch_assoc();
                $totalQuestions = $totalQuestionsRow['totalQuestions'];

                echo '<div class="card-body">';
                echo '<h5 class="card-title">' . $quizTitle . ': ' . $marks . ' out of ' . $totalQuestions . '</h5>';
                echo '</div>';
            } else {
                echo '<div class="card-body">';
                echo '<h5 class="card-title">' . $quizTitle . ': ' . $marks . '/0 (N/A)</h5>';
                echo '</div>';
            }
        }
    } else {
        echo "<p>No progress data available.</p>";
    }
    ?>
    <!-- End Fetch and display progress for each quiz -->
                          </div>
                 <!-- End  Edit  -->
                </div>

                <div class="tab-pane fade pt-3" id="profile-settings">

                  <!-- Settings Form -->
                  <form>

                    <div class="row mb-3">
                      <div class="col-md-8 col-lg-9">
                                    <div class="card">

                        <?php
$progressSql = "SELECT c.courseLevel, c.courseName, 
                SUM(ulc.completion_count) AS totalProgress,
                COUNT(DISTINCT l.lessonID) AS totalLessons
                FROM lesson l
                LEFT JOIN user_lesson_completion ulc ON ulc.lessonID = l.lessonID AND ulc.userID = $userID
                LEFT JOIN course c ON l.courseID = c.courseID
                GROUP BY c.courseLevel, c.courseName
                HAVING totalProgress = totalLessons";

$progressResult = $conn->query($progressSql);

if ($progressResult === false) {
    // Handle the SQL query error
    echo "Error executing the SQL query: " . $conn->error;
} else {
    if ($progressResult->num_rows > 0) {
        while ($row = $progressResult->fetch_assoc()) {
            $courseLevel = $row['courseLevel'];
            $courseName = $row['courseName'];
            $totalProgress = $row['totalProgress'];
            $totalLessons = $row['totalLessons'];

            if ($totalLessons > 0) {
                $overallProgress = round(($totalProgress / $totalLessons) * 100);

                echo '<div class="card-body">';
            echo '<h5 class="card-title">' . $courseLevel . ' - ' . $courseName . '</h5>';
                echo '<div class="progress">';
                echo '<div class="progress-bar" role="progressbar" style="width: ' . $overallProgress . '%" aria-valuenow="' . $overallProgress . '" aria-valuemin="0" aria-valuemax="100">' . $overallProgress . '%</div>';
                echo '</div>';
                echo '</div>';
            } else {
                echo '<div class="card">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title">' . $courseName . '</h5>';
                echo '<div class="progress">';
echo '<div class="progress-bar" role="progressbar" style="width: 100%; height: 100%; font-size: 22px;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">100%</div>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
        }
    } else {
        echo "<p>No progress data available.</p>";
    }
}
?>
</div>
                      </div>
                    </div>

                  </form><!-- End settings Form -->

                </div>


              </div><!-- End Bordered Tabs -->

            </div>
          </div>

        </div>
      </div>
    </section>

  </main><!-- End #main -->
      </section>
    </section>
					</div>
                </div>
            </div>
            <!-- footer section  -->


    </section>

<footer class="footer">

    <div class="box-container">

        <div class="box1">
            <h3>about us</h3>
            <p>SignTalk is your gateway to mastering MSL. Let's break down barriers and build bridges of understanding together.</p>
        </div>

        <div class="box1">
            <h3>quick links</h3>
	        <a href="index.php">home</a>
            <a href="dic.php">dictionary</a>
            <a href="beginner.php">course</a>
            <a href="price.php">price</a>
            <a href="login.php">log in</a>
        </div>

        <div class="box1">
            <h3>contact us</h3>
           <p> <i class="fas fa-phone"></i> +123-456-7890 </p>
           <p> <i class="fas fa-envelope"></i> signtalk@gmail.com </p>
        </div>

    </div>

    <div class="credit"> &copy;Copyright <span> SignTalk </span> | all rights reserved | for educational purpose only </div>

</footer>
</div>
  <script src="js/bootstrap.bundle.min.js"></script>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
