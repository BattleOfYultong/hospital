<?php
session_start();

if (isset($_SESSION['Email'])) {
    $Email = $_SESSION['Email'];
    include '../php/config.php';

    $profSql = "SELECT Email, Name, loginID, Photo, Account_Type FROM account_tbl WHERE Email = '$Email'";
    $result = mysqli_query($connections, $profSql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $NameSession = $row['Name'];
        $NameEmail = $row['Email'];
        $SessionloginID = $row['loginID'];
        $Photo = "../uploads/" . $row['Photo'];
        $accountPosition = $row['Account_Type'];

        if ($accountPosition == 1) {
            $position = "Admin";
        } elseif ($accountPosition == 2) {
            $position = "Staff";
        } else {
            $position = "Patient";
        }
    } else {
        // Handle case where query fails or no rows are returned
        echo "Error fetching user data or no user found.";
        exit;
    }
} else {
    echo "<script>window.location.href='../login.php?show_error=true';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="../SweetAlert/sweetalert2.all.min.js"></script>
    <script src="../bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../SweetAlert/sweetalert2.min.css" />
    <link rel="stylesheet" href="../fontawesome-free-6.5.2-web/css/all.min.css" />
    <link rel="stylesheet" href="../css/dashboard.css">
    <title>Admin</title>
</head>
<body>
    <aside>
        <div class="profiles">
            <img src="<?php echo $Photo; ?>" alt="">
            <h1><?php echo $NameSession; ?></h1>
            <h2><?php echo $position; ?></h2>
        </div>
        <ul>
            <li class="">
                <a href="admin.php">
                    <span>Edit Users</span>
                </a>
            </li>
            <li>
                <a href="staffs.php">
                    <span>Staffs</span>
                </a>
            </li>
            <li>
                <a href="patient.php">
                    <span>Patients</span>
                </a>
            </li>
            <li>
                <a href="salaries.php">
                    <span class="li-act">Salaries</span>
                </a>
            </li>
            <li >
                <a href="request.php">
                    <span >Requests</span>
                </a>
            </li>
            <li>
                <a href="../php/logout.php">
                    <span>Log - Out</span>
                </a>
            </li>
        </ul>
    </aside>
    <main>
        <nav>
            <div class="title-sys">
                <h1>Hospital Management</h1>
            </div>
        </nav>
        <section>
            <div class="main-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Position</th>
                            <th>Salary</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        include '../php/config.php';
                        $sql = "SELECT account_tbl.loginID, account_tbl.Name, account_tbl.Position, salary_tbl.salary, 
                                salary_tbl.salaryID, account_tbl.Account_Type
                                FROM account_tbl
                                LEFT JOIN salary_tbl ON account_tbl.loginID = salary_tbl.loginID";
                        $result = $connections->query($sql);

                        if (!$result) {
                            die("Invalid Query: " . $connections->error);
                        }

                        while ($row = $result->fetch_assoc()) {
                            $loginID = $row['loginID'];
                            $salary = isset($row['salary']) ? $row['salary'] : 'N/A';
                            $salaryID = isset($row['salaryID']) ? $row['salaryID'] : 'N/A';
                            if($row['Account_Type'] == 1 || $row['Account_Type'] == 3 ){
                                continue;
                            }
                            echo '
                                <tr>
                                    <td>'.$loginID.'</td>
                                    <td>'.$row['Name'].'</td>
                                    <td>'.$row['Position'].'</td>
                                    <td>'.$salary.'</td>
                                    <td class="d-flex gap-2 justify-content-center align-items-center">
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalId'.$loginID.'">Edit</button>
                                       
                                    </td>
                                </tr>';
                        }
                    ?>
                    </tbody>
                </table>

                <?php
// Loop through the result set again to generate modals
$result = $connections->query($sql);

if (!$result) {
    die("Invalid Query: " . $connections->error);
}

while ($row = $result->fetch_assoc()) {
    $loginID = $row['loginID'];
    $salary = isset($row['salary']) ? $row['salary'] : 'N/A';
    $salaryID = isset($row['salaryID']) ? $row['salaryID'] : 'N/A';

    echo '
        <form action="function/editsalary.php" method = "POST" class="modal fade" id="modalId'.$loginID.'" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog" aria-labelledby="modalTitleId'.$loginID.'" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId'.$loginID.'">
                            Edit Salary
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="" class="form-label">ID</label>
                            <input type="text" class="form-control" name="ID" id="ID" aria-describedby="helpId" placeholder="" value="'.$loginID.'" readonly/>
                           
                        </div>
                        <div class="mb-3">
                            <label for="" class="form-label">Name</label>
                            <input type="text" class="form-control" name="" id="" aria-describedby="helpId" placeholder="" value="'.$row['Name'].'" readonly/>
                            
                        </div>
                        <div class="mb-3">
                            <label for="" class="form-label">Salary</label>
                            <input type="number" class="form-control" name="Salary" id="" aria-describedby="helpId" placeholder="" value="'.$salary.'"/>
                            
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </form>';
}
?>            










                <!-- Optional: Place to the bottom of scripts -->
                <script>
                    function confirmDelete(requestID) {
                        Swal.fire({
                            title: 'Are you sure?',
                            text: "You won't be able to revert this!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Yes, delete it!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "function/requestdelete.php?requestID=" + requestID;
                            }
                        });
                    }

                   
                </script>
            </div>
        </section>
    </main>
</body>
</html>
