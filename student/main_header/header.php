<?php
 
  include('../init/model/class_model.php');
       session_start();
if (!(trim((string) ($_SESSION['student_id'] ?? '')))) {
        header('location:../index.php');
        exit;
    }

$headerStudentId = (int) $_SESSION['student_id'];
$headerModel = new class_model();
$headerUser = $headerModel->student_account($headerStudentId);
$headerFirstName = trim((string) ($headerUser['first_name'] ?? ''));
$headerLastName = trim((string) ($headerUser['last_name'] ?? ''));
$headerInitials = strtoupper(substr($headerFirstName, 0, 1) . substr($headerLastName, 0, 1));
$headerInitials = $headerInitials !== '' ? $headerInitials : 'ST';
$headerPhotoUrl = (string) ($headerUser['profile_photo_url'] ?? '');

?>

<!doctype html>
<html lang="en">

 
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/libs/css/style.css">
    <link rel="stylesheet" href="../assets/vendor/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="../assets/vendor/datatables/css/dataTables.bootstrap4.css">
    <link rel="stylesheet" type="text/css" href="../assets/vendor/datatables/css/buttons.bootstrap4.css">
    <link rel="stylesheet" type="text/css" href="../assets/vendor/datatables/css/select.bootstrap4.css">
    <link rel="stylesheet" type="text/css" href="../assets/vendor/datatables/css/fixedHeader.bootstrap4.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <title>E-Request</title>
    <style>
        ul.navbar-nav li a{
            color: rgb(207, 214, 200) !important;
        }
        ul.navbar-nav li a i{
            color: rgb(207, 214, 200) !important;
        }
        .navbar-brand{
            color: rgb(255, 55, 0) !important;
        }
         .box {
            display: flex;
            flex-direction:row;
          }

          .one {
            flex: 1 1 auto;
          }

          .two {
            flex: 1 1 auto;
          }

          .three {
            flex: 1 1 auto;
           }
        .four {
            flex: 1 1 auto;
           }

           * {
    box-sizing: border-box;
}

.form-box {
    background-color: #8ac088;
    margin: auto auto;
    padding: 40px;
    border-radius: 5px;
    box-shadow: 0 0 10px #000;

}

.form-box .header-text {
    font-size: 22px;
    font-weight: 600;
    padding-bottom: 30px;
    text-align: center;
}
.form-box .form-text {
    font-size: 15px;
    color: #fff;
    font-weight: 600;
    padding-bottom: 10px;
    text-align: center;
}
.form-box input {
    margin: 10px 0px;
    border: none;
    padding: 10px;
    border-radius: 5px;
    width: 100%;
    font-size: 18px;
    font-family: 'Poppins', sans-serif;
}
.form-box input[type=checkbox] {
    display: none;
}
.form-box label {
    position: relative;
    margin-left: 5px;
    margin-right: 10px;
    top: 5px;
    display: inline-block;
    width: 20px;
    height: 20px;
    cursor: pointer;
}
.form-box label:before {
    content: "";
    display: inline-block;
    width: 20px;
    height: 20px;
    border-radius: 5px;
    position: absolute;
    left: 0;
    bottom: 1px;
    background-color: #ddd;
}
.form-box input[type=checkbox]:checked+label:before {
    content: "\2713";
    font-size: 20px;
    color: #000;
    text-align: center;
    line-height: 20px;
}
.form-box span {
    font-size: 14px;
}
.form-box button {
    background-color: #d59f2a;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    width: 100%;
    font-size: 18px;
    padding: 10px;
    margin: 20px 0px;
}
span a {
    color: #BBB;
}


/*Profile image */


#profileImage {
width: 40px;
height: 40px;
border-radius: 50%;
background:#d6821c;
font-size: 16px;
color: #fff;
margin: 0px 0;
display: flex;
align-items: center;
justify-content: center;
overflow: hidden;
position: relative;
font-weight: 600;
}

#profileImage .profile-photo-image {
width: 100%;
height: 100%;
object-fit: cover;
position: absolute;
inset: 0;
}

    </style>
</head>

<body>
    <!-- ============================================================== -->
    <!-- main wrapper -->
    <!-- ============================================================== -->
    <div class="dashboard-main-wrapper">
        <!-- ============================================================== -->
        <!-- navbar -->
        <!-- ============================================================== -->
        <div class="dashboard-header">
            <nav class="navbar navbar-expand-lg bg-white fixed-top">
               <a class="navbar-brand" href="index.php"><p style="color: #ed790c;font-size: 100%;size: 3em">𝙀-𝙍𝙀𝙌𝙐𝙀𝙎𝙏</p></a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse " id="navbarSupportedContent">

                    <ul class="navbar-nav ml-auto navbar-right-top">

                         <li class="nav-item dropdown">
                            <a class="nav-link nav-user-img" href="#" id="navbarDropdownMenuLink1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="badge badge-danger count"></span>&nbsp;<i class="fa fa-bell" style="color: black"></i></a>
                            <div class="dropdown-menu dropdown-menu-right dropdown dropdown-menu_1" aria-labelledby="navbarDropdownMenuLink1" style="width: 400px">

                            </div>
                        </li>&nbsp;&nbsp;
                         <li class="nav-item dropdown nav-user">
                            <a class="nav-link nav-user-img" href="#" id="navbarDropdownMenuLink2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-label="Open profile menu">
                                <div id="profileImage">
                                    <span><?= htmlspecialchars($headerInitials, ENT_QUOTES, 'UTF-8'); ?></span>
                                    <img class="profile-photo-image" src="<?= htmlspecialchars($headerPhotoUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="Profile photo" onerror="this.remove()">
                                </div>
                            </a>
                            
                            <div class="dropdown-menu dropdown-menu-right nav-user-dropdown" aria-labelledby="navbarDropdownMenuLink2">
                                <div class="nav-user-info" style="background-color: #666">
                                    <h5 class="mb-0 text-white nav-user-name">
                                    <center><h4 class="text-warning"><b>Student:</b> <span id="lastName"><?= htmlspecialchars(ucfirst($headerLastName), ENT_QUOTES, 'UTF-8'); ?></span>, <span id="firstName"><?= htmlspecialchars(ucfirst($headerFirstName), ENT_QUOTES, 'UTF-8'); ?></span></h4></center>
                                    </h5>
                                    <a href="logout/logout.php"><i class="fas fa-power-off mr-2"></i><span class="ml-3">Logout</span></a>
                                </div>
<!--                                 <a class="dropdown-item" href="#"><i class="fas fa-user mr-2"></i>Account</a>
                                <a class="dropdown-item" href="#"><i class="fas fa-cog mr-2"></i>Setting</a>
                                <a class="dropdown-item" href="../index.html"><i class="fas fa-power-off mr-2"></i>Logout</a> -->
                            </div>
                        </li>
                        <li class="nav-item d-none d-md-flex align-items-center ml-2">
                            <a class="topbar-logout" href="logout/logout.php" title="Logout">
                                <i class="fas fa-sign-out-alt"></i><span>Logout</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
