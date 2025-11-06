<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'includes/config.php';
$user_id = $_SESSION['user_id'];
$name = $_SESSION['name'];

// ✅ Fetch counts dynamically
$petsQuery = $conn->query("SELECT COUNT(*) AS total FROM pets WHERE status='available'");
$available_pets = $petsQuery->fetch_assoc()['total'];

$reqQuery = $conn->query("SELECT COUNT(*) AS total FROM adoption_requests WHERE user_id=$user_id");
$my_requests = $reqQuery->fetch_assoc()['total'];

// Total donations
$stmt = $conn->prepare("SELECT SUM(amount) as total FROM donations WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_donations = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

// Latest donation
$stmt2 = $conn->prepare("SELECT amount, donation_type, created_at 
                         FROM donations 
                         WHERE user_id = ? 
                         ORDER BY created_at DESC LIMIT 1");
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$latest = $stmt2->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard | PetAdopt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f8fafc;
            font-family: 'Poppins', sans-serif;
        }
        .sidebar {
            height: 100vh;
            background: linear-gradient(180deg, #2563eb, #1e3a8a);
            color: white;
            padding-top: 20px;
            position: fixed;
            width: 220px;
        }
        .sidebar h3 {
            font-weight: 600;
            margin-bottom: 20px;
        }
        .sidebar a {
            color: #dbeafe;
            display: block;
            padding: 10px 18px;
            text-decoration: none;
            margin-bottom: 6px;
            border-radius: 8px;
            transition: 0.3s;
            font-weight: 500;
            font-size: 0.95rem;
        }
        .sidebar a:hover {
            background-color: #1e40af;
            color: #fff;
            transform: translateX(4px);
        }
        .content {
            margin-left: 240px;
            padding: 25px;
        }
        .card {
            border-radius: 14px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.08);
        }
        .card h5 {
            font-weight: 600;
            font-size: 1.1rem;
        }
        .card-text {
            font-size: 1rem;
            font-weight: bold;
            margin: 0;
        }
        .quick-actions h4 {
            font-weight: 600;
            margin-bottom: 15px;
        }
        .btn {
            border-radius: 10px;
            font-weight: 500;
            padding: 8px 16px;
            font-size: 0.95rem;
        }
        .pet-card img {
            border-top-left-radius: 14px;
            border-top-right-radius: 14px;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h3 class="text-center">🐾 PetAdopt</h3>
        <a href="dashboard.php"><i class="fa fa-home me-2"></i> Dashboard</a>
        <a href="view_pets.php"><i class="fa fa-dog me-2"></i> View Pets</a>
        <a href="my_requests.php"><i class="fa fa-file-alt me-2"></i> My Requests</a>
        <a href="donate.php"><i class="fa fa-hand-holding-heart me-2"></i> Donate Funds</a>
        <a href="donation_history.php"><i class="fa fa-history me-2"></i> My Donations</a>
        <a href="change_password.php"><i class="fa fa-lock me-2"></i> Change Password</a>
        <a href="logout.php"><i class="fa fa-sign-out-alt me-2"></i> Logout</a>
    </div>

    <!-- Main Content -->
    <div class="content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-semibold">Welcome, <?php echo $name; ?> 👋</h2>
            <h6 class="text-muted"><?php echo date('l, F j, Y'); ?></h6>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card text-white bg-primary p-3">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fa fa-paw me-2"></i>My Requests</h5>
                        <p class="card-text"><?php echo $my_requests; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-dark bg-warning p-3">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fa fa-hand-holding-heart me-2"></i>My Donations</h5>
                        <p class="mb-1"><strong>Total:</strong> ₹<?php echo $total_donations; ?></p>
                        <?php if ($latest): ?>
                            <p class="mb-1"><strong>Last:</strong> ₹<?php echo $latest['amount']; ?> (<?php echo $latest['donation_type']; ?>)</p>
                            <small class="text-muted">on <?php echo date("d M Y, h:i A", strtotime($latest['created_at'])); ?></small>
                        <?php else: ?>
                            <p>You haven’t made any donation yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions mt-5">
            <h4>Quick Actions</h4>
            <a href="view_pets.php" class="btn btn-success me-2"><i class="fa fa-eye me-1"></i> Browse Pets</a>
            <a href="my_requests.php" class="btn btn-primary me-2"><i class="fa fa-file-alt me-1"></i> My Requests</a>
            <a href="donate.php" class="btn btn-warning"><i class="fa fa-donate me-1"></i> Donate</a>
        </div>

        <!-- Latest Pets -->
        <div class="mt-5">
            <h4>Latest Available Pets</h4>
            <div class="row">
                <?php
                $result = $conn->query("SELECT * FROM pets WHERE status='available' ORDER BY added_on DESC LIMIT 5");
                if ($result->num_rows > 0) {
                    while ($pet = $result->fetch_assoc()) {
                ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm pet-card">
                        <img src="assets/images/pet-uploads/<?php echo $pet['image']; ?>" class="card-img-top" height="180" style="object-fit:cover;" alt="Pet Image">
                        <div class="card-body">
                            <h6 class="fw-bold"><?php echo $pet['name']; ?></h6>
                            <p class="card-text small mb-2">
                                <strong>Breed:</strong> <?php echo $pet['breed']; ?><br>
                                <strong>Age:</strong> <?php echo $pet['age']; ?> yrs<br>
                                <strong>Type:</strong> <?php echo $pet['type']; ?><br>
                                <strong>Gender:</strong> <?php echo $pet['gender']; ?>
                            </p>
                        </div>
                    </div>
                </div>
                <?php } } else { ?>
                    <p class="alert alert-info">No pets available right now.</p>
                <?php } ?>
            </div>
            <div class="text-center mt-3">
                <a href="view_pets.php" class="btn btn-outline-primary">See More Pets</a>
            </div>
        </div>
    </div>
</body>
</html>
