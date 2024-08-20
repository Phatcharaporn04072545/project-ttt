<?php
        session_start();

        // // ตรวจสอบว่า $_SESSION['user'] มีค่าหรือไม่ หากไม่มีให้ redirect ไปที่หน้า login
        // if (!isset($_SESSION['user'])) {
        //     header('Location: login.php');
        //     exit();
        // }

        if (isset($_POST['logout'])) {
            session_unset();
            session_destroy();
            header('Location: login.php'); // Adjust the redirect URL as needed
            exit();
        }
        $flname = $_SESSION['user'];
        $error_message = '';

?>


<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Menu </title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
         body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #9bc8d1 0%, #ffffff 100%);
            margin: 0;
            padding: 20px;
            margin: 0;
            min-height: 100vh;
        }

        .footer {
            bottom: 0;
            left: 0;
            width: 100%;
            color: rgba(0, 0, 0, 0.4);
            text-align: center;
            padding: 1px;
            font-size: 14px;
        }

        .navbar-brand img {
            height: 50px;
        }
        .img-custom {
            
            max-width: 45px;
            height: auto; 
        }
    </style>
</head>
<body>
        <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
            <img src="image/HY.png" alt="Login" class="img-fluid img-custom mb-1">
                <a class="navbar-brand ml-3 mb-1" href="#">โรงพยาบาลหาดใหญ่</a>
                <div class="ml-auto d-flex align-items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-person-fill mr-2 "style="color: #ffffff" viewBox="0 0 16 16">
                    <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
                </svg>
                    
                    <span class="mr-4"style="color: #ffffff;"><?php echo htmlspecialchars($flname); ?></span>
                    <form method="post" class="form-inline ">
                        <button type="submit" name="logout" class="btn btn-danger">
                            Logout
                        </button>
                    </form>
                </div>
        </nav>

    <div class="container mt-3 pt-5">
        <div class="text-center mb-5">
            <p class=" row display-4">รายงานการใช้วัสดุทางการแพทย์สำหรับการผ่าตัด</p>
            <h3 class="lead">กลุ่มงานพยบาลผู้ป่วยผ่าตัด กลุ่มการพยาบาล โรงพยาบาลหาดใหญ่</h3>
        </div>
    </div>
<div class="container mt-4 ">
        <div class="row justify-content-center">
            <div class="col-md-3 d-flex justify-content-center mb-4">  
            <div class="card-text center-around" >
                <a href="create.php" id="createLink">
                    <img src="https://img.icons8.com/?size=100&id=BhcrBzMkjelP&format=png&color=000000" alt="Card Image" class="card-img-top" style="width: 190px; height: 190px;">
                </a>
                <div class="row card-body"style="color: black">
                    <p class="row card-text mt-3" style="font-size: 20px;">เพิ่มข้อมูลการทำหัตถการ</p>
                </div>
            </div>
        </div> 
            <div class="card-text center-around" >
                    <a href="history.php">
                        <img src="https://img.icons8.com/?size=100&id=5iACQbjkgT9N&format=png&color=000000" alt="Card Image" class="card-img-top" style="width: 190px; height: 190px;">
                    </a>
                    <div class="row card-body "style="color: black">
                    <p class="row card-text mt-3 ml-3" style="font-size: 20px;">ข้อมูลการทำหัตถการ</p>

                    </div>
                </div>
            </div> 
    </div>
</div>

    <div class = "footer"style="padding-top: 100px;">
        <p>This page is protected and accessible only after login.</p>
        <p>&copy; 2024 Hat Yai Hospital. All rights reserved.</p>
    </div>
    
</body>
</html>
