<?php
include 'dbConn.php';
session_start();


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dictionary</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/dic.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
$(document).ready(function () {
    // Function to handle autocomplete suggestions
    $('#searchWord').on('input', function () {
        var searchWord = $(this).val();
        if (searchWord !== '') {
            $.ajax({
                url: 'autocomplete.php',
                method: 'POST',
                data: { search_word: searchWord },
                success: function (data) {
                    $('#autocompleteSuggestions').html(data);
                }
            });
        } else {
            $('#autocompleteSuggestions').html('');
        }
    });

    // Handle click on autocomplete suggestion using event delegation
    $('#autocompleteSuggestions').on('click', '.autocomplete-option', function () {
        var selectedWord = $(this).text();
        var videoPath = $(this).data('video');

        // Update the search input
        $('#searchWord').val(selectedWord);

        // Display the video
        $('#autocompleteSuggestions').html('<video width="560" height="460" controls>' +
            '<source src="' + videoPath + '" type="video/mp4">' +
            'Your browser does not support the video tag.' +
            '</video>');
    });
});

</script>

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

        <h1 class="heading"> Malaysian Sign Language Dictionary </h1>
        <div class="wrapper">
            <!-- Form for searching using the search bar -->
            <form action="dic.php" method="post"enctype="multipart/form-data">
                <div class="search">
<input type="text" name="search_word" id="searchWord" placeholder="Search a word" required spellcheck="false">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </div>
                <p class="info-text">Type any existing word and press enter</p><br>
<!-- Autocomplete suggestions -->
    <div id="autocompleteSuggestions"></div>

 <?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['search_word'])) {
        $searchWord = $_POST['search_word'];

        // Add your SQL query to fetch video information based on the search word
        $sql_search = "SELECT * FROM dic WHERE sign = '$searchWord'";
        $result_search = $conn->query($sql_search);

        if ($result_search->num_rows > 0) {
            $row_search = $result_search->fetch_assoc();
            $videoPath = $row_search['video'];

            // Increment the search count for the searched word
            $sql_update_count = "UPDATE dic SET searchCount = searchCount + 1 WHERE sign = '$searchWord'";
            $conn->query($sql_update_count);

            // Display the video
            echo '<video width="560" height="460" controls>
                    <source src="' . $videoPath . '" type="video/mp4">
                    Your browser does not support the video tag.
                  </video>';
        } else {
            echo '<p class="no-video-message">No video found for the searched word.</p>';
        }
    } elseif (isset($_POST['popular_search'])) {
        $popularSearchWord = $_POST['popular_search'];

        // Increment the search count for the popular search word
        $sql_update_popular_count = "UPDATE dic SET searchCount = searchCount + 1 WHERE sign = '$popularSearchWord'";
        $conn->query($sql_update_popular_count);

        // Fetch video information for the popular search word
        $sql_popular_search = "SELECT * FROM dic WHERE sign = '$popularSearchWord'";
        $result_popular_search = $conn->query($sql_popular_search);

        if ($result_popular_search->num_rows > 0) {
            $row_popular_search = $result_popular_search->fetch_assoc();
            $popularVideoPath = $row_popular_search['video'];

            // Display the video
            echo '<video width="560" height="460" controls>
                    <source src="' . $popularVideoPath . '" type="video/mp4">
                    Your browser does not support the video tag.
                  </video>';
        } else {
            echo '<p>No video found for the popular search word.</p>';
        }
    }
}
?>
            </form>
           

            <!-- Form for popular searches -->
            <form action="dic.php" method="post"enctype="multipart/form-data">
                <div class="popular-searches">
                    <p class="info-text1">Popular searches</p>
                    <?php
$sql_popular = "SELECT sign, SUM(searchCount) AS total_count FROM dic GROUP BY sign ORDER BY total_count DESC LIMIT 3";
                        $result_popular = $conn->query($sql_popular);

                        while ($row = $result_popular->fetch_assoc()) {
                            echo "<button class='btn' name='popular_search' value='" . $row['sign'] . "'>" . $row['sign'] . "</button>";
                        }
                    ?>
                </div>

            </form>
        </div>

    <!-- footer section  -->

<footer class="footer">

  <div class="box-container">

      <div class="box">
          <h3>about us</h3>
          <p>SignTalk is your gateway to <br> mastering MSL.  Let's break down barriers and build bridges of understanding together.</p>
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
    <!-- Inside this JavaScript file, I've inserted Questions and Options only -->
    <script src="js/questions.js"></script>
    <!-- Inside this JavaScript file, I've coded all Quiz Codes -->
    <script src="js/quizscript.js"></script>
</body>
</html>