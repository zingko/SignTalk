<?php 
   session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>home</title>

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

<!-- home section  -->
<div class="container">
<section class="home">

    <div class="content">
        <h3>Unlock the world of Malaysian Sign Language (MSL) with SignTalk</h3>
        <p>Ready to embark on your MSL adventure? Sign up for SignTalk today and experience the joy of communicating through Malaysian Sign Language.</p>
        <a href="beginner.php" class="btn">get started</a>
    </div>
    <div class="image">
        <img src="images/holdingHeart.jpg" alt="">
    </div>

</section>
<section class="feature">
    <div class="imageLeft">
        <img src="images/3d-hands-fun-and-wild-white-skin-hand-showing-love-you-sign-2.png" alt="I Love you Illustration by Icons 8 from Ouch!">
    </div>
    <div class="fcontent">
        <h3>Features of our website</h3>
        <p>- Sign Language Dictionaries</p>
        <p>- Free and Premium Courses</p>
        <p>- Interactive Quizzes After Completed All Lessons in each course</p>
        <a href="dic.php" class="btn">Try our dictionary now</a>
    </div>
</section>
<section class="flipbox">

    <h3>Test your knowledge</h3>
        <h4>Do you know what is the meaning of the sign in the picture?</h4>
<p>Hover the picture to see it</p>
<div class="flip">

    <div class="flipcard">
      <div class="front"></div>
      <div class="back">
        <h1>Meaning of Sign</h1>
        <p>Thank you</p>
      </div>
    </div>
  </div>
  <p>Image Source:YES Alumni Malaysia</p>

</section>
<!-- footer section  -->

<footer class="footer">

    <div class="box-container">

        <div class="box">
            <h3>about us</h3>
            <p>SignTalk is your gateway to <br> mastering MSL.  Let's break down <br>barriers and build bridges of <br>understanding together.</p>
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

</footer>
<div class="credit"> &copy;Copyright <span> SignTalk </span> | all rights reserved | for educational purpose only </div>

</div>



<!-- js file link -->
<script src="js/script.js"></script>

</body>
</html>
