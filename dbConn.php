<?php 

    $servername = "localhost:3306";
    $dBUsername = "umskalfy1_signtalkadmin";
    $dBPassword = "signtalkxyz11";
    $dBName = "umskalfy1_signtalk";

    $conn = mysqli_connect($servername, $dBUsername, $dBPassword, $dBName);

    if(!$conn){
        die("Connection failed: ".mysqli_connect_error());
    }
  
?>


