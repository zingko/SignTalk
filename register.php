<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- ===== Iconscout CSS ===== -->
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">

    <!-- ===== CSS ===== -->
    <link rel="stylesheet" href="css/login.css">
         
    <title>Registration</title> 
</head>
<body>
    <div class="main-container">
<div class="left-container">
<h1>Welcome to SignTalk!</h1>
<p>Login to continue your journey of learning sign language.</p>
<img src="gaming-animate.svg" class="left-container-image" alt="gaming animate">

</div>
<div class="right-container">
<div class="container">
        <div class="forms">
            <!-- Registration Form -->
            <div class="form signup">
                <div id="logo">
        <a href="index.php"><img src="images/silence.png" alt="" width="100" height="25"></a>
      </div>
            <h2>Registration</h2>
           
            <form action="registerValidate.php" method="POST">
            <?php
session_start(); // Start the session

// Check for registration errors in the session and display them
if (isset($_SESSION["registration_errors"]) && is_array($_SESSION["registration_errors"])) {
    foreach ($_SESSION["registration_errors"] as $error) {
        echo "<div class='alert alert-danger'>$error</div>";
    }
    unset($_SESSION["registration_errors"]); // Clear the error messages
}

// Check for a registration success message in the session and display it
if (isset($_SESSION["registration_success"])) {
    echo "<div class='alert alert-success'>{$_SESSION["registration_success"]}</div>";
    unset($_SESSION["registration_success"]); // Clear the success message
}
?>
                    <div class="input-field">
                        <input type="text" name="userName" placeholder="Enter your name" required>
                    </div>
                    <div class="input-field">
                        <input type="text" name="userEmail" placeholder="Enter your email" required>
                    </div>
                    <div class="input-field">
                        <input type="password" name="userPassword" class="password" placeholder="Create a password (at least 8 characters)" required>
                        <i class="uil uil-eye-slash showHidePw"></i>

                    </div>
                    <div class="input-field">
                        <input type="password"  name="Rpassword" class="Rpassword" placeholder="Confirm a password" required>
                        <i class="uil uil-eye-slash showHideRpw"></i>
                    </div>


                    <div class="input-field button">
                        <input type="submit" name="submit" value="Register">
                    </div>
                </form>

                <div class="login-signup">
                    <span class="text">Already a member?
                        <a href="login.php" class="text login-link">Login Now</a>
                    </span>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
    <!-- ===== JAVASCRIPT ===== -->
    <script src="jvscript.js"></script> 
    <script>
        // JavaScript code for show/hide confirm password
document.addEventListener('DOMContentLoaded', function () {
    const confirmPasswordField = document.querySelector('.Rpassword');
    const showHideRPasswordIcons = document.querySelectorAll('.showHideRpw');

    // Function to toggle password visibility
    function togglePasswordVisibility(passwordField, showHideIcons) {
        const type = passwordField.type === 'password' ? 'text' : 'password';
        passwordField.type = type;
        showHideIcons.forEach(icon => icon.classList.toggle('uil-eye-slash'));
        showHideIcons.forEach(icon => icon.classList.toggle('uil-eye'));
    }

    // Event listeners for confirm password field
    showHideRPasswordIcons.forEach(icon => {
        icon.addEventListener('click', function () {
            togglePasswordVisibility(confirmPasswordField, showHideRPasswordIcons);
        });
    });
});

    </script>
</body>
</html>