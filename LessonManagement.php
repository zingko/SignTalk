
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

$sql = "SELECT l.lessonID, l.courseID, l.lessonTitle, l.video_url,c.courseName 
        FROM lesson l
        JOIN course c ON l.courseID = c.courseID
        LIMIT $offset, $entries_per_page";
$result = $conn->query($sql);
if (!$result) {
    // If there is an error in the query, display the error message
    die("Error: " . $conn->error);
}
// Get the total number of rows in the user table
$sqlCount = "SELECT COUNT(*) as total FROM lesson";
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
    <title>Manage Lesson</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/manageDic.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script>

        $(document).ready(function(){
            // Activate tooltip
            $('[data-toggle="tooltip"]').tooltip();
            $('#getLesson').on("keyup", function(){
    var getLesson = $(this).val();
    $.ajax({
        method:'POST',
        url:'action.php',
        data:{lesson:getLesson},
        success:function(response)
        {
            $("#showdata").html(response);
        } 
    });
});

    
	$('#deleteEmployeeModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var lessonID = button.data('lessonid');
    $('#delete_lessonID').val(lessonID);
});
        });
    </script>
    <script>
// Use event delegation to handle click events on dynamically added elements
$(document).on('click', '.editLesson', function() {
    var lessonID = $(this).data('lessonid');
    var lessonTitle = $(this).data('lessontitle');
    var video_url = $(this).data('video_url');
    var courseID = $(this).data('courseid');
    
    // Open the edit modal and populate data
    $('#editEmployeeModal').modal('show');
    $('#edit_lessonID').val(lessonID);
    $('#edit_lessonID_display').val(lessonID);
    $('#edit_lessontitle').val(lessonTitle);
    $('#edit_video_url').val(video_url);
    $('#edit_courseID').val(courseID);
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
        <form action="addLesson.php" method="post" id="deleteForm">
        <?php
        if(isset($_GET['msg'])){
            $msg = $_GET['msg'];
            echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                    '.$msg.'
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
                </div>';
        }
        ?>
        <div class="table-responsive">
            <div class="table-wrapper">
                <div class="table-title">
                    <div class="row">
                        <div class="col-xs-6">
                            <h2>Manage <b>Lesson</b></h2>
                        </div>
                        <div class="col-xs-6">
                            <a href="#addEmployeeModal" class="btn btn-success" data-toggle="modal"><i class="material-icons">&#xE147;</i> <span>Add New Lesson</span></a>
                        </div>
                    </div>
                                    </div>
                <table class="table table-striped table-hover">
                    <div class="form-inline search-bar search-form d-flex align-items-center"> 
    <input type="text" id="getLesson" class="rounded" placeholder="Search Lesson/Course" style="color: black;"/>
</div><!-- End Search Bar -->
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th>Lesson ID</th>
                            <th>Lesson Title</th>
                            <th>Lesson Video</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="showdata">
                        <?php
                        while($row = mysqli_fetch_assoc($result)){
                        ?>
                        <tr>
                            <td><?php echo $row['courseName'] ?></td>
                            <td><?php echo $row['lessonID'] ?></td>
                            <td><?php echo $row['lessonTitle'] ?></td>
                            <td><?php echo $row['video_url'] ?></td>
                            <td>
<a href="#editEmployeeModal" class="editLesson" data-toggle="modal" data-lessonid="<?php echo $row['lessonID']; ?>"data-lessontitle="<?php echo $row['lessonTitle']; ?>"data-courseid="<?php echo $row['courseID']; ?>"data-description="<?php echo $row['description']; ?>"><i class="material-icons" data-toggle="tooltip" title="Edit">&#xE254;</i>                                
<a href="#deleteEmployeeModal" class="delete" data-toggle="modal"  data-lessonid="<?php echo $row['lessonID']; ?>"><i class="material-icons" data-toggle="tooltip" title="Delete">&#xE872;</i></a>
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
    <!-- Add Modal HTML -->
    <div id="addEmployeeModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="addLesson.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-header">                        
                        <h4 class="modal-title">Add Lesson</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    </div>
                    <div class="modal-body">  
                    <div class="form-group">
                            <label>Select Course ID</label>
                             <select class="form-control" name="courseID" required>
                            <?php
                                // Fetch Course ID and Course Name from the database
                                $sqlCourses = "SELECT courseID, courseName FROM course";
                                $resultCourses = $conn->query($sqlCourses);
                                
                                if ($resultCourses->num_rows > 0) {
                                    while ($rowCourse = $resultCourses->fetch_assoc()) {
                                        echo '<option value="' . $rowCourse['courseID'] . '">' . $rowCourse['courseName'] . '</option>';
                                    }
                                }
                            ?>
                        </select>
                        </div>
                        <div class="form-group">
                            <label>Lesson ID</label>
                            <?php
                            include "dbConn.php";
                            
                            // Fetch the maximum lessonID from the lesson table
                            $sqlMaxLessonID = "SELECT MAX(lessonID) AS maxLessonID FROM lesson";
                            $resultMaxLessonID = mysqli_query($conn, $sqlMaxLessonID);
                            
                            if ($rowMaxLessonID = mysqli_fetch_assoc($resultMaxLessonID)) {
                                // Increment the maximum lessonID by 1
                                $newLessonID = $rowMaxLessonID['maxLessonID'] + 1;
                            } else {
                                // If there are no existing records, start with 1
                                $newLessonID = 1;
                            }
                            ?>
                            <input type="text" class="form-control" name="lessonID" value="<?php echo $newLessonID; ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label>Lesson Title</label>
                            <input type="text" class="form-control" name="lessonTitle" required>
                        </div>
                        <div class="form-group">
                            <label>Lesson Video URL</label>
                               <input class="form-control" type="file" id="formFile" name="video_url" placeholder="video.mp4" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
                        <input type="submit" class="btn btn-success" value="Add" name="addLesson">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal HTML -->
    <div id="editEmployeeModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="addLesson.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="lessonID" id="edit_lessonID" value="<?php echo $lessonID; ?>">

                    <div class="modal-header">                        
                        <h4 class="modal-title">Edit Lesson</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    </div>
                    <div class="modal-body"> 
                    <div class="form-group">
                            <label>Select Course ID</label>
                             <select class="form-control" name="courseID" id="edit_courseID"required>
                            <?php
                                // Fetch Course ID and Course Name from the database
                                $sqlCourses = "SELECT courseID, courseName FROM course";
                                $resultCourses = $conn->query($sqlCourses);
                                
                                if ($resultCourses->num_rows > 0) {
                                    while ($rowCourse = $resultCourses->fetch_assoc()) {
                                        echo '<option value="' . $rowCourse['courseID'] . '">' . $rowCourse['courseName'] . '</option>';
                                    }
                                }
                            ?>
                        </select>
                        </div>
                        <div class="form-group">
                            <label>Lesson ID</label>
                            <input type="text" class="form-control"name="lessonID" id="edit_lessonID_display" value="<?php echo $row['lessonID']; ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label>Lesson Title</label>
                            <input type="text" class="form-control" name="lessonTitle" id="edit_lessontitle" value="<?php echo $row['lessonTitle']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Lesson Video URL</label>
                            <input type="file" class="form-control" name="video_url" id="edit_video_url" value="<?php echo $row['video_url']; ?>">
                                                    <small>If you want to keep the existing video, leave this field empty.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
                        <input type="submit" class="btn btn-info" value="Save" name="editLesson">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Modal HTML -->
    <div id="deleteEmployeeModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="addLesson.php" method="post">
                    <div class="modal-header">
                        <h4 class="modal-title">Delete Lesson</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete these Records?</p>
                        <p class="text-warning"><small>This action cannot be undone.</small></p>
                   
					</div>
					<div class="modal-footer">
                        <input type="hidden" name="deleteLessonID" id="delete_lessonID" value="">
						<input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
						<input type="submit" class="btn btn-danger" value="Delete" name ="delLesson">
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

