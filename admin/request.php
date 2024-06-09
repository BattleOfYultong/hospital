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
                    <span>Salaries</span>
                </a>
            </li>
            <li class="li-act">
                <a href="request.php">
                    <span class="li-act">Requests</span>
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
                            <th>Request ID</th>
                            <th>Name</th>
                            <th>Concern</th>
                            <th>Appointed Doctor</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            include '../php/config.php';
                            $sql = "SELECT request_tbl.requestID, request_tbl.requesterName, request_tbl.Concern, COALESCE(account_tbl.Name, 'Not Assigned') AS appointedDoctor 
                             FROM request_tbl 
                            LEFT JOIN account_tbl ON request_tbl.requestDoctor = account_tbl.loginID";
                            $result = $connections->query($sql);

                            if (!$result) {
                                die("Invalid Query: " . $connections->error);
                            }

                            while ($row = $result->fetch_assoc()) {
                                $requestID = $row['requestID'];
                                echo '
                                    <tr>
                                        <td>'.$requestID.'</td>
                                        <td>'.$row['requesterName'].'</td>
                                        <td>'.$row['Concern'].'</td>
                                        <td> '.$row['appointedDoctor'].'</td>
                                        <td class="d-flex gap-2 justify-content-center align-items-center">
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal'.$requestID.'">Appoint</button>
                                            <button type="button" class="btn btn-danger" onclick="confirmDelete('.$requestID.')">Delete</button>
                                        </td>
                                    </tr>';
                            }
                        ?>
                    </tbody>
                </table>
                <!-- Modal for each row -->
                <?php
                    include '../php/config.php';
                    $result = $connections->query($sql);
                    if (!$result) {
                        die("Invalid Query: " . $connections->error);
                    }
                    while ($row = $result->fetch_assoc()) {
                        $requestID = $row['requestID'];
                ?>
                <form action="function/requestadd.php" method="post" class="modal fade" id="exampleModal<?php echo $requestID; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-md">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title text-center" id="exampleModalLabel">Appoint Doctor</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="requestID" class="form-label">RequestID</label>
                                    <input type="text" name="requestID" class="form-control" id="requestID" value="<?php echo $requestID; ?>" readonly>
                                </div>

                                <div class="mb-3">
                                    <label for="" class="form-label">Name</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name=""
                                        id=""
                                        aria-describedby="helpId"
                                        placeholder=""
                                        value="<?php echo $row['requesterName']; ?>"
                                        readonly/>
                                   
                                </div>

                                <div class="mb-3">
                                    <label for="" class="form-label">Concern</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name=""
                                        id=""
                                        aria-describedby="helpId"
                                        placeholder=""
                                        value="<?php echo $row['Concern']; ?>"
                                         
                                         readonly />
                                  
                                </div>
                                <div class="mb-3">
                                    <label for="doctorSelect" class="form-label">Select Doctor</label>
                                    <select class="form-select" id="doctorSelect" name="DoctorID">
                                        <?php
                                            // Query to fetch doctors from logintbl where role is "doctor"
                                            $doctorSql = "SELECT loginID, Name FROM account_tbl WHERE Position = 'doctor'";
                                            $doctorResult = mysqli_query($connections, $doctorSql);
                                            if ($doctorResult && mysqli_num_rows($doctorResult) > 0) {
                                                while ($doctorRow = mysqli_fetch_assoc($doctorResult)) {
                                                    echo '<option value="' . $doctorRow['loginID'] . '">' . $doctorRow['Name'] . '</option>';
                                                }
                                            }
                                        ?>
                                    </select>
                                </div>
                                <!-- Include other form fields as needed -->
                            </div>
                            <div class="modal-footer d-flex gap-2">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </div>
                </form>
                <?php } ?>



            </div>
        </section>
    </main>

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
</body>
<!---for Containers --->
<script>
    function previewPhoto() {
      const fileInput = document.getElementById("imgup");
      const previewImg = document.getElementById("imagedis");
      const file = fileInput.files[0];

      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          previewImg.src = e.target.result;
          previewImg.style.display = "block";
        };
        reader.readAsDataURL(file);
      } else {
        previewImg.src = "#";
        previewImg.style.display = "none";
      }
    }

    function previewPhoto2() {
      const fileInput = document.getElementById("imgup2");
      const previewImg = document.getElementById("imagedis2");
      const file = fileInput.files[0];

      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          previewImg.src = e.target.result;
          previewImg.style.display = "block";   
        };
        reader.readAsDataURL(file);
      } else {
        previewImg.src = "#";
        previewImg.style.display = "none";
      }
    }

    function previewPhoto3() {
      const fileInput = document.getElementById("imgup3");
      const previewImg = document.getElementById("imagedis3");
      const file = fileInput.files[0];

      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          previewImg.src = e.target.result;
          previewImg.style.display = "block";
        };
        reader.readAsDataURL(file);
      } else {
        previewImg.src = "#";
        previewImg.style.display = "none";
      }
    }
</script>


</html>
