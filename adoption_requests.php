<?php
session_start();
include 'includes/config.php';

// Check login (Admin or Volunteer)
if (!isset($_SESSION['admin_id']) && !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle status updates (Admin only)
if (isset($_SESSION['admin_id']) && isset($_GET['action'], $_GET['id'])) {
    $action = $_GET['action'];
    $request_id = intval($_GET['id']);

    if (in_array($action, ['approved', 'rejected'])) {
        $update = $conn->prepare("UPDATE adoption_requests SET status = ? WHERE request_id = ?");
        $update->bind_param("si", $action, $request_id);
        $update->execute();

        // If approved, mark pet as adopted
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

// Fetch requests
if (isset($_SESSION['admin_id'])) {
    $query = "SELECT ar.request_id, ar.requested_on, ar.status, 
                     ar.full_name, ar.email, ar.phone, ar.address, ar.experience, ar.reason,
                     u.name AS user_name, 
                     p.name AS pet_name, p.type, p.breed, p.price, p.image
              FROM adoption_requests ar
              JOIN users u ON ar.user_id = u.user_id
              JOIN pets p ON ar.pet_id = p.pet_id
              ORDER BY ar.requested_on DESC";
    $stmt = $conn->prepare($query);
} else {
    $user_id = $_SESSION['user_id'];
    $query = "SELECT ar.request_id, ar.requested_on, ar.status, 
                     ar.full_name, ar.email, ar.phone, ar.address, ar.experience, ar.reason,
                     u.name AS user_name, 
                     p.name AS pet_name, p.type, p.breed, p.price, p.image
              FROM adoption_requests ar
              JOIN users u ON ar.user_id = u.user_id
              JOIN pets p ON ar.pet_id = p.pet_id
              WHERE p.added_by_user_id = ?
              ORDER BY ar.requested_on DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$result = $stmt->get_result();

function statusColor($status) {
    return match ($status) {
        'approved' => '#4ade80', // green
        'rejected' => '#ef4444', // red
        default => '#facc15',    // yellow
    };
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Adoption Requests</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        body {
            background-color: #f5f7fa;
            font-family: "Poppins", sans-serif;
        }
        h2 {
            text-align: center;
            font-weight: 600;
            color: #374151;
            margin-bottom: 25px;
        }
        .table-container {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.08);
            padding: 25px;
        }
        table thead {
            background: linear-gradient(90deg, #2563eb, #3b82f6);
            color: white;
        }
        table tbody tr {
            vertical-align: middle;
        }
        table img {
            width: 55px;
            height: 55px;
            border-radius: 12px;
            object-fit: cover;
            border: 2px solid #e5e7eb;
        }
        .btn-custom {
            border: none;
            border-radius: 6px;
            padding: 8px 18px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.25s ease;
            display: inline-block;
            text-decoration: none;
            text-align: center;
            cursor: pointer;
            letter-spacing: 0.3px;
        }
        .btn-view {
            background-color: #2563eb;
            color: #ffffff;
        }
        .btn-view:hover {
            background-color: #1e40af;
            box-shadow: 0 2px 6px rgba(37,99,235,0.3);
        }
        .btn-approve {
            background-color: #16a34a;
            color: #ffffff;
        }
        .btn-approve:hover {
            background-color: #15803d;
            box-shadow: 0 2px 6px rgba(22,197,94,0.3);
        }
        .btn-reject {
            background-color: #dc2626;
            color: #ffffff;
        }
        .btn-reject:hover {
            background-color: #b91c1c;
            box-shadow: 0 2px 6px rgba(239,68,68,0.3);
        }
        td .btn-custom {
            margin: 4px 4px;
        }

        .badge-status {
            padding: 6px 10px;
            border-radius: 12px;
            color: #fff;
            font-weight: 500;
            text-transform: capitalize;
        }
        .modal-content {
            border-radius: 18px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        }
        .pet-img-lg {
            border-radius: 15px;
            width: 100%;
            object-fit: cover;
        }
    </style>
</head>
<body class="container py-5">

    <h2>🐾 Adoption Requests</h2>

    <?php if (!isset($_SESSION['admin_id'])): ?>
    <div class="alert alert-info shadow-sm">
        <strong>Note:</strong> Only administrators can approve or reject requests. You can view requests for your added pets.
    </div>
    <?php endif; ?>

    <div class="table-container mt-4">
        <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Pet</th>
                    <th>Applicant</th>
                    <th>Reason</th>
                    <th>Requested On</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()):
                $modalId = "detailsModal" . $row['request_id'];
                $img = !empty($row['image']) ? 'assets/images/pet-uploads/' . htmlspecialchars($row['image']) : 'assets/images/default_pet.png';
            ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="<?= $img ?>" alt="Pet">
                            <div>
                                <strong><?= htmlspecialchars($row['pet_name']) ?></strong><br>
                                <small class="text-muted"><?= htmlspecialchars($row['breed']) ?></small>
                            </div>
                        </div>
                    </td>
                    <td><?= htmlspecialchars($row['full_name']) ?></td>
                    <td><?= substr($row['reason'], 0, 30) ?>...</td>
                    <td><?= date("M d, Y h:i A", strtotime($row['requested_on'])) ?></td>
                    <td>
                        <span class="badge-status" style="background-color: <?= statusColor($row['status']) ?>;">
                            <?= ucfirst($row['status']) ?>
                        </span>
                    </td>
                    <td>
                        <button class="btn-custom btn-view" data-bs-toggle="modal" data-bs-target="#<?= $modalId ?>">
                            View
                        </button>
                        <?php if (isset($_SESSION['admin_id']) && $row['status'] === 'pending'): ?>
                            <a href="adoption_requests.php?action=approved&id=<?= $row['request_id'] ?>" class="btn-custom btn-approve ms-1">Approve</a>
                            <a href="adoption_requests.php?action=rejected&id=<?= $row['request_id'] ?>" class="btn-custom btn-reject ms-1">Reject</a>
                        <?php endif; ?>
                    </td>
                </tr>

                <!-- Modal -->
                <div class="modal fade" id="<?= $modalId ?>" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title">Adoption Request #<?= $row['request_id'] ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-5">
                                <img src="<?= $img ?>" alt="Pet Image" class="pet-img-lg">
                                <div class="mt-3">
                                    <h6>🐶 Pet Info</h6>
                                    <p><strong>Name:</strong> <?= htmlspecialchars($row['pet_name']) ?><br>
                                       <strong>Type:</strong> <?= htmlspecialchars($row['type']) ?><br>
                                       <strong>Breed:</strong> <?= htmlspecialchars($row['breed']) ?><br>
                                       <strong>Price:</strong> ₹<?= htmlspecialchars($row['price']) ?></p>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <h6>👤 Applicant Info</h6>
                                <p><strong>Name:</strong> <?= htmlspecialchars($row['full_name']) ?><br>
                                   <strong>Email:</strong> <?= htmlspecialchars($row['email']) ?><br>
                                   <strong>Phone:</strong> <?= htmlspecialchars($row['phone']) ?><br>
                                   <strong>Address:</strong> <?= htmlspecialchars($row['address']) ?><br>
                                   <strong>Experience:</strong> <?= htmlspecialchars($row['experience']) ?><br>
                                   <strong>Requested On:</strong> <?= date("M d, Y h:i A", strtotime($row['requested_on'])) ?></p>
                                <h6>📄 Reason for Adoption</h6>
                                <p><?= nl2br(htmlspecialchars($row['reason'])) ?></p>
                            </div>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn-custom btn-view" data-bs-dismiss="modal">Close</button>
                      </div>
                    </div>
                  </div>
                </div>
            <?php endwhile; ?>
            </tbody>
        </table>
        </div>
    </div>

    <?php $dashboard_link = isset($_SESSION['admin_id']) ? 'admin_dashboard.php' : 'dashboard.php'; ?>
    <div class="text-center mt-4">
        <a href="<?= $dashboard_link ?>" class="btn-custom btn-view">← Back to Dashboard</a>
    </div>

</body>
</html>
