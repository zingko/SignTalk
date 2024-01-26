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
         
    <title>Login & Registration</title> 
</head>
<body>
    <div class="main-container">
<div class="left-container">
<h1>Welcome to SignTalk!</h1>
<p>Login to continue your journey of learning sign language.</p>
<img src="images/fingerprint-animate.svg" class="left-container-image" alt="fingerprint-animate">

</div>
<div class="right-container">
<div class="container">
        <div class="forms">
            <div class="form login">
            <div id="logo">
        <a href="index.php"><img src="images/silence.png" alt="" width="100" height="25"></a>
      </div>
                <h2>User Login</h2>
                
                <form action="loginValidate.php" method="POST">
                <?php
                        session_start();
                        if (isset($_SESSION["login_error"])) {
                            echo "<div class='alert alert-danger'>" . $_SESSION["login_error"] . "</div>";
                            unset($_SESSION["login_error"]); // Clear the error message from the session
                        }
                        ?>
                    <div class="input-field">
                        <input type="text" name="userEmail" placeholder="Email" required>
                    </div>
                    
                    <div class="input-field">
                        <input type="password" name="userPassword" class="password" placeholder="Password" required>
                        <i class="uil uil-eye-slash showHidePw"></i>

                    </div>

                    <div class="input-field button">
                        <input type="submit" name="login" value="Login">
                    </div>
                </form>

                <div class="login-signup">
                    <span class="text">Not a member?
                        <a href="register.php" class="text signup-link">Signup Now</a>
                    </span>
                </div>
                
            </div>
        </div>
    </div>
    </div>
    </div>
    <!-- ===== JAVASCRIPT ===== -->
     <script src="jvscript.js"></script> 

</body>
</html>