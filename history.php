<?php
session_start(); // เริ่มต้นเซสชัน

// ดึงข้อมูลจากเซสชัน
$records = isset($_SESSION['records']) ? $_SESSION['records'] : [];

// ตรวจสอบว่า $_SESSION['user'] มีค่าหรือไม่ หากไม่มีให้ redirect ไปที่หน้า login
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header('Location: login.php'); // Adjust the redirect URL as needed
    exit();
}

$flname = $_SESSION['user'];
$error_message = '';

include 'db_connection.php';

if (!$mysqli->set_charset("utf8")) {
    die("Error loading character set utf8: " . $mysqli->error);
}

$sql_combined = "
    SELECT
        r.rec_id,
        r.rec_operation AS 'หัตถการ',
        r.rec_operation_date AS 'วันที่มาทำหัตถการ',
        r.rec_room AS 'ห้อง',
        r.rec_surgeon AS 'หมอ',
        r.rec_created_date AS 'วันที่สร้างรายการ',
        r.rec_created_by AS 'คนสร้างรายการ',
        r.rec_edited_date AS 'วันที่แก้ไขล่าสุด',
        r.rec_edited_by AS 'คนที่แก้ไขล่าสุด',
        r.rec_pt_hn AS 'hn ของคนไข้ที่เกี่ยวข้อง'
    FROM
        tb_record r
";

$result_combined = $mysqli->query($sql_combined);

if (!$result_combined) {
    die("Query failed: " . $mysqli->error);
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประวัติ</title>
    <!-- เชื่อมต่อกับ Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #9bc8d1;
            color: #495057;
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
        tr {
            cursor: pointer;
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
    <br>
    <div class="container">
        <h3>ตารางประวัติรวม</h3>
        <table class="table table-bordered">
            <thead class="table-secondary">
                <tr>
                    <th>วันที่มาทำหัตถการ</th>
                    <th>แพทย์ผู้ทำการผ่าตัด</th>
                    <th>หัตถการ</th>
                    <th>ห้อง</th>
                    <th>วันที่สร้างรายการ</th>
                    <th>คนสร้างรายการ</th>
                    <th>วันที่แก้ไขล่าสุด</th>
                    <th>HN ของคนไข้</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $result_combined->fetch_assoc()) { ?>
                <tr onclick="window.location.href='details.php?rec_id=<?php echo $row['rec_id']; ?>'" style="cursor: pointer;">
                    <td>
                        <?php
                        $date = new DateTime($row['วันที่มาทำหัตถการ']);
                        echo htmlspecialchars($date->format('d-m-Y')); 
                        ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['หมอ']); ?></td>
                    <td><?php echo htmlspecialchars($row['หัตถการ']); ?></td>
                    <td><?php echo htmlspecialchars($row['ห้อง']); ?></td>
                    <td>
                        <?php
                        $createdDate = new DateTime($row['วันที่สร้างรายการ']);
                        echo htmlspecialchars($createdDate->format('d-m-Y')); 
                        ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['คนสร้างรายการ']); ?></td>
                    <td>
                        <?php
                        $editedDate = new DateTime($row['วันที่แก้ไขล่าสุด']);
                        echo htmlspecialchars($editedDate->format('d-m-Y')); 
                        ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['hn ของคนไข้ที่เกี่ยวข้อง']); ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>This page is protected and accessible only after login.</p>
        <p>&copy; 2024 Hat Yai Hospital. All rights reserved.</p>
    </div>
    <div class="container">
    <div class="col-lg-12 text-center">
        <button type="button" class="btn btn-secondary mt-4 ml-2" onclick="window.location.href='home.php'">กลับไปที่หน้า Home</button>
        <button type="button" class="btn btn-secondary mt-4 ml-2" onclick="window.location.href='create.php'">ไปที่หน้า Create</button>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
