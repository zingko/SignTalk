<?php
// Include database configuration file
session_start();
include 'dbConn.php';


if (!isset($_SESSION["user"])) {
    header("Location: login.php");
}
// Fetch User Data with pagination
$entries_per_page = 10;
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($current_page - 1) * $entries_per_page;

$sql = "SELECT courseID FROM course LIMIT $offset, $entries_per_page";
$result = $conn->query($sql);
if (!$result) {
    // If there is an error in the query, display the error message
    die("Error: " . $conn->error);
}
// Get the total number of rows in the user table
$sqlCount = "SELECT COUNT(*) as total FROM course";
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
	$('#getCourse').on("keyup", function(){
    var getCourse = $(this).val();
    $.ajax({
        method:'POST',
        url:'action.php',
        data:{course:getCourse},
        success:function(response)
        {
            $("#showdata").html(response);
        } 
    });
});

	$('#editEmployeeModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var courseID = button.data('courseid');
            var courseName = button.data('coursename');
            var courseDescription = button.data('coursedescription');
            var type = button.data('type');
            var courseLevel = button.data('courselevel');
            var duration_hours = button.data('duration_hours');
            var video_count = button.data('video_count');




            $('#edit_courseID').val(courseID);
            $('#edit_courseID_display').val(courseID);
            $('#edit_courseName').val(courseName);
            $('#edit_courseDescription').val(courseDescription);
            $('#edit_type').val(type);
            $('#edit_courseLevel').val(courseLevel);
            $('#edit_duration_hours').val(duration_hours);
            $('#edit_video_count').val(video_count);



            });
    
	$('#deleteEmployeeModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var courseID = button.data('courseid');
    $('#delete_courseID').val(courseID);
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
        <form action="courseManagement.php" method="post" id="deleteForm">
		<div class="table-responsive">
			<div class="table-wrapper">
				<div class="table-title">
					<div class="row">
						<div class="col-xs-6">
							<h2>Manage <b>Course</b></h2>
						</div>
						<div class="col-xs-6">
							<a href="#addEmployeeModal" class="btn btn-success" data-toggle="modal"><i class="material-icons">&#xE147;</i> <span>Add New Course</span></a>
						</div>
					</div>
				</div>
				<table class="table table-striped table-hover">
										    <div class="form-inline search-bar search-form d-flex align-items-center"> 
    <input type="text" id="getCourse" class ="rounded" placeholder="Search Course/ID/Name" style="color: black;"/>
</div><!-- End Search Bar -->
					<thead>
						<tr>
                            <th>Course ID</th>
							<th>Course Name</th>
							<th>Course Level</th>
                            <th>Course Description</th>
                            <th>Type</th>
							<th>Duration (MINS)</th>
                            <th>Video Count</th>
							<th>Actions</th>
						</tr>
					</thead>
										<tbody id="showdata">
					    <?php
                            $sql = "SELECT * FROM `course` LIMIT $offset, $entries_per_page";

							$result = mysqli_query($conn,$sql);
							if (!$result) {
								die("Query failed: " . mysqli_error($conn));
							}
							while($row = mysqli_fetch_assoc($result)){
							?>
							
					
<tr>
                            <td><?php echo $row['courseID'] ?></td>
                            <td><?php echo $row['courseName'] ?></td>
                            <td><?php echo $row['courseLevel'] ?></td>
                            <td><?php echo $row['courseDescription'] ?></td>
                            <td><?php echo $row['type'] ?></td>
                            <td><?php echo $row['duration_hours'] ?></td>
                            <td><?php echo $row['video_count'] ?></td>
							<td>
                                
                                <a href="#editEmployeeModal" class="edit" data-toggle="modal" 
                                    data-courseid="<?php echo $row['courseID']; ?>"
                                    data-coursename="<?php echo $row['courseName']; ?>"
                                    data-coursedescription="<?php echo $row['courseDescription']; ?>"
                                    data-courselevel="<?php echo $row['courseLevel']; ?>"
                                    data-duration_hours="<?php echo $row['duration_hours']; ?>"
                                    data-video_count="<?php echo $row['video_count']; ?>"
                                    data-type="<?php echo $row['type']; ?>"><i class="material-icons" data-toggle="tooltip" title="Edit">&#xE254;</i>                                
                                <a href="#deleteEmployeeModal" class="delete" data-toggle="modal"  
                                    data-courseid="<?php echo $row['courseID']; ?>"><i class="material-icons" data-toggle="tooltip" title="Delete">&#xE872;</i></a>
                                <a href="viewCourse.php?courseID=<?= $row['courseID']; ?>" class="btn btn-success" style="color: white;">view course</a>
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
		        </form>
    </div>
    
        
	<!-- Add New Course Modal HTML -->
	<div id="addEmployeeModal" class="modal fade">

		<div class="modal-dialog">
			<div class="modal-content">
	        <form method="post" action="courseManagement.php">
					<div class="modal-header">						
						<h4 class="modal-title">Add Course</h4>
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					</div>
					<div class="modal-body">	
                        <div class="form-group">
							<label>Course ID</label>
							<?php
                        include "dbConn.php";
                        
                        // Fetch the maximum signID from the dic table
                        $sqlMaxCourseID = "SELECT MAX(courseID) AS maxCourseID FROM course";
                        $resultMaxCourseID = mysqli_query($conn, $sqlMaxCourseID);
                        
                        if ($rowMaxCourseID = mysqli_fetch_assoc($resultMaxCourseID)) {
                            // Increment the maximum signID by 1
                            $newCourseID = $rowMaxCourseID['maxCourseID'] + 1;
                        } else {
                            // If there are no existing records, start with 1
                            $newCourseID = 1;
                        }
                        ?>
							<input type="text" class="form-control" name= "courseID" value="<?php echo $newCourseID; ?>" disabled>
						</div>
						<div class="form-group">
							<label>Course Name</label>
							<input type="text" class="form-control" name="courseName" required>
						</div>
						<div class="form-group">
							<label>Course Level</label><br>
                                <select id="level" name="courseLevel">
                                   <option value="Beginner">Beginner</option>
                                   <option value="Intermediate">Intermediate</option>
                                   <option value="Advanced">Advanced</option>
                               </select>						
                        </div>
                        <div class="form-group">
							<label>Course Description</label>
							<input type="text" class="form-control" name="courseDescription" required>
						</div>
						<div class="form-group">
                                <label>Type</label><br>
                                <select id="type" name="type">
                                   <option value="Free">Free</option>
                                   <option value="Premium">Premium</option>
                               </select>
                               </div>
                            <div class="form-group">
                                <label>Duration (mins)</label>
                                <input type="text" class="form-control" name="duration_hours" required>
                            </div>
                            <div class="form-group">
                                <label>Video Count</label>
                                <input type="text" class="form-control" name="video_count" required>
                            </div>
					</div>
				<div class="modal-footer">
            <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
            <input type="submit" name="addCourse"class="btn btn-success" value="Add">
        </div>
				</form>
			</div>
		</div>
	</div>
	<!-- Edit Modal HTML -->
	<div id="editEmployeeModal" class="modal fade">
	        <?php
    if (isset($_GET['courseID'])) {
        require 'dbConn.php';
        $courseID = mysqli_real_escape_string($conn, $_GET['courseID']);

        $details_query = "SELECT * FROM course WHERE courseID = '".$courseID."'";
        $details_result = mysqli_query($conn, $details_query);
        $row = mysqli_fetch_array($details_result);
    }
    ?>
		<div class="modal-dialog">
			<div class="modal-content">
	        <form method="post" action="courseManagement.php">
	                                <input type="hidden" name="courseID" id="edit_courseID" value="<?php echo $courseID; ?>">
					<div class="modal-header">						
                        <h4 class="modal-title">Edit Lesson</h4>
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					</div>
					<div class="modal-body">	
                        <div class="form-group">
							<label>Course ID</label>
							<input type="text" class="form-control" id="edit_courseID_display" name="courseID" value="<?php echo $row['courseID']; ?>" disabled>
						</div>
						<div class="form-group">
							<label>Course Name</label>
							<input type="text" class="form-control" id="edit_courseName" name="courseName"  value ="<?php echo $row['courseName']; ?>" required>
						</div>
						<div class="form-group">
						    <label>Course Level</label><br>
						    <select id="edit_courseLevel" name="courseLevel">
						        <option value="Beginner" <?php echo (isset($row['level']) && $row['level'] == 'Beginner') ? 'selected' : ''; ?>>Beginner</option>
						        <option value="Intermediate" <?php echo (isset($row['level']) && $row['level'] == 'Intermediate') ? 'selected' : ''; ?>>Intermediate</option>
						        <option value="Advanced" <?php echo (isset($row['level']) && $row['level'] == 'Advanced') ? 'selected' : ''; ?>>Advanced</option>
						    </select>
						  </div>
                        <div class="form-group">
							<label>Course Description</label>
							<input type="text" class="form-control" id="edit_courseDescription" name="courseDescription" value ="<?php echo $row['courseDescription']; ?>"required>
						</div>
						<div class="form-group">
						    <label>Type</label><br>
						    <select id="edit_type" name="type">
						        <option value="Free" <?php echo (isset($row['type']) && $row['type'] == 'Free') ? 'selected' : ''; ?>>Free</option>
						        <option value="Premium" <?php echo (isset($row['type']) && $row['type'] == 'Premium') ? 'selected' : ''; ?>>Premium</option>
						    </select>
						  </div>
                            <div class="form-group">
                                <label>Duration (mins)</label>
                                <input type="text" class="form-control" id="edit_duration_hours" name="duration_hours"value ="<?php echo $row['duration_hours']; ?>"required>
                            </div>
                            <div class="form-group">
                                <label>Video Count</label>
                                <input type="text" class="form-control" id="edit_video_count" name="video_count" value ="<?php echo $row['video_count']; ?>"required>
                            </div>
					</div>
					<div class="modal-footer">
						<input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
						<input type="submit" class="btn btn-info" value="Save" name="editCourse">
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- Delete Modal HTML -->
	<div id="deleteEmployeeModal" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
	        <form method="post" action="courseManagement.php">
					<div class="modal-header">						
						<h4 class="modal-title">Delete Course</h4>
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					</div>
					<div class="modal-body">					
						<p>Are you sure you want to delete these Records?</p>
						<p class="text-warning"><small>This action cannot be undone.</small></p>
					</div>
					<div class="modal-footer">
					    <input type="hidden" name="deleteCourseID" id="delete_courseID" value="">
						<input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
						<input type="submit" class="btn btn-danger" value="Delete" name="delCourse">
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