<?php
   include '../../php/config.php';

$deleterequestID = $_GET['requestID'];

$sql = "DELETE FROM request_tbl WHERE requestID = $deleterequestID";

    if($connections->query($sql) === TRUE){
        echo "<script>window.location.href='../request.php?delete_success=true';</script>";
    }
    else{
        echo 'Error:' .$sql. "br" .$connections->error;
    }
