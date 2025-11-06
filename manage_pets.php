<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
include 'includes/config.php';

// Handle deletion safely via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $id = intval($_POST['delete_id']);
    $stmt = $conn->prepare("DELETE FROM pets WHERE pet_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: manage_pets.php");
    exit();
}

// Fetch pets
$result = $conn->query("SELECT * FROM pets ORDER BY added_on DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Pets</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<style>
    body { background-color: #f0f4f8; margin: 10px; }
    .card { border-radius: 15px; box-shadow: 0 8px 20px rgba(0,0,0,0.1); margin-bottom: 20px; }
    .btn-edit { background-color: #f59e0b; border: none; color: #fff; }
    .btn-edit:hover { background-color: #d97706; }
    .btn-delete { background-color: #ef4444; border: none; color: #fff; }
    .btn-delete:hover { background-color: #b91c1c; }
    .badge-available { background-color: #6b7280; } /* gray */
    .badge-adopted { background-color: #10b981; } /* green */
    .badge-pending { background-color: #f59e0b; } /* orange */
    table img { border-radius: 10px; cursor: pointer; }
</style>
</head>
<body class="container mt-5">

<h2 class="mb-4 text-center"><i class="fa fa-dog me-2"></i>Manage Pets</h2>

<div class="d-flex justify-content-between mb-3">
    <a href="add_pet.php" class="btn btn-success"><i class="fa fa-plus me-1"></i> Add New Pet</a>
    <a href="admin_dashboard.php" class="btn btn-secondary"><i class="fa fa-arrow-left me-1"></i> Back to Dashboard</a>
</div>

<div class="table-responsive">
<table class="table table-hover align-middle">
    <thead class="table-dark">
        <tr>
            <th>Image</th>
            <th>Pet Name</th>
            <th>Type</th>
            <th>Breed</th>
            <th>Age</th>
            <th>Gender</th>
            <th>Status</th>
            <th>Added On</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): 
            // decide badge class in a PHP-safe way (avoid match for compatibility)
            $statusClass = 'badge-available';
            if (isset($row['status'])) {
                if ($row['status'] === 'adopted') $statusClass = 'badge-adopted';
                elseif ($row['status'] === 'pending') $statusClass = 'badge-pending';
            }
            $pet_id = (int)$row['pet_id'];
        ?>
            <tr>
                <td>
                    <img src="<?= 'assets/images/pet-uploads/' . htmlspecialchars($row['image'] ?? 'placeholder.png') ?>"
                         width="60" alt="Pet Image"
                         data-bs-toggle="modal" data-bs-target="#petModal<?= $pet_id ?>">
                </td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['type']) ?></td>
                <td><?= htmlspecialchars($row['breed']) ?></td>
                <td><?= htmlspecialchars($row['age']) ?></td>
                <td><?= htmlspecialchars($row['gender']) ?></td>
                <td><span class="badge <?= $statusClass ?> text-white"><?= htmlspecialchars($row['status']) ?></span></td>
                <td><?= date("M d, Y", strtotime($row['added_on'])) ?></td>
                <td>
                    <a href="edit_pet.php?id=<?= $pet_id ?>" class="btn btn-edit btn-sm me-1">
                        <i class="fa fa-edit"></i>
                    </a>

                    <!-- Delete form (submitted by JS after confirmation) -->
                    <form method="POST" class="d-inline delete-form">
                        <input type="hidden" name="delete_id" value="<?= $pet_id ?>">
                        <button type="button" class="btn btn-delete btn-sm btn-delete-confirm" title="Delete">
                            <i class="fa fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>

            <!-- Pet Detail Modal -->
            <div class="modal fade" id="petModal<?= $pet_id ?>" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title"><?= htmlspecialchars($row['name']) ?> - Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body row">
                    <div class="col-md-5">
                        <img src="<?= 'assets/images/pet-uploads/' . htmlspecialchars($row['image'] ?? 'placeholder.png') ?>" class="img-fluid rounded" alt="Pet Image">
                    </div>
                    <div class="col-md-7">
                        <p><strong>Breed:</strong> <?= htmlspecialchars($row['breed']) ?></p>
                        <p><strong>Age:</strong> <?= htmlspecialchars($row['age']) ?> years</p>
                        <p><strong>Type:</strong> <?= htmlspecialchars($row['type']) ?></p>
                        <p><strong>Gender:</strong> <?= htmlspecialchars($row['gender']) ?></p>
                        <p><strong>Status:</strong> <?= htmlspecialchars($row['status']) ?></p>
                        <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($row['description'])) ?></p>
                        <p><strong>Added On:</strong> <?= date("M d, Y", strtotime($row['added_on'])) ?></p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="9" class="text-center">No pets found.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-delete-confirm').forEach(function(button){
        button.addEventListener('click', function () {
            const form = this.closest('form');
            Swal.fire({
                title: 'Are you sure?',
                text: "This pet will be permanently deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>
</body>
</html>
