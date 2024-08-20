<?php
// <<<<<<< HEAD
$host = 'localhost';
$user = 'root';
$password = '';
// =======
// $host = '172.16.99.200';
// $user = 'hatyaih';
// $password = 'Com3274*';
// >>>>>>> origin/v.alpha
$database = 'implant_record';


$mysqli = new mysqli($host, $user, $password, $database);

if ($mysqli->connect_error) {
    die("การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $mysqli->connect_error);
}

if (!$mysqli->set_charset("utf8")) {
}

$sql = "SELECT id, company_name FROM tb_company";

$result = $mysqli->query($sql);

if ($result->num_rows > 0) {
    $companies = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $companies = [];
}

?>