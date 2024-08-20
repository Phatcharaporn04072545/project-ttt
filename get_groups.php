<?php
include 'db_connection.php';

if (isset($_POST['companyId']) && !empty($_POST['companyId'])) {
    $companyId = intval($_POST['companyId']);
    $query = "SELECT id, group_name FROM tb_plate_group WHERE companyId = ?";

    if ($stmt = $mysqli->prepare($query)) {
        $stmt->bind_param('i', $companyId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<option value="' . htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($row['group_name'], ENT_QUOTES, 'UTF-8') . '</option>';
            }
        } else {
            echo '<option value="">ไม่พบหมวดหมู่</option>';
        }

      
        $stmt->close();
    } else {
        echo '<option value="">เกิดข้อผิดพลาดในการเตรียมคำสั่ง</option>';
    }
} else {
    echo '<option value="">ไม่ได้รับค่า companyId</option>';
}

?>