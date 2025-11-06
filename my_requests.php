<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'includes/config.php';
$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Adoption Requests</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container my-5">
    <h2 class="mb-4 text-center">📄 My Adoption Requests</h2>

    <div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
        <thead class="table-dark text-center">
            <tr>
                <th>Pet</th>
                <th>Type</th>
                <th>Breed</th>
                <th>Price (₹)</th>
                <th>Requested On</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody class="text-center">
            <?php
            $query = "SELECT ar.requested_on, ar.status, ar.reason, 
                             p.name, p.type, p.breed, p.image, p.price
                      FROM adoption_requests ar
                      JOIN pets p ON ar.pet_id = p.pet_id
                      WHERE ar.user_id = ?
                      ORDER BY ar.requested_on DESC";

            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>
                                <img src='assets/images/pet-uploads/{$row['image']}' 
                                     alt='{$row['name']}' width='70' class='rounded mb-2'><br>
                                <strong>{$row['name']}</strong>
                            </td>
                            <td>{$row['type']}</td>
                            <td>{$row['breed']}</td>
                            <td>₹" . number_format($row['price'], 0) . "</td>
                            <td>{$row['requested_on']}</td>
                            <td>
                                <span class='badge text-bg-" . statusColor($row['status']) . "'>
                                    " . ucfirst($row['status']) . "
                                </span>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='6' class='text-center'>🐾 You haven’t made any adoption requests yet.</td></tr>";
            }

            function statusColor($status) {
                return match ($status) {
                    'approved' => 'success',
                    'rejected' => 'danger',
                    default => 'warning',
                };
            }
            ?>
        </tbody>
    </table>
    </div>

    <div class="d-flex justify-content-between mt-4">
        <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
        <a href="view_pets.php" class="btn btn-primary">🐾 Request for Adoption</a>
    </div>
</body>
</html>
