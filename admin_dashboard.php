<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

include 'includes/config.php';

// ✅ Fetch counts dynamically
$totalPetsQuery = $conn->query("SELECT COUNT(*) AS total FROM pets");
$total_pets = $totalPetsQuery->fetch_assoc()['total'];

$pendingQuery = $conn->query("SELECT COUNT(*) AS pending FROM adoption_requests WHERE status='pending'");
$pending_requests = $pendingQuery->fetch_assoc()['pending'];

$adoptedQuery = $conn->query("SELECT COUNT(*) AS adopted FROM pets WHERE status='adopted'");
$adopted_pets = $adoptedQuery->fetch_assoc()['adopted'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Poppins', sans-serif;
        }
        .sidebar {
            height: 100vh;
            background: linear-gradient(180deg, #1f2937, #111827);
            color: white;
            padding-top: 20px;
            position: fixed;
            width: 230px;
        }
        .sidebar h3 {
            font-weight: 600;
            margin-bottom: 20px;
        }
        .sidebar a {
            color: #d1d5db;
            display: block;
            padding: 12px 20px;
            text-decoration: none;
            margin-bottom: 5px;
            border-radius: 8px;
            transition: 0.3s;
            font-weight: 500;
        }
        .sidebar a:hover {
            background-color: #374151;
            color: #fff;
            transform: translateX(5px);
        }
        .content {
            margin-left: 250px;
            padding: 20px;
        }
        .card {
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .card h5 {
            font-weight: 600;
        }
        .card-text {
            font-size: 2.5rem;
            font-weight: bold;
        }
        .quick-actions h4 {
            font-weight: 600;
            margin-bottom: 15px;
        }
        .btn {
            border-radius: 10px;
            font-weight: 500;
            padding: 10px 18px;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
         <h3 class="text-center">🐾 PetAdmin</h3>
            <a href="admin_dashboard.php"><i class="fa fa-home me-2"></i> Dashboard</a>
            <a href="add_pet.php"><i class="fa fa-plus me-2"></i> Add Pet</a>
            <a href="manage_pets.php"><i class="fa fa-dog me-2"></i> Manage Pets</a>
            <a href="adoption_requests.php"><i class="fa fa-file-alt me-2"></i> Manage Requests</a>
            <a href="admin_profile.php"><i class="fa fa-user me-2"></i> Profile</a>
            <a href="manage_admins.php"><i class="fa fa-users-cog me-2"></i> Manage Admins</a> 
            <a href="admin_donations.php"><i class="fa fa-donate me-2"></i> Donations</a>
            <a href="logout.php"><i class="fa fa-sign-out-alt me-2"></i> Logout</a>
    </div>

    <!-- Main Content -->
    <div class="content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Welcome, <?php echo $_SESSION['admin_name']; ?> 👋</1>
            <h5><strong class="text-muted"><?php echo date('l, F j, Y'); ?></strong></h5>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card text-white bg-primary p-3">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fa fa-dog me-2"></i>Total Pets</h5>
                        <p class="card-text"><?php echo $total_pets; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-dark bg-warning p-3">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fa fa-clock me-2"></i>Pending Requests</h5>
                        <p class="card-text"><?php echo $pending_requests; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success p-3">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fa fa-heart me-2"></i>Adopted Pets</h5>
                        <p class="card-text"><?php echo $adopted_pets; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions mt-5">
            <h4>Quick Actions</h4>
            <a href="add_pet.php" class="btn btn-primary me-2"><i class="fa fa-plus me-1"></i> Add New Pet</a>
            <a href="adoption_requests.php" class="btn btn-warning me-2"><i class="fa fa-eye me-1"></i> View Requests</a>
        </div>
    </div>

</body>
</html>
