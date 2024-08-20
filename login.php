<?php
session_start();

// ฟังก์ชันล็อกเอาท์
if (isset($_POST['logout'])) {
    session_unset(); // ลบตัวแปรเซสชันทั้งหมด
    session_destroy(); // ทำลายเซสชัน
    header('Location: login.php'); // ไปยังหน้าล็อกอิน
    exit();
}

$error_message = "";

// จัดการข้อมูลการล็อกอิน
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['uname'];
    $password = $_POST['pword'];
    $utype = $_POST['utype']; 

    // แปลงประเภทผู้ใช้เป็นค่าตัวเลข
    if ($utype == 'doctor') {
        $utype_value = 1;
    } else if ($utype == 'staff') {
        $utype_value = 2;
    } 

    // URL ของ API
    $api_url = 'http://172.16.99.200/api/pmk/get_data/';
    
    // ข้อมูลที่จะส่งไปยัง API
    $data = array(
        'fnc' => 'chk_utable',
        'uname' => $username,
        'pword' => $password,
        'utype' => $utype_value 
    );

    // ตัวเลือกสำหรับ HTTP request
    $options = array(
        'http' => array(
            'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
        ),
    );

    // สร้าง context สำหรับ HTTP request
    $context  = stream_context_create($options);
    
    // ส่ง request ไปยัง API และรับ response
    $result = file_get_contents($api_url, false, $context);

    // ตรวจสอบว่าเรียก API สำเร็จหรือไม่
    if ($result === FALSE) {
        die('Error calling API');
    }

    // ถอดรหัส JSON response จาก API
    $response = json_decode($result, true);

    // ตรวจสอบการล็อกอินตาม response จาก API
    if (isset($response['data']) && $response['data'] == '1') {
        // กำหนดตัวแปรเซสชัน
        $_SESSION['user'] = $response['flname'];
        $_SESSION['utype'] = $response['utype'];
        $_SESSION['login'] = $response['login'];
        $_SESSION['userlogin'] = $response['userlogin'];
        
        // ไปยังหน้า dashboard.php เมื่อล็อกอินสำเร็จ
        header('Location: home.php');
        exit();
    } else {
        // กรณีล็อกอินไม่สำเร็จ
        $error_message = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #9bc8d1 0%, #ffffff 80%);
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .btn-custom {
            background-color: #007bff;
            color: white;
        }
        .error-message {
            color: red;
            margin-top: 10px;
        }
        .login-form-container {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }
        .img-custom {
            
            max-width: 200px;
            height: auto; 
        }
        .sub-title {
            color: #555; 
            font-size: 35px;
            margin-bottom: 20px;
            
        }
        .card-body{
            border: 2px solid #007bff; 
            border-radius: 1rem;
        }
    </style>
</head>
<body class="bg-light d-flex flex-column align-items-center justify-content-center">
    
    <div class="main-content mb-4">
        <h2 class="sub-title">ยินดีต้อนรับเข้าสู่ระบบ</h2>
    </div>


 

    <div class="container">
    <div class="d-flex justify-content-between align-items-start ml-1">
         <div class="d-flex flex-column align-items-center">
            <img src="image/HY.png" alt="Login" class="img-fluid img-custom mb-5">
            <h3 class="text-center mb-5">ระบบรายงานการใช้วัสดุทางการแพทย์สำหรับการผ่าตัด</h3>
            <p class="text-center">กลุ่มงานพยบาลผู้ป่วยผ่าตัด กลุ่มการพยาบาล โรงพยาบาลหาดใหญ่</p>
        </div>
        <div class="card" style="width: 100%; max-width: 400px;">
            <div class="card-body">
                <h2 class="text-center mb-5">เข้าสู่ระบบบัญชีของคุณ</h2>
                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="login-form-container">
                    <div class="form-group mb-4">
                        <label for="uname" class="form-label">ชื่อผู้ใช้:</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                            </div>
                            <input type="text" class="form-control" id="uname" name="uname" required>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label for="pword" class="form-label">รหัสผ่าน:</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            </div>
                            <input type="password" class="form-control" id="pword" name="pword" required>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label">ประเภทผู้ใช้:</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" id="doctor" name="utype" value="doctor" required>
                            <label class="form-check-label" for="doctor">แพทย์</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" id="staff" name="utype" value="staff" required>
                            <label class="form-check-label" for="staff">พยาบาล / เจ้าหน้าที่</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-custom mb-4 w-100">เข้าสู่ระบบ</button>

                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger mt-3" role="alert">
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</div>

  
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
</body>
</html>
