<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Confirmation</title>
    <style>
    :root{
  --blue:#FB7A8F;
  --peach:#F76C6C;
  /*--gradient:linear-gradient(90deg, var(--violet), var(--pink));
--gradient: linear-gradient(0deg, rgba(36,48,94,1) 0%, rgba(247,108,108,1) 100%);*/
  --gradient:linear-gradient(0deg, var(--blue), var(--peach));


}
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }

        .success {
            color: #4CAF50;
        }

        .error {
            color: #FF0000;
        }

        .subscription-info {
            font-weight: bold;
            margin-top: 10px;
        }
        .back-button {
            background-color: #333;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 20px;
        }

        .back-button:hover {
              background:var(--gradient);
        }
    </style>
</head>
<body>
    <div class="container">
  <?php
session_start();
include 'dbConn.php';

// Check if 'userID' is set in the session
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

// Ensure that the PayPal transaction was successful
if (isset($_GET['success']) && $_GET['success'] == 'true') {
    // Get subscriptionID from PayPal
    $subscriptionID = $_GET['subscriptionID'];

    // Get user ID from the session
    $userID = $_SESSION['userID'];

    // Get the current date
    $startDate = date("Y-m-d");

// Determine the subscription type (Monthly or Yearly)
$type = (isset($_GET['plan_id']) && $_GET['plan_id'] == 'P-48360802F97147915MWMNHIY') ? 'Monthly' : 'Yearly';

// Set the price based on the subscription type
$price = ($type == 'Monthly') ? 39.99 : 358.99;




// Set the 'free' field (you may need to adjust this based on your requirements)
$free = 0; // Assuming it's not free

// Calculate the end date based on the subscription type
if ($type == 'Monthly') {
    // Add one month for Monthly plan
    $endDate = date("Y-m-d", strtotime($startDate . '+1 month'));
} elseif ($type == 'Yearly') {
    // Add one year for Yearly plan
    $endDate = date("Y-m-d", strtotime($startDate . '+1 year'));
} else {
    // Handle any other subscription types if needed
    $endDate = $startDate; // Set a default value for unknown types
}

// Insert data into the subscriptions table
$insertQuery = "INSERT INTO subscriptions (subscriptionID, userID, startDate, endDate, type, price, free)
                VALUES ('$subscriptionID', '$userID', '$startDate', '$endDate', '$type', '$price', '$free')";

    // Execute the query
    if (mysqli_query($conn, $insertQuery)) {
        // Insertion successful
        echo '<p class="success">Success! You are now subscribed!</p>';
                    echo '<p class="subscription-info">Type: ' . htmlspecialchars($type) . ', Price: $' . number_format($price, 2) . '</p>';
                    echo '<button class="back-button" onclick="window.location.href=\'price.php\'">Back</button>';

    } else {
        // Insertion failed
        echo "Error: " . $insertQuery . "<br>" . mysqli_error($conn);
    }
} else {
    // Handle unsuccessful payment 
            echo '<p class="error">Payment unsuccessful. Please try again.</p>';
            echo '<button class="back-button" onclick="window.location.href=\'price.php\'">Back</button>';

}
?>
      
            </div>
</body>
</html>
