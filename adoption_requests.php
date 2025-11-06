<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

include 'includes/config.php';

// Handle status update
if (isset($_GET['action'], $_GET['id'])) {
    $action = $_GET['action'];
    $request_id = intval($_GET['id']);

    if (in_array($action, ['approved', 'rejected'])) {
        $update = $conn->prepare("UPDATE adoption_requests SET status = ? WHERE request_id = ?");
        $update->bind_param("si", $action, $request_id);
        $update->execute();

        // If approved → also mark pet as adopted
        if ($action === 'approved') {
            $petUpdate = $conn->prepare("UPDATE pets p 
                                         JOIN adoption_requests ar ON p.pet_id = ar.pet_id 
                                         SET p.status = 'adopted' 
                                         WHERE ar.request_id = ?");
            $petUpdate->bind_param("i", $request_id);
            $petUpdate->execute();
        }

        header("Location: adoption_requests.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Adoption Requests</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="container mt-5">

    <h2>🐾 All Adoption Requests</h2>

    <table class="table table-bordered table-striped mt-4">
        <thead class="table-dark">
            <tr>
                <th>Request ID</th>
                <th>User</th>
                <th>Pet</th>
                <th>Reason</th>
                <th>Request Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = "SELECT ar.request_id, ar.requested_on, ar.status, 
                             ar.full_name, ar.email, ar.phone, ar.address, ar.experience, ar.reason,
                             u.name AS user_name, 
                             p.name AS pet_name, p.type, p.breed,p.price
                      FROM adoption_requests ar
                      JOIN users u ON ar.user_id = u.user_id
                      JOIN pets p ON ar.pet_id = p.pet_id
                      ORDER BY ar.requested_on DESC";

            $result = $conn->query($query);
            while ($row = $result->fetch_assoc()) {
                $modalId = "detailsModal" . $row['request_id'];

                echo "<tr>
                        <td>{$row['request_id']}</td>
                        <td>{$row['full_name']}</td>
                        <td>{$row['pet_name']}</td>
                        <td>" . substr($row['reason'], 0, 30) . "...</td>
                        <td>{$row['requested_on']}</td>
                        <td><span class='badge text-bg-" . statusColor($row['status']) . "'>" . ucfirst($row['status']) . "</span></td>
                        <td>
                            <button class='btn btn-info btn-sm' data-bs-toggle='modal' data-bs-target='#$modalId'>View</button>";

                if ($row['status'] === 'pending') {
                    echo " <a href='adoption_requests.php?action=approved&id={$row['request_id']}' class='btn btn-success btn-sm me-1'>Approve</a>
                           <a href='adoption_requests.php?action=rejected&id={$row['request_id']}' class='btn btn-danger btn-sm'>Reject</a>";
                } else {
                    echo " -";
                }

                echo "</td>
                </tr>";

                // Modal for full details
                echo "
                <div class='modal fade' id='$modalId' tabindex='-1' aria-hidden='true'>
                  <div class='modal-dialog modal-lg'>
                    <div class='modal-content'>
                      <div class='modal-header'>
                        <h5 class='modal-title'>Adoption Request #{$row['request_id']}</h5>
                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                      </div>
                      <div class='modal-body'>
                        <h6>👤 Applicant Info</h6>
                        <p><strong>Name:</strong> {$row['full_name']}<br>
                           <strong>Email:</strong> {$row['email']}<br>
                           <strong>Phone:</strong> {$row['phone']}<br>
                           <strong>Address:</strong> {$row['address']}<br>
                           <strong>Experience:</strong> {$row['experience']}</p>
                        
                        <h6>🐶 Pet Info</h6>
                        <p><strong>Name:</strong> {$row['pet_name']}<br>
                           <strong>Type:</strong> {$row['type']}<br>
                           <strong>Price:</strong> {$row['price']}<br>
                           <strong>Breed:</strong> {$row['breed']}</p>
                        <h6>📄 Reason for Adoption</h6>
                        <p>{$row['reason']}</p>
                      </div>
                      <div class='modal-footer'>
                        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>";
                if ($row['status'] === 'pending') {
                    echo "<a href='adoption_requests.php?action=approved&id={$row['request_id']}' class='btn btn-success'>Approve</a>
                          <a href='adoption_requests.php?action=rejected&id={$row['request_id']}' class='btn btn-danger'>Reject</a>";
                }
                echo "</div>
                    </div>
                  </div>
                </div>";
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

    <a href="admin_dashboard.php" class="btn btn-secondary mt-3">← Back to Admin Dashboard</a>
</body>
</html>
