<?php
session_start();
include 'includes/config.php';

$type      = $_GET['type']      ?? '';
$breed     = $_GET['breed']     ?? '';
$gender    = $_GET['gender']    ?? '';
$age       = $_GET['age']       ?? '';
$min_price = $_GET['min_price'] ?? '';
$max_price = $_GET['max_price'] ?? '';
$name      = $_GET['name']      ?? '';

// Check logged-in user (only adopter/volunteer matter)
$user_id = $_SESSION['user_id'] ?? 0;

$sql = "SELECT p.*,
        (
          SELECT COUNT(*) FROM adoption_requests ar 
          WHERE ar.pet_id = p.pet_id AND ar.user_id = $user_id
        ) AS already_requested
        FROM pets p
        WHERE p.status='available'";

// Type filter
if (!empty($type)) {
    $sql .= " AND p.type = '".$conn->real_escape_string($type)."'";
}

// Breed filter
if (!empty($breed)) {
    $sql .= " AND p.breed LIKE '%".$conn->real_escape_string($breed)."%'";
}

// Gender filter
if (!empty($gender)) {
    $sql .= " AND p.gender = '".$conn->real_escape_string($gender)."'";
}

if (!empty($name)) {
    $sql .= " AND p.name = '".$conn->real_escape_string($name)."'";
}

// Age filter
if (!empty($age) && is_numeric($age)) {
    $sql .= " AND p.age <= ".intval($age);
}

// Price range filter
if ($min_price !== '' && is_numeric($min_price)) {
    $sql .= " AND p.price >= ".intval($min_price);
}
if ($max_price !== '' && is_numeric($max_price)) {
    $sql .= " AND p.price <= ".intval($max_price);
}

$sql .= " ORDER BY p.added_on DESC";

$result = $conn->query($sql);
$pets = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $row['already_requested'] = (int)$row['already_requested']; // ensure integer in JSON
        $pets[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($pets);
?>
