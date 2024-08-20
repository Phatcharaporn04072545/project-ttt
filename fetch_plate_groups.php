<?php
$host = 'localhost';
$db = 'implant_record';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    exit();
}

if (isset($_POST['companyId'])) {
    $companyId = intval($_POST['companyId']);

    try {
        // ดึงข้อมูลจาก tb_plate_group ตาม companyId
        $query = "SELECT id, group_name FROM tb_plate_group WHERE company_id = :companyId";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':companyId', $companyId, PDO::PARAM_INT);
        $stmt->execute();
        $plateGroups = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // ตรวจสอบว่ามีข้อมูลหรือไม่
        if (empty($plateGroups)) {
            echo '<option value="">ไม่พบอุปกรณ์</option>';
        } else {
            // สร้างตัวเลือกสำหรับฟิลด์ select
            foreach ($plateGroups as $group) {
                echo '<option value="' . htmlspecialchars($group['id']) . '">' . htmlspecialchars($group['group_name']) . '</option>';
            }
        }
    } catch (PDOException $e) {
        echo '<option value="">เกิดข้อผิดพลาดในการโหลดข้อมูล</option>';
    }
} else {
    echo '<option value="">เกิดข้อผิดพลาดในการโหลดข้อมูล</option>';
}
?>
