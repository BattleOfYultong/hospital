<?php
include '../../php/config.php';

// Get ID and Salary from POST data
$loginID = $_POST['ID'];
$salary = $_POST['Salary'];

// Check if loginID exists in salary_tbl
$checkQuery = "SELECT COUNT(*) AS count FROM salary_tbl WHERE loginID = $loginID";
$result = $connections->query($checkQuery);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $count = $row['count'];

    if ($count > 0) {
        // loginID exists, update salary
        $sql = "UPDATE salary_tbl SET Salary = $salary WHERE loginID = $loginID";

        if ($connections->query($sql) === TRUE) {
            echo "<script>window.location.href='../salaries.php?edit_success=true';</script>";
        } else {
            echo "Error updating salary: " . $connections->error;
        }
    } else {
        // loginID does not exist, insert new record
        $insertSql = "INSERT INTO salary_tbl (loginID, Salary) VALUES ($loginID, $salary)";

        if ($connections->query($insertSql) === TRUE) {
            echo "<script>window.location.href='../salaries.php?insert_success=true';</script>";
        } else {
            echo "Error inserting record: " . $connections->error;
        }
    }
} else {
    echo "Error checking loginID: " . $connections->error;
}
?>
