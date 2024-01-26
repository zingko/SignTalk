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
    q.questionID,
    q.quizID,
    q.questionDescription,
    q.option_a,
    q.option_b,
    q.option_c,
    q.option_d,
    q.answer,
    q.questionMedia,
    quiz.quizTitle
FROM
    quiz
JOIN
    question q ON quiz.quizID = q.quizID
LIMIT $offset, $entries_per_page";


$result = $conn->query($sql);
if (!$result) {
    // If there is an error in the query, display the error message
    die("Error: " . $conn->error);
}

// Get the total number of rows in the user table
$sqlCount = "SELECT COUNT(*) as total FROM question";
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
<title>Manage Question</title>
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
	$('#getQuestion').on("keyup", function(){
    var getQuestion = $(this).val();
    $.ajax({
        method:'POST',
        url:'action.php',
        data:{question:getQuestion}, // Change 'sign' to 'question'
        success:function(response)
        {
            $("#showdata").html(response);
        } 
    });
});
$('#editEmployeeModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var questionID = button.data('questionid');
            var questionDescription = button.data('questiondescription');
            var questionMedia = button.data('questionMedia');
            var answer = button.data('answer');
            var quizID = button.data('quizid');
            var option_a = button.data('option_a');
            var option_b = button.data('option_b');
            var option_c = button.data('option_c');
            var option_d = button.data('option_d');



            $('#edit_questionID').val(questionID);
            $('#edit_questionID_display').val(questionID);
            $('#edit_questionDescription').val(questionDescription);
            $('#edit_questionMedia').val(questionMedia);
            $('#edit_answer').val(answer);
            $('#edit_quizID').val(quizID);
            $('#edit_option_a').val(option_a);
            $('#edit_option_b').val(option_b);
            $('#edit_option_c').val(option_c);
            $('#edit_option_d').val(option_d);





            });
    
	$('#deleteEmployeeModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var questionID = button.data('questionid');
    $('#delete_questionID').val(questionID);
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
        <form action="questionManagement.php" method="post" id="deleteForm">
		<div class="table-responsive">
			<div class="table-wrapper">
				<div class="table-title">
					<div class="row">
						<div class="col-xs-6">
							<h2>Manage <b>Question</b></h2>
						</div>
						<div class="col-xs-6">
							<a href="#addEmployeeModal" class="btn btn-success" data-toggle="modal"><i class="material-icons">&#xE147;</i> <span>Add New Question</span></a>
						</div>
					</div>
					
				</div>
				<table class="table table-striped table-hover">
				    <div class="form-inline search-bar search-form d-flex align-items-center"> 
    <input type="text" id="getQuestion" class="rounded" placeholder="Search Question/Course/Quiz" style="color: black;"/>
</div><!-- End Search Bar -->
					<thead>
						<tr>
                            <th>Quiz ID</th>
                            <th>Quiz name</th>
                            <th>Question ID</th>
                            <th>Question</th>
                            <th>Question Media</th>
							<th>Option A</th>
                            <th>Option B</th>
							<th>Option C</th>
							<th>Option D</th>
                            <th>Answer</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody id="showdata">
						<?php
                        while($row = mysqli_fetch_assoc($result)){
                        ?>
                        <tr>
                            <td><?php echo $row['quizID'] ?></td>
                            <td><?php echo $row['quizTitle'] ?></td>
                            <td><?php echo $row['questionID'] ?></td>
                            <td><?php echo $row['questionDescription'] ?></td>
                            <td><?php echo $row['questionMedia'] ?></td>
                            <td><?php echo $row['option_a'] ?></td>
                            <td><?php echo $row['option_b'] ?></td>
                            <td><?php echo $row['option_c'] ?></td>
                            <td><?php echo $row['option_d'] ?></td>
                            <td><?php echo $row['answer'] ?></td>
                            <td>
<a href="#editEmployeeModal" class="edit" data-toggle="modal" data-questionid="<?php echo $row['questionID']; ?>"data-questiondescription="<?php echo $row['questionDescription']; ?>"data-quizid="<?php echo $row['quizID']; ?>"data-answer="<?php echo $row['answer']; ?>"data-option_a="<?php echo $row['option_a']; ?>"data-option_b="<?php echo $row['option_b']; ?>"data-option_c="<?php echo $row['option_c']; ?>"data-option_d="<?php echo $row['option_d']; ?>"data-questionMedia="<?php echo $row['questionMedia']; ?>"><i class="material-icons" data-toggle="tooltip" title="Edit">&#xE254;</i>
<a href="#deleteEmployeeModal" class="delete" data-toggle="modal"  data-questionid="<?php echo $row['questionID']; ?>"><i class="material-icons" data-toggle="tooltip" title="Delete">&#xE872;</i></a>
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
                <form action="questionManagement.php" method="POST" enctype="multipart/form-data">
					<div class="modal-header">						
						<h4 class="modal-title">Add Question</h4>
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					</div>
					<div class="modal-body">
                        <div class="form-group">
                            <label>Select Quiz</label>
                             <select class="form-control" name="quizID" required>
                            <?php
                                // Fetch Course ID and Course Name from the database
                                $sqlQuizzes = "SELECT quizID, quizTitle FROM quiz";
                                $resultQuizzes = $conn->query($sqlQuizzes);
                                
                                if ($resultQuizzes->num_rows > 0) {
                                    while ($rowQuiz = $resultQuizzes->fetch_assoc()) {
                                        echo '<option value="' . $rowQuiz['quizID'] . '">' . $rowQuiz['quizTitle'] . '</option>';
                                    }
                                }
                            ?>
                        </select>
                        </div>
                        <div class="form-group">
                            <label>Question ID</label>
                            <?php
                            include "dbConn.php";
                            
                            // Fetch the maximum lessonID from the lesson table
                            $sqlMaxQuestionID = "SELECT MAX(questionID) AS maxQuestionID FROM question";
                            $resultMaxQuestionID = mysqli_query($conn, $sqlMaxQuestionID);
                            
                            if ($rowMaxQuestionID = mysqli_fetch_assoc($resultMaxQuestionID)) {
                                // Increment the maximum lessonID by 1
                                $newQuestionID = $rowMaxQuestionID['maxQuestionID'] + 1;
                            } else {
                                // If there are no existing records, start with 1
                                $newQuestionID = 1;
                            }
                            ?>
                            <input type="text" class="form-control" name="questionID" value="<?php echo $newQuestionID; ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label>Question Description</label>
                            <input type="text" class="form-control" name="questionDescription" required>
                        </div>
                        <div class="form-group">
                            <label>Question Media</label>
                                <input class="form-control" type="file" id="img" name="questionMedia" accept="image/*,video/*" required>
                        </div>
                        <div class="form-group">
							<label>A</label><br>
                            <input type="text" class="form-control" name="option_a" required>

						</div>	
                        <div class="form-group">
							<label>B</label><br>
                            <input type="text" class="form-control" name="option_b" required>
						</div>		
                        <div class="form-group">
							<label>C</label><br>
                            <input type="text" class="form-control" name="option_c" required>
						</div>	
                        <div class="form-group">
							<label>D</label><br>
                            <input type="text" class="form-control" name="option_d" required>
						</div>	
						<div class="form-group">
							<label>Answer</label><br>
                            <input type="text" class="form-control" name="answer" required>
						</div>	
					</div>
					<div class="modal-footer">
						<input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
						<input type="submit" class="btn btn-success" value="Add" name ="addQuestion">
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- Edit Modal HTML -->
	<div id="editEmployeeModal" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
                <form action="questionManagement.php" method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="questionID" id="edit_questionID" value="<?php echo $questionID; ?>">

					<div class="modal-header">						
						<h4 class="modal-title">Edit Question</h4>
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					</div>
					<div class="modal-body">	
                        <div class="form-group">
                            <label>Select Quiz</label>
                             <select class="form-control" name="quizID" id="edit_quizID" required>
                            <?php
                                // Fetch Course ID and Course Name from the database
                                $sqlQuizzes = "SELECT quizID, quizTitle FROM quiz";
                                $resultQuizzes = $conn->query($sqlQuizzes);
                                
                                if ($resultQuizzes->num_rows > 0) {
                                    while ($rowQuiz = $resultQuizzes->fetch_assoc()) {
                                        echo '<option value="' . $rowQuiz['quizID'] . '">' . $rowQuiz['quizTitle'] . '</option>';
                                    }
                                }
                            ?>
                        </select>
                        </div>
                        <div class="form-group">
                            <label>Question ID</label>
                            <input type="text" class="form-control"name="questionID" id="edit_questionID_display" value="<?php echo $row['questionID']; ?>" disabled>
                        </div>
						<div class="form-group">
                            <label>Question Description</label>
                            <input type="text" class="form-control" name="questionDescription" id="edit_questionDescription" value="<?php echo $row['questionDescription']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Question Media</label>
                                <input class="form-control" type="file" id="edit_questionMedia" name="questionMedia" accept="image/*,video/*" value="<?php echo $row['questionMedia']; ?>">
                                <small>If you want to keep the existing video, leave this field empty.</small>
                        </div>
                        <div class="form-group">
							<label>A</label><br>
                            <input type="text" class="form-control" name="option_a" id="edit_option_a" value="<?php echo $row['option_a']; ?>"required>

						</div>	
                        <div class="form-group">
							<label>B</label><br>
                            <input type="text" class="form-control" name="option_b" id="edit_option_b" value="<?php echo $row['option_b']; ?>" required>
						</div>		
                        <div class="form-group">
							<label>C</label><br>
                            <input type="text" class="form-control" name="option_c" id="edit_option_c" value="<?php echo $row['option_c']; ?>" required>
						</div>	
                        <div class="form-group">
							<label>D</label><br>
                            <input type="text" class="form-control" name="option_d" id="edit_option_d" value="<?php echo $row['option_d']; ?>" required>
						</div>	
						<div class="form-group">
							<label>Answer</label><br>
                            <input type="text" id="edit_answer"class="form-control" name="answer" required>
						</div>	
					</div>
					<div class="modal-footer">
						<input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
						<input type="submit" class="btn btn-info" value="Save" name="editQuestion">
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- Delete Modal HTML -->
	<div id="deleteEmployeeModal" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
                <form action="questionManagement.php" method="POST" enctype="multipart/form-data">
					<div class="modal-header">						
						<h4 class="modal-title">Delete Question</h4>
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					</div>
					<div class="modal-body">					
						<p>Are you sure you want to delete these Records?</p>
						<p class="text-warning"><small>This action cannot be undone.</small></p>
					</div>
					<div class="modal-footer">
					    <input type="hidden" name="deleteQuestionID" id="delete_questionID" value="">

						<input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
						<input type="submit" class="btn btn-danger" value="Delete" name="delQuestion">
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
