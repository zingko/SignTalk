<?php
session_start();
include 'dbConn.php';

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
}
$userID = isset($_SESSION['userID']) ? $_SESSION['userID'] : null;

// Fetch User Data with pagination
$entries_per_page = 10;
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($current_page - 1) * $entries_per_page;

$sql = "SELECT 
        q.quizID, 
        q.quizTitle,
        c.courseID,
        c.courseName 
        FROM quiz q
        JOIN course c ON q.courseID = c.courseID
        LIMIT $offset, $entries_per_page";

$result = $conn->query($sql);
if (!$result) {
    // If there is an error in the query, display the error message
    die("Error: " . $conn->error);
}

// Get the total number of rows in the user table
$sqlCount = "SELECT COUNT(*) as total FROM quiz";
$resultCount = $conn->query($sqlCount);
if (!$resultCount) {
    die("Error: " . $conn->error);
}

$rowCount = $resultCount->fetch_assoc();
$total_rows = $rowCount['total'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Manage Quiz</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round">
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<link rel="stylesheet" href="css/manageDic.css">

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<script>
$(document).ready(function(){
	// Activate tooltip
	$('[data-toggle="tooltip"]').tooltip();
$('#getQuiz').on("keyup", function(){
    var getQuiz = $(this).val();
    $.ajax({
        method: 'POST',
        url: 'action.php',
        data: {quiz: getQuiz},
        success: function(response) {
            $("#showdata").html(response);
        } 
    });
});


$('#editEmployeeModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var quizID = button.data('quizid');
            var courseID = button.data('courseid');
            var quizTitle = button.data('quiztitle');
            


            $('#edit_quizID').val(quizID);
            $('#edit_quizID_display').val(quizID);
            $('#edit_quizTitle').val(quizTitle);
            $('#edit_courseID').val(courseID);
            });
    
	$('#deleteEmployeeModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var quizID = button.data('quizid');
    $('#delete_quizID').val(quizID);
});
        });
</script>
</head>
<body>

<header>

        <a href="adminPro.php"><img src="images/silence.png" alt="" width="200" height="50"></a>
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
<?php
// Display success message
if (isset($_SESSION['success_message'])) {
    echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
    // Unset the session variable to clear the message
    unset($_SESSION['success_message']);
}

// Display error message
if (isset($_SESSION['error_message'])) {
    echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
    // Unset the session variable to clear the message
    unset($_SESSION['error_message']);
}
?>
    <div class="container1">

    <div class="container">
		<div class="table-responsive">
			<div class="table-wrapper">
				<div class="table-title">
					<div class="row">
						<div class="col-xs-6">
							<h2>Manage <b>Quiz</b></h2>
						</div>
						<div class="col-xs-6">
							<a href="#addEmployeeModal" class="btn btn-success" data-toggle="modal"><i class="material-icons">&#xE147;</i> <span>Add New Quiz</span></a>
						</div>
					</div>
				</div>
				<table class="table table-striped table-hover">
					<div class="form-inline search-bar search-form d-flex align-items-center"> 
    <input type="text" id="getQuiz" class ="rounded" placeholder="Search Quiz/ID/Title" style="color: black;"/>
</div><!-- End Search Bar -->
					<thead>
						<tr>
							<th>Course ID</th>
                            <th>Course name</th>
                            <th>Quiz ID</th>
                            <th>Quiz name</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody id="showdata">
						<?php
                        while($row = mysqli_fetch_assoc($result)){
                        ?>
                        <tr>
                            <td><?php echo $row['courseID'] ?></td>
                            <td><?php echo $row['courseName'] ?></td>
                            <td><?php echo $row['quizID'] ?></td>
                            <td><?php echo $row['quizTitle'] ?></td>
                            <td>
<a href="#editEmployeeModal" class="editQuiz" data-toggle="modal" data-courseid="<?php echo $row['courseID']; ?>" data-quizid="<?php echo $row['quizID']; ?>" data-quiztitle="<?php echo $row['quizTitle']; ?>"><i class="material-icons" data-toggle="tooltip" title="Edit">&#xE254;</i></a>
<a href="#deleteEmployeeModal" class="delete" data-toggle="modal"  data-quizid="<?php echo $row['quizID']; ?>"><i class="material-icons" data-toggle="tooltip" title="Delete">&#xE872;</i></a>
                            </td>
                        </tr>
                        <?php
                        }
                        ?>
					</tbody>
				</table>
				<!-- Display pagination information -->
				<div class="clearfix">
				    <div class="hint-text">
				    <?php
                    $start_range = ($current_page - 1) * $entries_per_page + 1;
                    $end_range = min($start_range + $entries_per_page - 1, $total_rows);

                    echo 'Showing <b>' . $start_range . ' to ' . $end_range . '</b> out of <b>' . $total_rows . '</b> entries';
                    ?>
                </div>
                <ul class="pagination">
                    <?php
                    // Calculate total pages
                    $total_pages = ceil($total_rows / $entries_per_page);
        
                    // Display "Previous" button
                    if ($current_page > 1) {
                        $prev_page = $current_page - 1;
                        echo '<li class="page-item"><a href="?page=' . $prev_page . '" class="page-link">Previous</a></li>';
                    }
        
                    // Display page numbers
                    for ($i = 1; $i <= $total_pages; $i++) {
                        echo '<li class="page-item ' . ($i == $current_page ? 'active' : '') . '"><a href="?page=' . $i . '" class="page-link">' . $i . '</a></li>';
                    }
        
                    // Display "Next" button
                    if ($current_page < $total_pages) {
                        $next_page = $current_page + 1;
                        echo '<li class="page-item"><a href="?page=' . $next_page . '" class="page-link">Next</a></li>';
                    }
                    ?>
                </ul>
            </div>
			</div>
		</div>        
    </div>
	<!-- Add Modal HTML -->
	<div id="addEmployeeModal" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
                <form action="quizManagement.php" method="POST" enctype="multipart/form-data">
					<div class="modal-header">						
						<h4 class="modal-title">Add Quiz</h4>
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					</div>
					<div class="modal-body">
					    <div class="form-group">
                        <label>Select Course</label>
                        <select class="form-control" name="courseID" required>
                                                        <option value="" disabled selected>No Course Chosen</option>

                            <?php
                            // Fetch Course ID and Course Name from the database
                            $sqlCourses = "SELECT courseID, courseName, courseLevel FROM course";
                            $resultCourses = $conn->query($sqlCourses);

                            if ($resultCourses->num_rows > 0) {
                                while ($rowCourse = $resultCourses->fetch_assoc()) {
                                    // Check if the course is already chosen for a quiz
                                    $sqlChosenCourses = "SELECT DISTINCT courseID FROM quiz";
                                    $resultChosenCourses = $conn->query($sqlChosenCourses);
                                    $chosenCourses = array();
                                    
                                    while ($rowChosenCourse = $resultChosenCourses->fetch_assoc()) {
                                        $chosenCourses[] = $rowChosenCourse['courseID'];
                                    }

                                    // Check if the current course is in the list of chosen courses
                                    $isDisabled = in_array($rowCourse['courseID'], $chosenCourses) ? 'disabled' : '';

                                    // Concatenate courseName and courseLevel in the dropdown options
                                    echo '<option value="' . $rowCourse['courseID'] . '" ' . $isDisabled . '>'
                                         . $rowCourse['courseLevel'] . ' - ' . $rowCourse['courseName'] . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>	
                        <div class="form-group">
                            <label>Quiz ID</label>
                            <?php
                            include "dbConn.php";
                            
                            // Fetch the maximum lessonID from the lesson table
                            $sqlMaxQuizID = "SELECT MAX(quizID) AS maxQuizID FROM quiz";
                            $resultMaxQuizID = mysqli_query($conn, $sqlMaxQuizID);
                            
                            if ($rowMaxQuizID = mysqli_fetch_assoc($resultMaxQuizID)) {
                                // Increment the maximum lessonID by 1
                                $newQuizID = $rowMaxQuizID['maxQuizID'] + 1;
                            } else {
                                // If there are no existing records, start with 1
                                $newQuizID = 1;
                            }
                            ?>
                            <input type="text" class="form-control" name="quizID" value="<?php echo $newQuizID; ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label>Quiz Title</label>
                            <input type="text" class="form-control" name="quizTitle" required>
                        </div>
                        </div>
					<div class="modal-footer">
						<input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
						<input type="submit" class="btn btn-success" value="Add" name ="addQuiz">
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- Edit Modal HTML -->
	<div id="editEmployeeModal" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
                <form action="quizManagement.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="quizID" id="edit_quizID" value="<?php echo $quizID; ?>">

					<div class="modal-header">						
						<h4 class="modal-title">Edit Quiz</h4>
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					</div>
					<div class="modal-body">
					    
    <div class="form-group">
        <label>Select Course</label>
        <select class="form-control" name="courseID" id="edit_courseID" required>
            <!-- Populate the options dynamically based on your data -->
            <?php
            // Fetch Course ID and Course Name from the database
            $sqlCourses = "SELECT courseID, courseName, courseLevel FROM course";
            $resultCourses = $conn->query($sqlCourses);

            if ($resultCourses->num_rows > 0) {
                while ($rowCourse = $resultCourses->fetch_assoc()) {
                    // Check if the current course is the selected one
                    $isSelected = ($rowCourse['courseID'] == $courseID) ? 'selected' : '';

                    // Concatenate courseName and courseLevel in the dropdown options
                    echo '<option value="' . $rowCourse['courseID'] . '" ' . $isSelected . '>'
                        . $rowCourse['courseLevel'] . ' - ' . $rowCourse['courseName'] . '</option>';
                }
            }
            ?>
        </select>
    </div>
                        <div class="form-group">
                            <label>Quiz ID</label>
                            <input type="text" class="form-control"name="quizID" id="edit_quizID_display" value="<?php echo $row['quizID']; ?>" disabled>
                        </div>
						<div class="form-group">
                            <label>Quiz Title</label>
                            <input type="text" class="form-control" name="quizTitle" id="edit_quizTitle" value="<?php echo $row['quizTitle']; ?>" required>
                        </div>
					</div>
					<div class="modal-footer">
						<input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
						<input type="submit" class="btn btn-info" value="Save" name="editQuiz">
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- Delete Modal HTML -->
	<div id="deleteEmployeeModal" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
                <form action="quizManagement.php" method="POST" enctype="multipart/form-data">
					<div class="modal-header">						
						<h4 class="modal-title">Delete Quiz</h4>
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					</div>
					<div class="modal-body">					
						<p>Are you sure you want to delete these Records?</p>
						<p class="text-warning"><small>This action cannot be undone.</small></p>
					</div>
					<div class="modal-footer">
					    <input type="hidden" name="deleteQuizID" id="delete_quizID" value="">
						<input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
						<input type="submit" class="btn btn-danger" value="Delete" name="delQuiz">
					</div>
				</form>
			</div>
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
</body>
</html>
