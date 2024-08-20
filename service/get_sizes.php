<?php
include '../db_connection.php'; // ตรวจสอบให้แน่ใจว่าคุณมีไฟล์นี้และตัวแปร $host, $user, $pass, $db ถูกต้อง


if (isset($_GET['groupId']) && !empty($_GET['groupId'])) {
    $groupId = intval($_GET['groupId']);
    $query = "SELECT DISTINCT plate_size FROM tb_plate WHERE groupId = ? ORDER BY plate_size ASC";

    if ($stmt = $mysqli->prepare($query)) {
        $stmt->bind_param('i', $groupId);
        $stmt->execute();
        $result = $stmt->get_result();

        $sizes = [];
        while ($row = $result->fetch_assoc()) {
            $sizes[] = $row['plate_size'];
        }

        echo json_encode(['sizes' => $sizes]);

        $stmt->close();
    } else {
        echo json_encode(['sizes' => []]);
    }
} else {
    echo json_encode(['sizes' => []]);
}

$mysqli->close();

?>