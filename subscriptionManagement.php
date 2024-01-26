<?php
session_start();

require "dbConn.php";

if (isset($_POST['editSubscription'])) {
    // Edit operation
$subscriptionID = isset($_POST['subscriptionID']) ? $_POST['subscriptionID'] : null;
$userID = isset($_POST['userID']) ? $_POST['userID'] : null;
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];
    $type = $_POST['type'];
    $price = $_POST['price'];
    $free = $_POST['free'];

$sql = "UPDATE `subscriptions` SET 
        `startDate`=?, 
        `endDate`=?, 
        `type`=?, 
        `price`=?, 
        `free`=? 
            WHERE `subscriptionID`=? AND `userID`=?";

$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'ssssdsi', $startDate, $endDate, $type, $price, $free, $subscriptionID, $userID);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        $_SESSION['success_message'] = "Record edited successfully.";
        header("Location: manageSubscription.php");
        exit();
    } else {
        die("Failed to execute statement: " . mysqli_stmt_error($stmt));
    }

    mysqli_stmt_close($stmt);
} else {
    die("Failed to prepare statement: " . mysqli_error($conn));
}

} elseif (isset($_POST['delSelected'])) {
    // Delete selected operation
    $selectedSubscriptionIDs = isset($_POST['options']) ? $_POST['options'] : [];

    if (!empty($selectedSubscriptionIDs)) {
        $selectedSubscriptionIDsString = implode(',', $selectedSubscriptionIDs);
        $sqlDeleteSelected = "DELETE FROM `subscriptions` WHERE subscriptionID IN ($selectedSubscriptionIDsString)";
        $resultDeleteSelected = mysqli_query($conn, $sqlDeleteSelected);

        if ($resultDeleteSelected) {
            $_SESSION['success_message'] = "Records deleted successfully.";
            header("Location: manageSubscription.php");
            exit();
        } else {
            echo "Failed to delete selected subscriptions: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['error_message'] = "Error: No records selected for deletion.";
        header("Location: manageSubscription.php");
        exit();
    }
} elseif (isset($_POST['delSubscription'])) {
    // Delete single operation
$deleteSubscriptionID = isset($_POST['deleteSubscriptionID']) ? $_POST['deleteSubscriptionID'] : null;

if ($deleteSubscriptionID) {
    $sqlDeleteSubscription = "DELETE FROM `subscriptions` WHERE subscriptionID = ?";
    $stmtDeleteSubscription = mysqli_prepare($conn, $sqlDeleteSubscription);
    
    if ($stmtDeleteSubscription) {
        mysqli_stmt_bind_param($stmtDeleteSubscription, 's', $deleteSubscriptionID);
        $resultDeleteSubscription = mysqli_stmt_execute($stmtDeleteSubscription);

        if ($resultDeleteSubscription) {
            $_SESSION['success_message'] = "Record deleted successfully.";
            header("Location: manageSubscription.php");
            exit();
        } else {
            echo "Failed to delete Subscription: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmtDeleteSubscription);
    } else {
        echo "Failed to prepare statement: " . mysqli_error($conn);
    }
} else {
    echo "Error: 'deleteSubscriptionID' is not set in the POST request.";
}

} elseif (isset($_POST['addSubscription'])) {
    // Add operation
        $subscriptionID = $_POST['subscriptionID'];
    $userID = $_POST['userID'];
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];
    $type = $_POST['type'];
    $price = $_POST['price'];
    $free = $_POST['free'];

    $sql = "INSERT INTO `subscriptions`(`subscriptionID`,`userID`, `startDate`, `endDate`, `type`, `price`, `free`) 
            VALUES ('$subscriptionID', '$userID', '$startDate', '$endDate', '$type', '$price', '$free')";
    
    $result = mysqli_query($conn, $sql);

    if ($result) {
        $_SESSION['success_message'] = "Record added successfully.";
        header("Location: manageSubscription.php");
        exit();
    } else {
        echo "Failed: " . mysqli_error($conn);
    }
} else {
    echo "Error: Invalid operation.";
}
?>
