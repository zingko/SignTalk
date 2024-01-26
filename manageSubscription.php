<?php
session_start();
include 'dbConn.php';

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
}

$userID = isset($_SESSION['userID']) ? $_SESSION['userID'] : null;
// Fetch User Data for Dropdown
$sqlUsers = "SELECT userID, username FROM user";
$resultUsers = $conn->query($sqlUsers);
if (!$resultUsers) {
    die("Error fetching user data: " . $conn->error);
}

// Fetch User Data with pagination
$entries_per_page = 10;
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($current_page - 1) * $entries_per_page;

$sql = "SELECT subscriptionID, free, type FROM subscriptions LIMIT $offset, $entries_per_page";
$result = $conn->query($sql);
if (!$result) {
    // If there is an error in the query, display the error message
    die("Error: " . $conn->error);
}
// Get the total number of rows in the user table
$sqlCount = "SELECT COUNT(*) as total FROM subscriptions";
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
<title>Manage Subscription</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round">
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<link rel="stylesheet" href="css/manageDic.css">

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<script>
    $(document).ready(function () {
        // Activate tooltip
        $('[data-toggle="tooltip"]').tooltip();

        $('#getSubscription').on("keyup", function(){
    var getSubscription = $(this).val();
    $.ajax({
        method:'POST',
        url:'action.php',
        data:{subscription:getSubscription},
        success:function(response)
        {
            $("#showdata").html(response);
        } 
    });
});


        // Capture subscriptionID for edit modal
        $('#editSubscriptionModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var subscriptionID = button.data('subscriptionid');
            var userID = button.data('userid'); 
            var startDate = button.data('startdate');
            var endDate = button.data('enddate');
            var type = button.data('type');
            var price = button.data('price');
            var free = button.data('free');

            $('#edit_subscriptionID').val(subscriptionID);
            $('#edit_subscriptionID_display').val(subscriptionID);
            $('#edit_userID').val(userID);
            $('#edit_userID_display').val(userID);
            $('#edit_startDate').val(startDate);
            $('#edit_endDate').val(endDate);
            $('#edit_type').val(type);
            $('#edit_price').val(price);
            $('#edit_free').val(free);
        });

        // Capture subscriptionID for delete modal
        $('#deleteSubscriptionModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var subscriptionID = button.data('subscriptionid');
            $('#delete_subscriptionID').val(subscriptionID);
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
							<h2>Manage <b>Subscription</b></h2>
						</div>
					</div>
				</div>
				<table class="table table-striped table-hover">
					<div class="form-inline search-bar search-form d-flex align-items-center"> 
    <input type="text" id="getSubscription" class="rounded" placeholder="Search Subscription/ID/User/Type" style="color: black;"/>
</div><!-- End Search Bar -->
    <thead>
        <tr>
            <th>Subscription ID</th>
            <th>User ID</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Type</th>
            <th>Price</th>
            <th>Free</th>
        </tr>
    </thead>
    <tbody id="showdata">
        <?php
        include "dbConn.php";
        $sql = "SELECT * FROM `subscriptions` LIMIT $offset, $entries_per_page";

        $result = mysqli_query($conn, $sql);
        if (!$result) {
            die("Query failed: " . mysqli_error($conn));
        }
        while ($row = mysqli_fetch_assoc($result)) {
        ?>
            <tr>
                <td><?php echo $row['subscriptionID'] ?></td>
                <td><?php echo $row['userID'] ?></td>
                <td><?php echo $row['startDate'] ?></td>
                <td><?php echo $row['endDate'] ?></td>
                <td><?php echo $row['type'] ?></td>
                <td><?php echo $row['price'] ?></td>
                <td><?php echo $row['free'] ?></td>
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
</body>
</html>