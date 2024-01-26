<?php
session_start();
include 'dbConn.php';


// Function to check if the user is subscribed
function isUserSubscribed($userID, $type, $conn) {
    // Get the current date
    $currentDate = date('Y-m-d');

    // Query the database to check subscription status
    $query = "SELECT endDate FROM subscriptions WHERE userID='$userID' AND type='$type'";
    $result = mysqli_query($conn, $query);

    // Check if a valid subscription record exists
    if ($row = mysqli_fetch_assoc($result)) {
        $endDate = $row['endDate'];
        return $endDate >= $currentDate;
    }

    return false;
}
$userLoggedIn = isset($_SESSION["user"]);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Price</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">

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

<h1 class="heading"> select the plan </h1>
            <h3><center>Unlimited access to all lessons for Beginner, Intermediate and Advanced Levels</center></h3>
                <p><center>Recurring billing. Cancel anytime.</center></p>


<!-- price section  -->

<section class="price">
    <div class="box">
        <h3>Monthly</h3>
        <div class="amount"><span>RM</span>39.99<span>/month</span></div>
        <ul>
            <li>billed every month</li>
        </ul>
        <!-- Add PayPal Smart Button for Monthly subscription -->
<?php
                if ($userLoggedIn) {
                    $userID = $_SESSION['userID'];
                    $type = 'Monthly';
                    if (isUserSubscribed($userID, $type, $conn)) {
                        echo '<div class="subscribed-message">Subscribed</div>';
                    } else {
                        echo '<div id="paypal-button-container-P-48360802F97147915MWMNHIY"></div>';
                    }
                } else {
                       echo '<p class="login-message">Please log in to subscribe.</p>';
                }
                ?>
    </div>

    <div class="box">
        <h3>Yearly</h3>
        <div class="amount"><span>RM</span>359.88<span>/year</span></div>
        <ul>
            <li>billed annually (Saved RM 120)</li>
        </ul>
        <!-- Add PayPal Smart Button for Yearly subscription -->
        <?php
                if ($userLoggedIn) {
                    $userID = $_SESSION['userID'];
                    $type = 'Yearly';
                    if (isUserSubscribed($userID, $type, $conn)) {
                        echo '<div class="subscribed-message">Subscribed</div>';
                    } else {
                        echo '<div id="paypal-button-container-P-8JD667640J381203UMWMNMSI"></div>';
                    }
                } else {
                        echo '<p class="login-message">Please log in to subscribe.</p>';
                }
                
                ?>
    </div>
    
</section>


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

<!-- custom js file link -->
<script src="js/script.js"></script>
<script src="https://www.paypal.com/sdk/js?client-id=AYwtrlcsxuPMrw6Juv6J0YmFzGl-bCqpeQhFz6MBMCJKWWQAZv3CpV0bjQl4_oLJJffBAQI-oqvk7bX6&vault=true&intent=subscription" data-sdk-integration-source="button-factory"></script>

<script>
    // Monthly subscription button
    paypal.Buttons({
        style: {
            shape: 'rect',
            color: 'silver',
            layout: 'horizontal',
            label: 'subscribe'
        },
        createSubscription: function(data, actions) {
            return actions.subscription.create({
                plan_id: 'P-48360802F97147915MWMNHIY'
            });
        },
        onApprove: function(data, actions) {
            window.location.href = 'success.php?success=true&subscriptionID=' + data.subscriptionID + '&plan_id=' + 'P-48360802F97147915MWMNHIY';
        }
    }).render('#paypal-button-container-P-48360802F97147915MWMNHIY');

    // Yearly subscription button
    paypal.Buttons({
        style: {
            shape: 'rect',
            color: 'silver',
            layout: 'horizontal',
            label: 'subscribe'
        },
        createSubscription: function(data, actions) {
            return actions.subscription.create({
                plan_id: 'P-8JD667640J381203UMWMNMSI'
            });
        },
        onApprove: function(data, actions) {
            window.location.href = 'success.php?success=true&subscriptionID=' + data.subscriptionID + '&plan_id=' + 'P-8JD667640J381203UMWMNMSI';
        }
    }).render('#paypal-button-container-P-8JD667640J381203UMWMNMSI');
</script>


</body>
</html>