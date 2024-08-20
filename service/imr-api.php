<?php
include '../db_connection.php'; // ตรวจสอบให้แน่ใจว่าคุณมีไฟล์นี้และตัวแปร $host, $user, $pass, $db ถูกต้อง


if(isset($_POST["fnc"])){
    $fnc = $_POST["fnc"];
    if($fnc == "getPlateByGroup"){
        $groupId = $_POST['groupId'];
        $sql = "SELECT * 
                FROM tb_plate 
                WHERE groupId = '$groupId'";
        $result = mysqli_query($mysqli, $sql);        
        mysqli_close($mysqli);
        $plateData = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $resultQuery = [];
        $resultQuery = $plateData;
        print_r(json_encode($resultQuery));  
    }
}else{

}



?>