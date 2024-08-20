<?php
session_start(); // Start the session

// Redirect to login page if the user is not logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// Include database connection
include 'db_connection.php';

// Check if an ID was passed from history.php
if (isset($_GET['rec_id'])) {
    $rec_id = $_GET['rec_id'];
    
    // Prepare the SQL query
    $sql = "
        SELECT 
            tb_record.rec_operation_date AS 'Operation Date',
            tb_record.rec_surgeon AS 'Surgeon',
            tb_record.rec_operation AS 'Operation',
            tb_record.rec_room AS 'Room',
            tb_record.rec_created_date AS 'Created Date',
            tb_record.rec_created_by AS 'Created By',
            tb_record.rec_edited_date AS 'Edited Date',
            tb_record.rec_pt_hn AS 'Patient HN',
            tb_patients.pt_name AS 'Patient Name',
            tb_patients.pt_address AS 'Patient Address',
            tb_implant_used.imu_id AS 'Implant ID',
            tb_implant_used.imu_name AS 'Implant Name',
            tb_implant_used.imu_company_id AS 'Company ID',
            tb_implant_used.imu_number AS 'Implant Number',
            tb_plate.plate_name AS 'Plate Name',
            tb_plate.plate_size AS 'Plate Size'
        FROM 
            tb_record
        INNER JOIN 
            tb_patients ON tb_record.rec_pt_hn = tb_patients.pt_hn
        INNER JOIN 
            tb_implant_used ON tb_record.rec_id = tb_implant_used.rec_id
        INNER JOIN 
            tb_plate ON tb_implant_used.imu_company_id = tb_plate.companyId
        WHERE 
            tb_record.rec_id = ?"; // Use a prepared statement to avoid SQL injection

    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("i", $rec_id); // Bind the rec_id to the query
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
        } else {
            echo "No records found.";
            exit();
        }
        $stmt->close();
    } else {
        echo "Error: Could not prepare the SQL statement.";
        exit();
    }
} else {
    echo "Error: No record ID provided.";
    exit();
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดการผ่าตัด</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2 class="mt-5">รายละเอียดการผ่าตัด</h2>
        <table class="table table-bordered mt-3">
            <tr>
                <th>Operation Date</th>
                <td><?php echo htmlspecialchars($row['Operation Date']); ?></td>
            </tr>
            <tr>
                <th>Surgeon</th>
                <td><?php echo htmlspecialchars($row['Surgeon']); ?></td>
            </tr>
            <tr>
                <th>Operation</th>
                <td><?php echo htmlspecialchars($row['Operation']); ?></td>
            </tr>
            <tr>
                <th>Room</th>
                <td><?php echo htmlspecialchars($row['Room']); ?></td>
            </tr>
            <tr>
                <th>Created Date</th>
                <td><?php echo htmlspecialchars($row['Created Date']); ?></td>
            </tr>
            <tr>
                <th>Created By</th>
                <td><?php echo htmlspecialchars($row['Created By']); ?></td>
            </tr>
            <tr>
                <th>Edited Date</th>
                <td><?php echo htmlspecialchars($row['Edited Date']); ?></td>
            </tr>
            <tr>
                <th>Patient HN</th>
                <td><?php echo htmlspecialchars($row['Patient HN']); ?></td>
            </tr>
            <tr>
                <th>Patient Name</th>
                <td><?php echo htmlspecialchars($row['Patient Name']); ?></td>
            </tr>
            <tr>
                <th>Patient Address</th>
                <td><?php echo htmlspecialchars($row['Patient Address']); ?></td>
            </tr>
            <tr>
                <th>Implant ID</th>
                <td><?php echo htmlspecialchars($row['Implant ID']); ?></td>
            </tr>
            <tr>
                <th>Implant Name</th>
                <td><?php echo htmlspecialchars($row['Implant Name']); ?></td>
            </tr>
            <tr>
                <th>Company ID</th>
                <td><?php echo htmlspecialchars($row['Company ID']); ?></td>
            </tr>
            <tr>
                <th>Implant Number</th>
                <td><?php echo htmlspecialchars($row['Implant Number']); ?></td>
            </tr>
            <tr>
                <th>Plate Name</th>
                <td><?php echo htmlspecialchars($row['Plate Name']); ?></td>
            </tr>
            <tr>
                <th>Plate Size</th>
                <td><?php echo htmlspecialchars($row['Plate Size']); ?></td>
            </tr>
        </table>
        <a href="history.php" class="btn btn-secondary mt-4">กลับไปที่หน้า History</a>
    </div>
</body>
</html>
