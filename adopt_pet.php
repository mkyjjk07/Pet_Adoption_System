<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'includes/config.php';

$user_id = $_SESSION['user_id'];
$pet_id = isset($_GET['pet_id']) ? intval($_GET['pet_id']) : 0;
$message = "";
$pet = null;

// Check if pet exists and is available
$check_pet = $conn->prepare("SELECT * FROM pets WHERE pet_id = ? AND status = 'available'");
$check_pet->bind_param("i", $pet_id);
$check_pet->execute();
$result = $check_pet->get_result();

if ($result->num_rows > 0) {
    $pet = $result->fetch_assoc();

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $full_name  = trim($_POST['full_name']);
        $email      = trim($_POST['email']);
        $phone      = trim($_POST['phone']);
        $address    = trim($_POST['address']);
        $experience = trim($_POST['experience']);
        $reason     = trim($_POST['reason']);

        // Prevent duplicate requests
        $check_request = $conn->prepare("SELECT * FROM adoption_requests WHERE user_id = ? AND pet_id = ?");
        $check_request->bind_param("ii", $user_id, $pet_id);
        $check_request->execute();
        $check_request_result = $check_request->get_result();

        if ($check_request_result->num_rows > 0) {
            $message = "⚠ You have already requested to adopt this pet.";
        } else {
            // Insert adoption request
            $stmt = $conn->prepare("INSERT INTO adoption_requests 
                (user_id, pet_id, full_name, email, phone, address, experience, reason, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
            $stmt->bind_param("iissssss", 
                $user_id, $pet_id, $full_name, $email, $phone, $address, $experience, $reason);

            if ($stmt->execute()) {
                $message = "✅ Your adoption request has been submitted successfully!";
            } else {
                $message = "❌ Error submitting request.";
            }
        }
    }
} else {
    $message = "Invalid pet ID or pet not available.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Adoption Request</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-9">
      <div class="card shadow-lg border-0 rounded-3">
        <div class="card-header bg-primary text-white text-center">
          <h3>Adoption Request Form</h3>
        </div>
        <div class="card-body p-4">

          <?php if ($message): ?>
            <div class="alert alert-info text-center"><?= $message; ?></div>
          <?php endif; ?>

          <?php if ($pet && !$message): ?>
            <!-- Pet Info Section -->
            <div class="card mb-4">
              <div class="row g-0">
                <div class="col-md-4">
                  <img src="assets/images/pet-uploads/<?= htmlspecialchars($pet['image']); ?>" 
                       class="img-fluid rounded-start" alt="Pet Image" style="object-fit:cover;height:100%;">
                </div>
                <div class="col-md-8">
                  <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($pet['name']); ?> </h5>
                    <hr>
                    <p class="card-text mb-1"><strong>Breed:</strong> <?= htmlspecialchars($pet['breed']); ?></p>
                    <p class="card-text mb-1"><strong>Type:</strong> <?= htmlspecialchars($pet['type']); ?></p>
                    <p class="card-text mb-1"><strong>Age:</strong> <?= htmlspecialchars($pet['age']); ?> years</p>
                    <p class="card-text mb-1"><strong>Gender:</strong> <?= htmlspecialchars($pet['gender']); ?></p>
                    <p class="card-text mb-1"><strong>Price:</strong> <?= htmlspecialchars($pet['price']); ?></p>
                    <p class="card-text mb-1"><strong>Description:</strong> <?= htmlspecialchars($pet['description']); ?></p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Adoption Form -->
            <form method="POST" action="">
              <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="full_name" class="form-control" required>
              </div>

              <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
              </div>

              <div class="mb-3">
                <label class="form-label">Phone Number</label>
                <input type="text" name="phone" class="form-control" required>
              </div>

              <div class="mb-3">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" rows="2" required></textarea>
              </div>

              <div class="mb-3">
                <label class="form-label">Do you have any past pet experience?</label>
                <textarea name="experience" class="form-control" rows="2"></textarea>
              </div>

              <div class="mb-3">
                <label class="form-label">Why do you want to adopt this pet?</label>
                <textarea name="reason" class="form-control" rows="3" required></textarea>
              </div>

              <button type="submit" class="btn btn-success w-100">Submit Request</button>
              <a href="view_pets.php" class="btn btn-secondary w-100 mt-2">Cancel</a>
            </form>
          <?php endif; ?>

        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
