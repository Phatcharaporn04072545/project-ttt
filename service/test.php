<?php
session_start();
include '../db_connection.php'; // ตรวจสอบให้แน่ใจว่าคุณมีไฟล์นี้และตัวแปร $host, $user, $pass, $db ถูกต้อง


$_POST["pt_hn"];
$_SESSION["user"];


// get or create pt
$pt_hn = $_POST['pt_hn'];
$pt_name = $_POST['pt_name'];
$pt_age = $_POST['pt_age'];
$pt_cid = $_POST['pt_cid'];
$pt_credit = $_POST['pt_credit'];
$recOper = $_POST["rec_operation"];
$recOperDate = $_POST["rec_operation_date"];
$recRoom = $_POST["rec_room"];
$recSurgeon = $_POST["rec_surgeon"];
$countItem = $_POST["countItem"];

$sql = "SELECT * 
        FROM tb_patients 
        WHERE pt_hn = '".$pt_hn."'";
$result = mysqli_query($mysqli, $sql);        
$ptData = mysqli_fetch_assoc($result);
if (empty($ptData)) {
    // ยังไม่มีข้อมูลคนไข้
    $userSql = "INSERT INTO tb_patients (pt_hn, pt_name, pt_cid, pt_credit, created_by)
            VALUES ('$pt_hn', '$pt_name', '$pt_cid', '$pt_credit', '".$_SESSION["user"]."')";
    $resultUser = mysqli_query($mysqli, $userSql);
}





$recSql = "INSERT INTO tb_record (rec_operation, rec_operation_date, rec_room, rec_surgeon, rec_created_by, rec_pt_hn)
            VALUES ('$recOper', '$recOperDate', '$recRoom', '$recSurgeon', '".$_SESSION["user"]."', '$pt_hn')";
$resultRec = mysqli_query($mysqli, $recSql);

if ($resultRec) {
    $lastRec_id = mysqli_insert_id($mysqli);
    // echo "Record inserted successfully. Last inserted ID is: " . $last_id;
    $index = 1;
    foreach ($_POST["itemArray"] as $item) {
        // ชื่อ, หมวด, บริษัท, จำนวน
        // $item = explode('&', $_POST["itemArray"][i])[0]; // plateName=xxxxx
        parse_str($item, $parsedArray);
        $plateId = $parsedArray['plateName']; 
        $groupId = $parsedArray['groupText']; 
        $companyId = $parsedArray['company']; 
        $plateNum = $parsedArray['plateNum']; 
        
        $recSql = "INSERT INTO tb_implant_used (imu_group_id, imu_name, imu_company_id, imu_number, rec_id)
                    VALUES ('$groupId', '$plateId', '$companyId', '$plateNum', '".$lastRec_id."')";
        $resultRec = mysqli_query($mysqli, $recSql);
        
    }
}


mysqli_close($mysqli);

?>