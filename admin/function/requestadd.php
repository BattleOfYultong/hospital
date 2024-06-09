<?php
    session_start();
    include '../../php/config.php';

    $requestID = $_POST['requestID'];
    $DoctorID = $_POST['DoctorID'];

    $sql = "UPDATE request_tbl SET requestDoctor = '$DoctorID' WHERE requestID = $requestID";

    if($connections->query($sql) === TRUE){
        echo "<script>window.location.href='../request.php?request_success=true';</script>";
    }
    else{
        echo 'Error:' .$sql. "br" .$connections->error;
    }


