<?php
session_start();

if (isset($_SESSION['Email'])) {
    $Email = $_SESSION['Email'];
    include '../php/config.php';

    $profSql = "SELECT Email, Name, loginID, Photo, Account_Type, Position FROM account_tbl WHERE Email = '$Email'";
    $result = mysqli_query($connections, $profSql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $NameSession = $row['Name'];
        $NameEmail = $row['Email'];
        $SessionloginID = $row['loginID'];
        $Photo = "../uploads/" . $row['Photo'];
        $accountPosition = $row['Account_Type'];
        $profession = $row['Position'];

        if ($accountPosition == 1) {
            $position = "Admin";
        } elseif ($accountPosition == 2) {
            $position = "Staff";
        } else {
            $position = "Patient";
        }
    } else {
        echo "Error fetching user data or no user found.";
        exit;
    }
} else {
    echo "<script>window.location.href='../login.php?show_error=true';</script>";
    exit;
}

// Fetch doctors from the database
$doctors = [];
$sql = "SELECT loginID, Name FROM account_tbl WHERE Position = 'Doctor'";
$result = $connections->query($sql);
while ($row = $result->fetch_assoc()) {
    $doctors[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../SweetAlert/sweetalert2.min.css" />
    <link rel="stylesheet" href="../fontawesome-free-6.5.2-web/css/all.min.css" />
    <script src="../Jquery/jquery.js"></script>
    <link rel="stylesheet" href="../css/dashboard.css">
    <title>Patient</title>
</head>
<body>

    <aside>
        <div class="profiles">
            <img src="<?php echo $Photo; ?>" alt="">
            <h1><?php echo $NameSession; ?></h1>
            <h2><?php echo $position; ?></h2>
        </div>
        <ul class ="">
            <li >
                <a href="">
                    <span class="li-act">My Request</span>
                </a>
            </li>
            <li>
                <a href="crud.php">
                    <span>Payments</span>
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
                <div class="form-container">
                    <button id="requestDoctorBtn">Request Doctor</button>

                    <table>
                        <thead>
                            <tr>
                                <th>
                                    RequestNo:
                                </th>
                                <th>
                                    My Concern
                                </th>

                                <th>
                                    Doctor Assigned
                                </th>
                                <th>
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                include '../php/config.php';

                $sql = "SELECT request_tbl.requestID, request_tbl.requesterName, request_tbl.Concern, COALESCE(account_tbl.Name, 'Not Assigned') AS appointedDoctor 
                        FROM request_tbl 
                        LEFT JOIN account_tbl ON request_tbl.requestDoctor = account_tbl.loginID
                        WHERE requesterName = '$NameSession'";

                    $result = $connections->query($sql);

                            if (!$result) {
                                die("Invalid Query: " . $connections->error);
                            }

                            while ($row = $result->fetch_assoc()) {
                                $requestID = $row['requestID'];
                                echo '
                                <tr>
                                    <td>'.$row['requestID'].'</td>
                                    <td>'.$row['Concern'].'</td>
                                    <td>'.$row['appointedDoctor'].'</td>
                                    <td class="d-flex gap-2 justify-content-center align-items-center">
                                        <button type="button" class="btn btn-danger" onclick="confirmDelete('.$requestID.')">Cancel Request</button>
                                    </td>
                                </tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
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
               
                window.location.href = "delete.php?requestID=" + requestID;
            }
        });
    }

</script>
    




    <form id="hiddenRequestForm" action="process_request.php" method="POST" style="display: none;">
        <input type="hidden" name="name" id="hiddenName" value ="<?php  echo "$NameSession" ?>" readonly>
        <input type="hidden" name="concerns" id="hiddenConcerns">
        
    </form>

    <script>
        const doctors = <?php echo json_encode($doctors); ?>;

        document.getElementById('requestDoctorBtn').addEventListener('click', function() {
            let doctorOptions = '';
            doctors.forEach(doctor => {
                doctorOptions += `<option value="${doctor.loginID}">${doctor.Name}</option>`;
            });

            Swal.fire({
                title: 'Request a Doctor',
                html: `
                    <input type="text" id="name" class="swal2-input" placeholder="" value="<?php  echo "$NameSession" ?>" readonly> 
                    <input type="text" id="concerns" class="swal2-input" placeholder="Your Concerns">
                `,
                focusConfirm: false,
                preConfirm: () => {
                    const name = document.getElementById('name').value;
                    const concerns = document.getElementById('concerns').value;
                    
                    return { name: name, concerns: concerns  };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const { name, concerns, doctor } = result.value;
                    // Populate the hidden form and submit it
                    document.getElementById('hiddenName').value = name;
                    document.getElementById('hiddenConcerns').value = concerns;
                    document.getElementById('hiddenRequestForm').submit();
                }
            });
        });
    </script>

     <?php
if (isset($_GET['request_success']) && $_GET['request_success'] == 'true') {
    echo "
    <script>
        Swal.fire({
            position: 'top',
            icon: 'success',
            title: '$NameSession Your Request has been Submited',
            showConfirmButton: false,
            timer: 1500
        });
    </script>
    ";
}
?>

</body>
</html>
