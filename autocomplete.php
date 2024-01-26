<?php
include 'dbConn.php';

if (isset($_POST['search_word'])) {
    $searchWord = $_POST['search_word'];
    $sql_autocomplete = "SELECT sign, video FROM dic WHERE sign LIKE '%$searchWord%' LIMIT 10";
    $result_autocomplete = $conn->query($sql_autocomplete);

    if ($result_autocomplete->num_rows > 0) {
        while ($row = $result_autocomplete->fetch_assoc()) {
            // Include the video path as a data attribute
            echo '<div class="autocomplete-option" data-video="' . $row['video'] . '">' . $row['sign'] . '</div>';
        }
    } else {
        echo '<div class="autocomplete-option">No suggestions found</div>';
    }
}
?>
