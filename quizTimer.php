<?php
session_start();
include 'dbConn.php';

// Check if quizID is set
if (isset($_GET['quizID'])) {
    // Sanitize input to prevent SQL injection
    $quizID = mysqli_real_escape_string($conn, $_GET['quizID']);
} else {
    // Handle case where quizID is not set
    echo "Invalid request.";
    exit;
}

// Assuming $quizID is already available

$queryQuestions = "SELECT * FROM question WHERE quizID = '$quizID'";
$resultQuestions = mysqli_query($conn, $queryQuestions);

$quizData = []; // Array to store questions

if ($resultQuestions) {
    // Check if any rows are returned
    if (mysqli_num_rows($resultQuestions) > 0) {
        // Fetch questions and construct the $quizData array
while ($rowQuestion = mysqli_fetch_assoc($resultQuestions)) {
    $mediaExtension = pathinfo($rowQuestion['questionMedia'], PATHINFO_EXTENSION);
    $mediaType = in_array($mediaExtension, ['mp4', 'mov']) ? 'video' : 'image';

    // Map options to alphabet letters
    $options = [
        'A' => $rowQuestion['option_a'],
        'B' => $rowQuestion['option_b'],
        'C' => $rowQuestion['option_c'],
        'D' => $rowQuestion['option_d']
    ];

$quizData[] = [
    'question' => $rowQuestion['questionDescription'],
    'mediaType' => $mediaType,
    'mediaSource' => $rowQuestion['questionMedia'],
    'options' => array_values($options), // Use indexed array for options
    'correctAnswer' => $rowQuestion['answer'] // Set correct answer based on the 'answer' column
];
}

    } else {
        // Handle case where no rows are returned (no questions for the quiz)
        echo "No questions found for this quiz.";
        exit;
    }
} else {
    // Handle database query error
    echo "Error: " . mysqli_error($conn);
    exit;
}
// Fetch the course level from the database
$queryCourseLevel = "SELECT courseLevel FROM course WHERE courseID = (SELECT courseID FROM quiz WHERE quizID = '$quizID')";
$resultCourseLevel = mysqli_query($conn, $queryCourseLevel);

if ($resultCourseLevel && mysqli_num_rows($resultCourseLevel) > 0) {
    $rowCourseLevel = mysqli_fetch_assoc($resultCourseLevel);
    $courseLevel = $rowCourseLevel['courseLevel'];

    // Add the quitQuiz script with the dynamic course level using a switch statement
    echo '<script>
            function quitQuiz(){
                switch ("' . strtolower($courseLevel) . '") {
                    case "beginner":
                        window.location.href = "beginner.php";
                        break;
                    case "intermediate":
                        window.location.href = "intermediate.php";
                        break;
                    case "advanced":
                        window.location.href = "advanced.php";
                        break;
                    default:
                        // Handle the case when the course level is not recognized
                        console.error("Unknown course level: ' . strtolower($courseLevel) . '");
                }
            }
          </script>';
} else {
    // Handle the case where course level is not found
    echo "Error: Course level not found.";
    exit;
}
$conn->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz</title>
    <link rel="stylesheet" href="css/quizTimer.css">
    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">


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
<div class="wrapper">
    <div id="quiz-status"></div>

            <div class="quiz-container" id="quiz-container">
                <!-- Questions and options will be dynamically added here -->
            </div>
            <div id="timer"></div>
            
            <audio id="correctSound" src="sound/correct.wav"></audio>
<audio id="incorrectSound" src="sound/incorrect.wav"></audio>
<audio id="timeoutSound" src="sound/timeout.mp3"></audio>
            <div class="button-container">
                <button onclick="replayQuiz()" style="display: none;">Replay</button>
                <button onclick="nextQuestion()" style="display: none;">Next</button>
                <button onclick="quitQuiz()" class="quit-button" style="display: none;">Quit Quiz</button>
            </div>
        </div>
<footer class="footer">

    <div class="box-container">

        <div class="box">
            <h3>about us</h3>
            <p>SignTalk is your gateway to <br> mastering MSL.  Let's break down <br>barriers and build bridges of <br>understanding together.</p>
        </div>

        <div class="box">
            <h3>quick links</h3>
            <a href="#">home</a>
            <a href="#">dictionary</a>
            <a href="#">course</a>
            <a href="#">price</a>
            <a href="#">log in</a>
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


<script>
    
const quizData = <?php echo json_encode($quizData); ?>;

const quizContainer = document.getElementById('quiz-container');
const timerElement = document.getElementById('timer');
const quizStatusElement = document.getElementById('quiz-status');
const buttonContainer = document.querySelector('.button-container');
let currentQuestionIndex = 0;
let correctAnswers = 0;
console.log('quizData:', quizData);
console.log('quizContainer:', quizContainer);


function loadQuiz() {
    if (currentQuestionIndex < quizData.length) {
        const questionData = quizData[currentQuestionIndex];

        const questionElement = document.createElement('div');
        questionElement.classList.add('question');
        questionElement.innerHTML = `<p>${currentQuestionIndex + 1}. ${questionData.question}</p>`;

        if (questionData.mediaType === 'image') {
            const imageElement = document.createElement('img');
            imageElement.src = questionData.mediaSource;
            imageElement.classList.add('centered-image');
            // Set width and height for the image
    imageElement.style.width = 'auto'; // Adjust as needed
    imageElement.style.height = '100'; // Maintain aspect ratio
            questionElement.appendChild(imageElement);
        } else if (questionData.mediaType === 'video') {
            const videoElement = document.createElement('video');
            videoElement.src = questionData.mediaSource;
            videoElement.controls = true; // Add controls for play/pause
            videoElement.autoplay = true; // Autoplay the video
            videoElement.loop = true; // Repeat the video
            videoElement.classList.add('centered-video');
            // Set width and height for the video
    videoElement.style.width = '100%'; // Adjust as needed
    videoElement.style.height = 'auto'; // Maintain aspect ratio
            questionElement.appendChild(videoElement);
        }

        timerElement.innerText = `Time left: 15 seconds`;

        const optionsElement = document.createElement('div');
        optionsElement.classList.add('options');

        questionData.options.forEach((option) => {
            const optionButton = document.createElement('button');
            optionButton.textContent = option;
            optionButton.addEventListener('click', () => checkAnswer(option, questionData.correctAnswer, optionButton));
            optionsElement.appendChild(optionButton);
        });

        questionElement.appendChild(optionsElement);
        quizContainer.innerHTML = '';
        quizContainer.appendChild(questionElement);

        quizStatusElement.innerText = `Question ${currentQuestionIndex + 1} of ${quizData.length}`;

        startTimer();
    } else {
        showResults();
    }
}


function checkAnswer(selectedAnswer, correctAnswer, button) {
    stopTimer();

    const options = quizContainer.querySelectorAll('.options button');

    options.forEach((optionButton) => {
        optionButton.disabled = true;
        if (optionButton.textContent === correctAnswer) {
            optionButton.classList.add('correct');
        } else if (optionButton.textContent === selectedAnswer) {
            optionButton.classList.add('incorrect');
        }
    });
// Play the sound effect
    // Increment correctAnswers only if the answer is correct
    if (selectedAnswer === correctAnswer) {
                document.getElementById('correctSound').play();

        correctAnswers++;
    }else {
        document.getElementById('incorrectSound').play();
    }

    // Automatically move to the next question after a brief delay
    setTimeout(() => {
        currentQuestionIndex++;
        loadQuiz();
    }, 2000); // Adjust the delay as needed
}





function showResults() {
    quizContainer.innerHTML = '';
        stopTimer(); // Stop the timer when the quiz is completed

    timerElement.innerText = ''; // Clear the timer element
    
    
    const scoreElement = document.createElement('div');
    scoreElement.innerText = `Quiz completed! Your score: ${correctAnswers} out of ${quizData.length}`;
    scoreElement.style.fontSize = '22px';
    scoreElement.style.position = 'absolute';
    scoreElement.style.top = '30%';
    scoreElement.style.left = '50%';
    scoreElement.style.transform = 'translate(-50%, -50%)';
    quizContainer.appendChild(scoreElement);
    
    // Save the quiz result
    saveQuizResult(correctAnswers);
    
    // Display both "Replay" and "Quit" buttons
    buttonContainer.querySelector('button:first-child').style.display = 'inline'; // Display the "Replay" button
    buttonContainer.querySelector('button:last-child').style.display = 'inline'; // Display the "Quit" button
    buttonContainer.style.position = 'absolute';
    buttonContainer.style.top = '40%';
    buttonContainer.style.left = '50%';
    buttonContainer.style.transform = 'translate(-50%, -50%)';
        quizStatusElement.innerText = 'Congrats!';

}
function saveQuizResult(marks) {
    const quizID = "<?php echo $quizID; ?>";

    // Send an AJAX request to save the quiz result
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "saveResult.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            console.log("Quiz result saved successfully.");
        }
    };

    // Send the data to saveResult.php
    xhr.send(`quizID=${quizID}&marks=${marks}`);
}

function nextQuestion() {
    // Move to the next question after a brief delay
    setTimeout(() => {
        currentQuestionIndex++;
        loadQuiz();
        // Hide the "Next" button
        buttonContainer.querySelector('button:last-child').style.display = 'none';
    }, 2000);
}



function startTimer() {
    let timeLeft = 15;

    timer = setInterval(() => {
        timerElement.innerText = `Time left: ${timeLeft} seconds`;

        if (timeLeft <= 0) {
            stopTimer();
            // Play the timeout sound
            document.getElementById('timeoutSound').play();
            const options = quizContainer.querySelectorAll('.options button');
            options.forEach((optionButton) => {
                optionButton.disabled = true;
                if (optionButton.textContent === quizData[currentQuestionIndex].correctAnswer) {
                    optionButton.classList.add('correct');
                }
            });

            // Automatically move to the next question after a brief delay
            setTimeout(() => {
                currentQuestionIndex++;
                loadQuiz();
            }, 1000);
        }

        timeLeft--;
    }, 1000);
}



function stopTimer() {
    clearInterval(timer);
}

function replayQuiz() {
    currentQuestionIndex = 0;
    correctAnswers = 0;
    loadQuiz();
    buttonContainer.querySelector('button:first-child').style.display = 'none'; // Hide the "Replay" button
    buttonContainer.querySelector('button:last-child').style.display = 'none'; // Hide the "Next" button
}


// Call the function to load the quiz when the page loads

window.onload = function () {
    loadQuiz();
    // Set the initial display style for the "Quit Quiz" button to 'none'
    buttonContainer.querySelector('.quit-button').style.display = 'none';
};

</script>
</body>
</html>
