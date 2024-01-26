<?php
// action.php

include 'dbConn.php';

if(isset($_POST['sign'])) {
    $sign = $_POST['sign'];
    $dicSql = "SELECT * FROM dic 
                WHERE (signID LIKE '%$sign%' OR sign LIKE '%$sign%' OR video LIKE '%$sign%')
                LIMIT 10"; // Add LIMIT clause to limit the results to 10
  
    $dicQuery = mysqli_query($conn, $dicSql);
    $dicData = '';
    
    while($dicRow = mysqli_fetch_assoc($dicQuery)) {
        $dicData .= '
        <tr>
            <td>'.$dicRow["signID"].'</td>
            <td>'.$dicRow["sign"].'</td>
            <td>'.$dicRow["video"].'</td>
            <td>
                <a href="#editEmployeeModal" class="edit" data-toggle="modal" data-signid="'.$dicRow['signID'].'" data-sign="'.$dicRow['sign'].'" data-video="'.$dicRow['video'].'">
                    <i class="material-icons" data-toggle="tooltip" title="Edit">&#xE254;</i>
                </a>
                <a href="#deleteEmployeeModal" class="delete" data-toggle="modal"  data-signid="'.$dicRow['signID'].'">
                    <i class="material-icons" data-toggle="tooltip" title="Delete">&#xE872;</i>
                </a>
            </td>
        </tr>';
    }
    
    echo $dicData;
    
} elseif(isset($_POST['lesson'])) {
    $lesson = $_POST['lesson'];
    $lessonSql = "SELECT l.lessonID, l.courseID, l.lessonTitle, l.video_url, c.courseName 
                  FROM lesson l
                  JOIN course c ON l.courseID = c.courseID
                  WHERE (l.lessonID LIKE '%$lesson%' OR l.lessonTitle LIKE '%$lesson%' OR l.video_url LIKE '%$lesson%' OR c.courseName LIKE '%$lesson%')
                  LIMIT 10"; // Add LIMIT clause to limit the results to 10

    $lessonQuery = mysqli_query($conn, $lessonSql);
    $lessonData = '';

    while($lessonRow = mysqli_fetch_assoc($lessonQuery)) {
        $lessonData .= '
        <tr>
            <td>'.$lessonRow["courseName"].'</td>
            <td>'.$lessonRow["lessonID"].'</td>
            <td>'.$lessonRow["lessonTitle"].'</td>
            <td>'.$lessonRow["video_url"].'</td>
            <td>
                <a href="#editEmployeeModal" class="editLesson" data-toggle="modal" 
                    data-lessonid="'.$lessonRow['lessonID'].'"
                    data-lessontitle="'.$lessonRow['lessonTitle'].'"
                    data-courseid="'.$lessonRow['courseID'].'">
                    <i class="material-icons" data-toggle="tooltip" title="Edit">&#xE254;</i>
                </a>
                <a href="#deleteEmployeeModal" class="delete" data-toggle="modal"  
                    data-lessonid="'.$lessonRow['lessonID'].'">
                    <i class="material-icons" data-toggle="tooltip" title="Delete">&#xE872;</i>
                </a>
            </td>
        </tr>';
    }

    echo $lessonData;
} elseif(isset($_POST['course'])) {
    $course = $_POST['course'];
    $courseSql = "SELECT * FROM course 
                  WHERE (courseID LIKE '%$course%' OR courseName LIKE '%$course%' OR courseDescription LIKE '%$course%' OR courseLevel LIKE '%$course%' OR type LIKE '%$course%' OR duration_hours LIKE '%$course%' OR video_count LIKE '%$course%')
                  LIMIT 10"; // Add LIMIT clause to limit the results to 10
  
    $courseQuery = mysqli_query($conn, $courseSql);
    $courseData = '';
    
    while($courseRow = mysqli_fetch_assoc($courseQuery)) {
        $courseData .= '
        <tr>
            <td>'.$courseRow["courseID"].'</td>
            <td>'.$courseRow["courseName"].'</td>
            <td>'.$courseRow["courseDescription"].'</td>
            <td>'.$courseRow["courseLevel"].'</td>
            <td>'.$courseRow["type"].'</td>
            <td>'.$courseRow["duration_hours"].'</td>
            <td>'.$courseRow["video_count"].'</td>
            <td>
                <a href="#editEmployeeModal" class="edit" data-toggle="modal" 
                    data-courseid="'.$courseRow['courseID'].'"
                    data-coursename="'.$courseRow['courseName'].'"
                    data-coursedescription="'.$courseRow['courseDescription'].'"
                    data-courselevel="'.$courseRow['courseLevel'].'"
                    data-duration_hours="'.$courseRow['duration_hours'].'"
                    data-video_count="'.$courseRow['video_count'].'"
                    data-type="'.$courseRow['type'].'">
                    <i class="material-icons" data-toggle="tooltip" title="Edit">&#xE254;</i>
                </a>
                <a href="#deleteEmployeeModal" class="delete" data-toggle="modal"  
                    data-courseid="'.$courseRow['courseID'].'">
                    <i class="material-icons" data-toggle="tooltip" title="Delete">&#xE872;</i>
                </a>
                <a href="viewCourse.php?courseID='.$courseRow['courseID'].'" class="btn btn-success" style="color: white;">view course</a>
            </td>
        </tr>';
    }
    
    echo $courseData;
} elseif(isset($_POST['quiz'])) {
    $quiz = $_POST['quiz'];
    $quizSql = "SELECT q.quizID, q.quizTitle, c.courseID, c.courseName 
                  FROM quiz q
                  JOIN course c ON q.courseID = c.courseID
                  WHERE (q.quizID LIKE '%$quiz%' OR q.quizTitle LIKE '%$quiz%' OR c.courseName LIKE '%$quiz%')
                  LIMIT 10"; // Add LIMIT clause to limit the results to 10

    $quizQuery = mysqli_query($conn, $quizSql);
    $quizData = '';
    
    while($quizRow = mysqli_fetch_assoc($quizQuery)) {
        $quizData .= '
        <tr>
            <td>'.$quizRow["courseID"].'</td>
            <td>'.$quizRow["courseName"].'</td>
            <td>'.$quizRow["quizID"].'</td>
            <td>'.$quizRow["quizTitle"].'</td>
            <td>
                <a href="#editEmployeeModal" class="edit" data-toggle="modal" 
                    data-quizid="'.$quizRow['quizID'].'"
                    data-courseid="'.$quizRow['courseID'].'"
                    data-quiztitle="'.$quizRow['quizTitle'].'">
                    <i class="material-icons" data-toggle="tooltip" title="Edit">&#xE254;</i>
                </a>
                <a href="#deleteEmployeeModal" class="delete" data-toggle="modal"  
                    data-quizid="'.$quizRow['quizID'].'">
                    <i class="material-icons" data-toggle="tooltip" title="Delete">&#xE872;</i>
                </a>
            </td>
        </tr>';
    }
    
    echo $quizData;
} elseif(isset($_POST['question'])) {
$question = $_POST['question'];
$questionSql = "SELECT q.quizID, q.questionID, q.questionDescription, q.option_a, q.option_b, q.option_c, q.option_d, q.answer, q.questionMedia, quiz.quizTitle
                FROM quiz
                JOIN question q ON quiz.quizID = q.quizID
                WHERE (quiz.quizID LIKE '%$question%' OR q.questionID LIKE '%$question%' OR q.questionDescription LIKE '%$question%' OR q.option_a LIKE '%$question%' OR q.option_b LIKE '%$question%' OR q.option_c LIKE '%$question%' OR q.option_d LIKE '%$question%' OR q.answer LIKE '%$question%' OR q.questionMedia LIKE '%$question%' OR quiz.quizTitle LIKE '%$question%')
                LIMIT 10";


    $questionQuery = mysqli_query($conn, $questionSql);
    $questionData = '';

    while($questionRow = mysqli_fetch_assoc($questionQuery)) {
        $questionData .= '
        <tr>
            <td>'.$questionRow["quizID"].'</td>
            <td>'.$questionRow["quizTitle"].'</td>
            <td>'.$questionRow["questionID"].'</td>
            <td>'.$questionRow["questionDescription"].'</td>
            <td>'.$questionRow["questionMedia"].'</td>
            <td>'.$questionRow["option_a"].'</td>
            <td>'.$questionRow["option_b"].'</td>
            <td>'.$questionRow["option_c"].'</td>
            <td>'.$questionRow["option_d"].'</td>
            <td>'.$questionRow["answer"].'</td>
            <td>
                <a href="#editEmployeeModal" class="edit" data-toggle="modal" 
                    data-questionid="'.$questionRow['questionID'].'"
                    data-questiondescription="'.$questionRow['questionDescription'].'"
                    data-quizid="'.$questionRow['quizID'].'"
                    data-answer="'.$questionRow['answer'].'"
                    data-option_a="'.$questionRow['option_a'].'"
                    data-option_b="'.$questionRow['option_b'].'"
                    data-option_c="'.$questionRow['option_c'].'"
                    data-option_d="'.$questionRow['option_d'].'"
                    data-questionMedia="'.$questionRow['questionMedia'].'">
                    <i class="material-icons" data-toggle="tooltip" title="Edit">&#xE254;</i>
                </a>
                <a href="#deleteEmployeeModal" class="delete" data-toggle="modal"  
                    data-questionid="'.$questionRow['questionID'].'">
                    <i class="material-icons" data-toggle="tooltip" title="Delete">&#xE872;</i>
                </a>
            </td>
        </tr>';
    }

    echo $questionData;
} elseif(isset($_POST['subscription'])) {
    $subscription = $_POST['subscription'];
    $subscriptionSql = "SELECT * FROM subscriptions 
                        WHERE (subscriptionID LIKE '%$subscription%' OR userID LIKE '%$subscription%' OR startDate LIKE '%$subscription%' OR endDate LIKE '%$subscription%' OR type LIKE '%$subscription%' OR price LIKE '%$subscription%' OR free LIKE '%$subscription%')
                        LIMIT 10"; // Add LIMIT clause to limit the results to 10

    $subscriptionQuery = mysqli_query($conn, $subscriptionSql);
    $subscriptionData = '';

    while($subscriptionRow = mysqli_fetch_assoc($subscriptionQuery)) {
        $subscriptionData .= '
        <tr>
            <td>'.$subscriptionRow["subscriptionID"].'</td>
            <td>'.$subscriptionRow["userID"].'</td>
            <td>'.$subscriptionRow["startDate"].'</td>
            <td>'.$subscriptionRow["endDate"].'</td>
            <td>'.$subscriptionRow["type"].'</td>
            <td>'.$subscriptionRow["price"].'</td>
            <td>'.$subscriptionRow["free"].'</td>
        </tr>';
    }

    echo $subscriptionData;
}elseif(isset($_POST['user'])) {
    $user = $_POST['user'];
    $userSql = "SELECT * FROM user 
                WHERE (userID LIKE '%$user%' OR username LIKE '%$user%' OR userEmail LIKE '%$user%')
                LIMIT 10"; // Add LIMIT clause to limit the results to 10

    $userQuery = mysqli_query($conn, $userSql);
    $userData = '';

    while($userRow = mysqli_fetch_assoc($userQuery)) {
        $userData .= '
        <tr>
            <td>'.$userRow["userID"].'</td>
            <td>'.$userRow["username"].'</td>
            <td>'.$userRow["userEmail"].'</td>
        </tr>';
    }

    echo $userData;
}else{
    echo "No search term provided.";
}
?>
