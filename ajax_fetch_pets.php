<?php
include 'includes/config.php';

$type      = $_GET['type']      ?? '';
$breed     = $_GET['breed']     ?? '';
$gender    = $_GET['gender']    ?? '';
$age       = $_GET['age']       ?? '';
$min_price = $_GET['min_price'] ?? '';
$max_price = $_GET['max_price'] ?? '';
$name      = $_GET['name']      ?? ''; // for searching by pet name

$sql = "SELECT * FROM pets WHERE status='available'";

// Type filter
if (!empty($type)) {
    $sql .= " AND type = '".$conn->real_escape_string($type)."'";
}

// Breed filter
if (!empty($breed)) {
    $sql .= " AND breed LIKE '%".$conn->real_escape_string($breed)."%'";
}

// Gender filter
if (!empty($gender)) {
    $sql .= " AND gender = '".$conn->real_escape_string($gender)."'";
}
if (!empty($name)) {
    $sql .= " AND name = '".$conn->real_escape_string($name)."'";
}

// Age filter
if (!empty($age) && is_numeric($age)) {
    $sql .= " AND age <= ".intval($age);
}

// Price range filter
if ($min_price !== '' && is_numeric($min_price)) {
    $sql .= " AND price >= ".intval($min_price);
}
if ($max_price !== '' && is_numeric($max_price)) {
    $sql .= " AND price <= ".intval($max_price);
}
$sql .= " ORDER BY added_on DESC";

//echo $sql; exit;

$result = $conn->query($sql);
$pets = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $pets[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($pets);
