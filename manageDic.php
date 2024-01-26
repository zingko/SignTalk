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

$sql = "SELECT signID, sign, video FROM dic LIMIT $offset, $entries_per_page";
$result = $conn->query($sql);
if (!$result) {
    // If there is an error in the query, display the error message
    die("Error: " . $conn->error);
}
// Get the total number of rows in the user table
$sqlCount = "SELECT COUNT(*) as total FROM dic";
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
<title>Manage Dictionary</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round">
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<link rel="stylesheet" href="css/manageDic.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<script>
        $(document).ready(function(){
            // Activate tooltip
            $('[data-toggle="tooltip"]').tooltip();
        $('#getSign').on("keyup", function(){
        var getSign = $(this).val();
        $.ajax({
            method:'POST',
            url:'action.php',
            data:{sign:getSign},
            success:function(response)
            {
                $("#showdata").html(response);
            } 
        });
    });



            // Capture signID for edit modal
         //   $('#editEmployeeModal').on('show.bs.modal', function (event) {
          //      var button = $(event.relatedTarget);
           //     var signID = button.data('signid');
           //     $('#edit_signID').val(signID);
           // });
            
            $('#editEmployeeModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var signID = button.data('signid');
        var sign = button.data('sign'); // Add this line
        var video = button.data('video'); // Add this line

        $('#edit_signID').val(signID);
        $('#edit_signID_display').val(signID);
        $('#edit_sign').val(sign); // Populate the sign input
        $('#edit_video').val(video); // Populate the video input
    });

            // Capture signID for delete modal
$('#deleteEmployeeModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var signID = button.data('signid');
    $('#delete_signID').val(signID);
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
							<h2>Manage <b>Dictionary</b></h2>
						</div>
						<div class="col-xs-6">
							<a href="#addEmployeeModal" class="btn btn-success" data-toggle="modal"><i class="material-icons">&#xE147;</i> <span>Add New Word</span></a>

						</div>

					</div>
				</div>
				<table class="table table-striped table-hover">
											<div class="form-inline search-bar search-form d-flex align-items-center"> 
    <input type="text" id="getSign" class="rounded"placeholder="Search Sign/ID/Video"style="color: black;"/>
</div><!-- End Search Bar -->
					<thead>
						<tr>
                            <th>Sign ID</th>
							<th>Sign</th>
							<th>Video</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody id="showdata">
						<?php
						include "dbConn.php";
$sql = "SELECT * FROM `dic` LIMIT $offset, $entries_per_page";

							$result = mysqli_query($conn,$sql);
							if (!$result) {
								die("Query failed: " . mysqli_error($conn));
							}
							while($row = mysqli_fetch_assoc($result)){
							?>
<tr>
                            <td><?php echo $row['signID'] ?></td>
                            <td><?php echo $row['sign'] ?></td>
                            <td><?php echo $row['video'] ?></td>
							<td>
								<!--<a href="#editEmployeeModal" class="edit" data-toggle="modal"><i class="material-icons" data-toggle="tooltip" title="Edit">&#xE254;</i></a>-->
								<!--<a href="#editEmployeeModal" class="edit" data-toggle="modal" data-signid="<?php echo $row['signID']; ?>"><i class="material-icons" data-toggle="tooltip" title="Edit">&#xE254;</i></a>-->
                                <a href="#editEmployeeModal" class="edit" data-toggle="modal" data-signid="<?php echo $row['signID']; ?>"data-sign="<?php echo $row['sign']; ?>"data-video="<?php echo $row['video']; ?>">
   <i class="material-icons" data-toggle="tooltip" title="Edit">&#xE254;</i>
</a>
                                <a href="#deleteEmployeeModal" class="delete" data-toggle="modal"  data-signid="<?php echo $row['signID']; ?>"><i class="material-icons" data-toggle="tooltip" title="Delete">&#xE872;</i></a>


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
				<form action="manageDicValidate.php" method="POST" enctype="multipart/form-data">
					<div class="modal-header">						
						<h4 class="modal-title">Add Dictionary</h4>
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					</div>
					<div class="modal-body">	
                        <div class="form-group">
							<label>Sign ID</label>
                            <?php
                        include "dbConn.php";
                        
                        // Fetch the maximum signID from the dic table
                        $sqlMaxSignID = "SELECT MAX(signID) AS maxSignID FROM dic";
                        $resultMaxSignID = mysqli_query($conn, $sqlMaxSignID);
                        
                        if ($rowMaxSignID = mysqli_fetch_assoc($resultMaxSignID)) {
                            // Increment the maximum signID by 1
                            $newSignID = $rowMaxSignID['maxSignID'] + 1;
                        } else {
                            // If there are no existing records, start with 1
                            $newSignID = 1;
                        }
                        ?>
							<input type="text" class="form-control" name="signID" value="<?php echo $newSignID; ?>" disabled>
						</div>
						<div class="form-group">
							<label>Sign</label>
							<input type="text" class="form-control" name="sign"required>
						</div>
						<div class="form-group">
							<label>video</label>
                               <input class="form-control" type="file" id="formFile" name="video" placeholder="video.mp4" required>
						</div>	
					</div>
					<div class="modal-footer">
						<input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
						<input type="submit" class="btn btn-success" value="Add" name="add_submit">
					</div>
				</form>
			</div>
		</div>
	</div>
	
	<!-- Edit Modal HTML -->
<div id="editEmployeeModal" class="modal fade">
    <?php
    if (isset($_GET['signID'])) {
        require 'dbConn.php';
        $signID = mysqli_real_escape_string($conn, $_GET['signID']);

        $details_query = "SELECT * FROM dic WHERE signID = '".$signID."'";
        $details_result = mysqli_query($conn, $details_query);
        $row = mysqli_fetch_array($details_result);
    }
    ?>
    
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="editDic.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="signID" id="edit_signID" value="<?php echo $signID; ?>">

                <div class="modal-header">                        
                    <h4 class="modal-title">Edit Dictionary</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">    
                    <div class="form-group">
                        <label>Sign ID</label>
                        <input type="text" class="form-control" id="edit_signID_display" value="<?php echo $row['signID']; ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label>Sign</label>
                        <input type="text" class="form-control" name="sign" id="edit_sign" value="<?php echo $row['sign']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Video</label>
                        <?php
                        // Check if video file exists
                        if (!empty($row['video'])) {
                            echo '<p>Current Video: <a href="' . $row['video'] . '" target="_blank">' . $row['video'] . '</a></p>';
                        }
                        ?>
                        <input class="form-control" type="file" id="formFile" name="video">
                        <small>If you want to keep the existing video, leave this field empty.</small>
                    </div>              
                </div>
                <div class="modal-footer">
                    <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
                    <input type="submit" class="btn btn-info" value="Save" name="edit">
                </div>
            </form>
        </div>
    </div>
</div>


	<!-- Delete Modal HTML -->
	<div id="deleteEmployeeModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="deleteDic.php" method="POST">
                <div class="modal-header">
                    <h4 class="modal-title">Delete Dictionary</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete these Records?</p>
                    <p class="text-warning"><small>This action cannot be undone.</small></p>
                </div>
                <div class="modal-footer">
                    
                    <input type="hidden" name="signID" id="delete_signID" value="">
                    <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
                    <input type="submit" class="btn btn-danger" name="delete" value="Delete">
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