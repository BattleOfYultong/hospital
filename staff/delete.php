<?php

include '../php/config.php';

$deleteID = $_GET['requestID'];

$sql = "DELETE FROM request_tbl WHERE requestID = $deleteID";

if($connections->query($sql) === TRUE){
    echo "<script>window.location.href='staff.php?delete_success=true'</script>";
}
else{
    echo 'Error: ' .$sql. "br" .$connections->error;
}


