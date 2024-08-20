<?php
session_start();

// ตรวจสอบว่า $_SESSION['user'] มีค่าหรือไม่ หากไม่มีให้ redirect ไปที่หน้า login
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// Handle logout
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit();
}

$hn = $_SESSION['hn'] ?? '';
$flname = $_SESSION['user'] ?? '';
$flname_pt = '';
$age_year = '';
$procedure = $date = $room = $doctor = "";
$error_message = ''; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['hn'])) {
        $hn = $_POST['hn'];

        $apiurl = "http://61.19.25.200/api/pmk/get_data/";
        $ptdata = array('fnc' => 'patients_opd_ipd', 'hn' => $hn);

        $options = array(
            'http' => array(
                'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($ptdata),
            ),
        );

        $context = stream_context_create($options);
        $result = file_get_contents($apiurl, false, $context);

        if ($result !== FALSE) {
            $ptdata = json_decode($result, true);
            if (isset($ptdata['ptdata'][0])) {
                $flname_pt = $ptdata['ptdata'][0]['flname'] ?? '';
                $age_year = $ptdata['ptdata'][0]['age_year'] ?? '';
            } else {
                $error_message = "กรอก HN ไม่ถูกต้อง";
            }
        } else {
            $error_message = "ไม่สามารถเรียก API ได้";
        }
    } else {
        $error_message = "กรุณากรอก HN";
    }
}

include 'db_connection.php';
?>


<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>กรอกข้อมูลการผ่าตัด</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        .horizontal-form {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
    </style>

    <script>
        $(function() {

            // console.log('x');
            bindDataToTable();
        })


        function onClickAddItemToPreview(){
            // TODO validate input
            let companyElement = document.getElementById("companyId");
            let companyName = companyElement.value;
            let companyText = companyElement.options[companyElement.selectedIndex].innerText;

            let titleElement = document.getElementById("plate_type");
            let titleName = titleElement.value;
            let titleText = titleElement.options[titleElement.selectedIndex].innerText;

            let groupElement = document.getElementById("groupId");
            let groupName = groupElement.value;
            let groupText = groupElement.options[groupElement.selectedIndex].innerText;

            let itemElement = document.getElementById("plate_number").value;
  
            if(companyName != "0" && titleName != "0" && groupName != "0" && itemElement != "0"){
                if(getCookie("countItem") == null){
                    // first time create
                    setCookie("countItem", 1, 1);
                    setCookie("item_1", "plateName=" + titleName + "&groupText=" + groupName + "&company=" + companyName + "&plateNum=" + itemElement, 1);
                    setCookie("itemDisplay_1", "plateName=" + titleText + "&groupText=" + groupText + "&company=" + companyText.trim() + "&plateNum=" + itemElement, 1);
                }else{
                    let itemNum = Number(getCookie("countItem")) + 1;
                    setCookie("item_" + itemNum, "plateName=" + titleName + "&groupText=" + groupName + "&company=" + companyName + "&plateNum=" + itemElement, 1);
                    setCookie("itemDisplay_" + itemNum, "plateName=" + titleText + "&groupText=" + groupText + "&company=" + companyText.trim() + "&plateNum=" + itemElement, 1);
                    setCookie("countItem", itemNum, 1);
                }
                // let item1 = getCookie("item_1");
                // console.log(item1);
                bindDataToTable();
                clearInputText();
            }
        }

        function clearInputText(){
            var companyElement = document.getElementById("companyId");
            if (companyElement) {
                companyElement.value = "0";
                var event = new Event('change');
                companyElement.dispatchEvent(event);
            }
            var groupElement = document.getElementById("groupId");
            if (groupElement) {
                groupElement.value = "0";
                var event = new Event('change');
                groupElement.dispatchEvent(event);
            }

            var typeElement = document.getElementById("plate_type");
            if (typeElement) {
                typeElement.value = "0";
                var event = new Event('change');
                typeElement.dispatchEvent(event);
            }
            var numElement = document.getElementById("plate_number");
            numElement.value = "";
        }

        function delItem(itemNum){
            // console.log("u del item: " + itemNum);
            deleteCookie('item_' + itemNum);
            deleteCookie('itemDisplay_' + itemNum);

            var currentItemNum = getCookie("countItem");
            setCookie("countItem", Number(currentItemNum)-1, 1);
            let realIndex = 1;
            for (let i = 1; i <= currentItemNum; i++) {
                let item = getCookie("item_" + i)
                if(item != null){
                    renameCookie('item_'+i, 'item_'+realIndex);
                    renameCookie('itemDisplay_'+i, 'itemDisplay_'+realIndex);
                    realIndex++;
                }
            }
            bindDataToTable();
        }

        function renameCookie(oldName, newName) {
            var value = getCookie(oldName);
            if (value) {
                deleteCookie(oldName); // Delete the old cookie
                setCookie(newName, value, 1); // Set the new cookie with the same value and a desired expiration time
            }
        }

        function bindDataToTable(){
            // let item1 = getCookie("item_1");
            let countItem = parseInt(getCookie("countItem"));
            if (isNaN(countItem)) {
                console.error("Invalid countItem cookie value");
            } else {
                let tableItemPreview = document.getElementById("tableItemPreview");
                if (tableItemPreview) {
                    const tbody = tableItemPreview.getElementsByTagName('tbody')[0];
                    if (tbody) {
                        tbody.innerHTML = '';
                        for (let i = 1; i <= countItem; i++) {
                            let cookieValue = getCookie("itemDisplay_" + i);
                            if (cookieValue) {
                                let item = cookieValue.split("&");
                                let row = document.createElement('tr');
                                item.forEach(data => {
                                    let keyValue = data.split("=");
                                    if (keyValue.length == 2) {
                                        let nameCell = document.createElement('td');
                                        nameCell.textContent = keyValue[1];
                                        row.appendChild(nameCell);
                                    } else {
                                        console.error(`Malformed cookie data for item_${i}: ${data}`);
                                    }
                                });

                                // craete del button
                                let nameCell = document.createElement('td');
                                nameCell.innerHTML = "<button type='button' onclick='delItem(&#39;" + i + "&#39;)' class='btn btn-danger btn-sm-2'>"+"<i class='fas fa-trash-alt'></i>" +"</button>";

                                row.appendChild(nameCell);

                                tbody.appendChild(row);
                            } else {
                                console.error(`Cookie item_${i} not found`);
                            }
                        }
                    } else {
                        console.error("No tbody element found in tableItemPreview");
                    }
                } else {
                    console.error("No table element found with id tableItemPreview");
                }
            }

        }

        function setCookie(name, value, days) {
          let date = new Date();
          date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
          let expires = "expires=" + date.toUTCString();
          document.cookie = name + "=" + value + ";" + expires + ";path=/";
        }

        function getCookie(name) {
            let nameEQ = name + "=";
            let ca = document.cookie.split(';');
            for (let i = 0; i < ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) == ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
            }
            return null;
        }

        function deleteCookie(name) {
          document.cookie = name + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        }

        function onClickSubmitCreateNewRec(){
            let isCompleted = 1;
            let hn = document.getElementById("hn");
            hn.classList.remove("is-invalid");
            if(hn.value == ""){
                hn.classList.add("is-invalid");
                isCompleted = 0;
            }

            let flname = document.getElementById("flname");
            flname.classList.remove("is-invalid");
            if(flname.value == ""){
                flname.classList.add("is-invalid");
                isCompleted = 0;
            }
            
            let procedure = document.getElementById("procedure");
            procedure.classList.remove("is-invalid");
            if(procedure.value == ""){
                procedure.classList.add("is-invalid");
                isCompleted = 0;
            }

            let date = document.getElementById("date");
            date.classList.remove("is-invalid");
            if(date.value == ""){
                date.classList.add("is-invalid");
                isCompleted = 0;
            }

            let doctor = document.getElementById("doctor");
            doctor.classList.remove("is-invalid");
            if(doctor.value == ""){
                doctor.classList.add("is-invalid");
                isCompleted = 0;
            }

            if(isCompleted == 1){
                let countItem = getCookie("countItem");
                let itemArray = Array.from({ length: countItem }, (_, i) => getCookie(`item_${i + 1}`));

                $.ajax({
                    url: "./service/test.php",
                    type: "POST",
                    data: {
                        fnc: "createNewRec",
                        pt_hn: hn.value,
                        pt_name: flname.value,
                        pt_age: document.getElementById("age_year").value,
                        pt_cid: document.getElementById("pt_cid").value,
                        pt_credit: document.getElementById("pt_credit").value,
                        rec_operation: procedure.value,
                        rec_operation_date: date.value,
                        rec_room: room.value,
                        rec_surgeon: doctor.value,
                        countItem: countItem,
                        itemArray: itemArray
                    },
                    success: function(data) {
                      console.log(data);
                    },
                    error:function(e){
                        console.log(e);
                    }
                });



                Swal.fire({
                    title: "บันทึกข้อมูลเรียบร้อย",
                    text: "กดปุ่ม ok เพื่อกลับสู่หน้าหลัก",
                    icon: "success"
                });
            }else{
                window.scrollTo(0, 0);
            }
        }
        function onClickGoToNextPage() {
        // เพิ่มโค้ดสำหรับนำทางไปยังหน้าถัดไป
        window.location.href = "history.php"; 
    }
    </script>
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
        <div class="container mt-5"style="padding-top: 25px;">
            <div class="row justify-content-start">
                <div class="col-md-8 col-lg-5">
                    <div class="card shadow-sm">
                        <div class="card-body"style="padding: 10px;">
                                <form method="POST" action="" class="d-flex flex-column">
                                    <div class="m-2">
                                        <label for="hn" class="form-label">กรุณากรอก HN :</label> <label class="text-danger">*</label>
                                        <input type="text" 
                                               id="hn" 
                                               name="hn"
                                               class="form-control mb-3"style="margin-right: 0.5rem; width: 300px;" value="<?php echo htmlspecialchars($hn); ?>" 
                                               placeholder="กรอก HN ที่นี่">
                                                    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

                                                    <button type="submit" class="btn btn btn-secondary">
                                                        <i class="fas fa-search"></i> ค้นหา
                                                    </button>
                                                </div>
                                            </form>
                                    </div>
                                </div>            
                            </div>
                        </div>
                    </div>

                <div class="container">
                    <div class="row">
                        <div class="col-12 mt-4"> 
                            <div class="card mb-4">
                                <div class="card-body">
                            <div class="card-body container ">
                            <h2 class="card-title text-center">กรอกข้อมูลการผ่าตัด</h2>
                            <form method="POST" action="" class="horizontal-form"style="padding-top: 20px;"jo>
                                <div class="form-group">
                                    <label for="flname">ชื่อ-นามสกุล:</label><label class="text-danger">*</label>
                                    <input type="text" id="flname" name="flname" class="form-control" value="<?php echo isset($ptdata[0]['flname']) ? htmlspecialchars($ptdata[0]['flname']) : ''; ?>">

                                </div>
                                
                                <div class="form-group">
                                    <label for="age_year">อายุ (ปี) :</label>
                                    <input type="text" id="age_year" name="age_year" class="form-control" value="<?php echo isset($ptdata[0]['age_year']) ? htmlspecialchars($ptdata[0]['age_year']) : ''; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="age_cid">เลขบัตรประชาชน :</label>
                                    <input type="text" 
                                        id="pt_cid" 
                                        name="pt_cid" 
                                        class="form-control" 
                                        value="<?php echo isset($ptdata[0]['id_card']) ? htmlspecialchars($ptdata[0]['id_card']) : ''; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="age_cid">สิทธิการรักษา :</label>
                                    <input type="text" 
                                        id="pt_credit" 
                                        name="pt_credit" 
                                        class="form-control" 
                                        value="<?php echo isset($ptdata[0]['credit']) ? htmlspecialchars($ptdata[0]['credit']) : ''; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="procedure">หัตถการ:</label><label class="text-danger">*</label>
                                    <input type="text" id="procedure" name="procedure" class="form-control" value="<?php echo htmlspecialchars($procedure); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="date">วันที่:</label><label class="text-danger">*</label>
                                    <input type="date" id="date" name="date" class="form-control" value="<?php echo htmlspecialchars($date); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="room">ห้อง:</label>
                                    <input type="text" id="room" name="room" class="form-control" value="<?php echo htmlspecialchars($room); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="doctor">แพทย์ผู้ทำการผ่าตัด:</label><label class="text-danger">*</label>
                                    <input type="text" id="doctor" name="doctor" class="form-control" value="<?php echo htmlspecialchars($doctor); ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
                   
                    <div id="company1" class="container">
                        <div class="card form p-4 mb-3">
                        <div class="row ml-5">
                            <div class="form-group">
                                <label for="companyId">บริษัท:</label>
                                <select id="companyId" name="companyId" class="form-control "style="max-width: 200px;">
                                    <option value="0">เลือกบริษัท</option>
                                    <?php foreach ($companies as $comp) : ?>
                                        <option value="<?php echo htmlspecialchars($comp['id']); ?>">
                                            <?php echo htmlspecialchars($comp['company_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                                <div class="col-lg-3 mb-3">
                                    <label for="groupId">หมวดหมู่:</label>
                                    <select id="groupId" name="groupId" class="form-control">
                                        <option value="">เลือกหมวดหมู่</option>
                                    </select>
                                </div>
                                <div class="col-lg-3">
                                    <label for="size">ชนิดวัสดุ:</label>
                                    <select id="plate_type" name="plate_type" class="form-control">
                                        <option value="">เลือกชนิด</option>                                        
                                    </select>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label for="size">จำนวน:</label>
                                    <input type="number"
                                           id="plate_number" 
                                           name="plate_number" 
                                           class="form-control"
                                           min="1" 
                                           value="">
                                </div>
                                <div class="row mt-3">
                                    <div class="col d-flex justify-content-center align-items-center">
                                        <button type="button" onclick="onClickAddItemToPreview()" class="btn btn-success">+ เพิ่มวัสดุ</button>
                                    </div>
                                </div>

                            </div>
                        </div>
                        
                        <div class="card p-3">
                            <div class="table-responsive">
                                <table id="tableItemPreview" class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">อุปกรณ์</th>
                                            <th scope="col">หมวด</th>
                                            <th scope="col">บริษัท</th>
                                            <th scope="col">จำนวน</th>
                                            <th scope="col"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>  
                <script>
                $(document).ready(function() {
                    $('#companyId').change(function() {
                        var companyId = $(this).val();
                        
                        $.ajax({
                            url: 'get_groups.php',
                            type: 'POST',
                            data: { companyId: companyId },
                            success: function(response) {
                                $('#groupId').html('<option value="0">เลือกหมวดหมู่</option>'); // Clear existing options
                                $('#groupId').append(response);
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                
                            }
                        });
                    });
                });
                </script>
                    <script>
                        document.getElementById('groupId').addEventListener('change', function() {
                            var groupId = this.value;

                            if (groupId) {
                                $.ajax({
                                    url: 'service/imr-api.php',
                                    type: 'POST',
                                    data: { fnc: 'getPlateByGroup', groupId: groupId },
                                    success: function(response) {
                                        // console.log(response);
                                        let data = JSON.parse(response);
                                        // console.log(data);
                                        var sizeSelect = document.getElementById('plate_type');
                                        sizeSelect.innerHTML = '<option value="0">เลือกชนิด</option>'; // Clear existing options
                                        let size = Object.keys(data).length;
                                        if (size > 0) {
                                            console.log("1");
                                            data.forEach(item => { // ใช้ชื่อ 'item' แทน 'size' เพื่อหลีกเลี่ยงการซ้ำกัน
                                                var option = document.createElement('option');
                                                option.value = item.id;
                                                option.textContent = item.plate_name;
                                                sizeSelect.appendChild(option);
                                            });
                                        } else {
                                            console.log("2");
                                            var option = document.createElement('option');
                                            option.value = '';
                                            option.textContent = 'ไม่มีขนาด';
                                            sizeSelect.appendChild(option);
                                        }
                                    },
                                    error: function(jqXHR, textStatus, errorThrown) {
                                        document.getElementById('plate_type').innerHTML = '<option value="">เลือกชนิด</option>';
                                    }
                                });
                            } else {
                                document.getElementById('plate_type').innerHTML = '<option value="">เลือกชนิด</option>';
                            }
                        });
                    </script>
                </form>
                <div class="col-lg-12 text-center">
                    <button type="button" 
                            class="btn btn-primary mt-4"
                            onclick="onClickSubmitCreateNewRec()">บันทึกข้อมูล</button>
                            <div class="col-lg-12 text-center">
                </div>
            </div>
            
        </div>
    </div>
    <div class="container">
    <!-- <div class="col-lg-12 text-center">
        <button type="button" class="btn btn-secondary mt-4 ml-2" onclick="window.location.href='home.php'">กลับไปที่หน้า Home</button>
        <button type="button" class="btn btn-secondary mt-4 ml-2" onclick="window.location.href='history.php'">ไปที่หน้า History</button>
    </div> -->
    <div class="footer"style="padding-top: 20px;">
        <p>This page is protected and accessible only after login.</p>
        <p>&copy; 2024 Hat Yai Hospital. All rights reserved.</p>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</body>
</html>