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
    
    <title>Staff</title>
</head>
<body>

    <aside>
        <div class="profiles">
            <img src="<?php echo "$Photo"  ?>" alt="">
            <h1><?php echo "$NameSession" ?></h1>
            <h2><?php echo "$profession" ?></h2>
        </div>

        <ul>
            <li >
                <a href="staff.php">
                    <span >Assigned Patients</span>
                </a>
            </li>

           
               <li>
                <a href="mysalary.php">
                    <span class="li-act">My Salary</span>
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
            <?php
                 $sql = "SELECT *FROM salary_tbl WHERE loginID = $SessionloginID ";
                 $result = mysqli_query($connections, $sql);
             
                 if ($result && mysqli_num_rows($result) > 0) {
                     $row = mysqli_fetch_assoc($result);
                    $salary = $row['Salary'];
                 }       

                ?>
                
                <div class="container-md w-25 rounded bg-primary d-flex justify-content-center align-items-center gap-2">
                        
                            <h1 class="text-white"><?php echo "$salary" ?></h1>
                            <h2>
                            <i class="fa-solid fa-peso-sign text-white"></i>
                            </h2>
                        
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
    
</body>

</html>