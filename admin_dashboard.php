<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

include 'includes/config.php';

$admin_name = $_SESSION['admin_name'] ?? 'Admin';
$role = $_SESSION['admin_role'] ?? 'staff';

// ✅ Fetch counts dynamically
$totalPetsQuery = $conn->query("SELECT COUNT(*) AS total FROM pets WHERE status='available'");
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
    <title>Admin Dashboard - PetAdopt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
    body {
    background: #f8fafc;
    font-family: 'Poppins', sans-serif;
}

/* Sidebar */
.sidebar {
    height: 200vh;
    background: linear-gradient(180deg, #4f46e5, #7c3aed);
    color: white;
    padding-top: 25px;
    position: fixed;
    width: 280px;
    box-shadow: 3px 0 10px rgba(0,0,0,0.2);
}

.sidebar h3 {
    text-align: center;
    font-weight: 700;
    margin-bottom: 30px;
    letter-spacing: 1px;
    color: #fff;
}

.sidebar a {
    color: #e0e7ff;
    display: block;
    padding: 12px 20px;
    margin: 5px 15px;
    text-decoration: none;
    border-radius: 10px;
    transition: 0.3s;
    font-weight: 500;
}

.sidebar a:hover {
    background: rgba(255,255,255,0.25);
    color: #fff;
    transform: translateX(5px);
}

/* Content */
.content {
    margin-left: 260px;
    padding: 35px;
}

.welcome h1 {
    font-weight: 700;
    color: #312e81;
    font-size: 1.9rem;
}

.welcome h5 {
    color: #6b7280;
    font-weight: 500;
}

/* Stats Cards */
.stats-card {
    border: none;
    border-radius: 18px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    transition: all 0.3s ease-in-out;
    padding: 25px;
    color: white;
    text-shadow: 0 1px 2px rgba(0,0,0,0.15);
}

.stats-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}

.bg-pets {
    background: linear-gradient(135deg, #6366f1, #4f46e5);
}
.bg-requests {
    background: linear-gradient(135deg, #f59e0b, #d97706);
}
.bg-adopted {
    background: linear-gradient(135deg, #22c55e, #15803d);
}

.stats-card h5 {
    font-weight: 600;
    font-size: 1.1rem;
    margin-bottom: 5px;
}

.stats-card p {
    font-size: 2.3rem;
    font-weight: 700;
    margin-top: 8px;
}

/* Buttons */
.btn-custom {
    border: none;
    border-radius: 10px;
    font-weight: 500;
    padding: 10px 20px;
    transition: 0.3s;
}

.btn-primary {
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
}
.btn-primary:hover {
    background: linear-gradient(135deg, #4338ca, #6d28d9);
}

.btn-warning {
    background: linear-gradient(135deg, #fbbf24, #f59e0b);
    color: #fff;
}
.btn-warning:hover {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: #fff;
}

/* Headings */
.quick-actions h4 {
    font-weight: 600;
    color: #312e81;
    margin-bottom: 15px;
}

/* Footer */
footer {
    margin-top: 50px;
    text-align: center;
    color: #9ca3af;
    font-size: 0.9rem;
}
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h3>🐾 PetAdopt</h3>
        <a href="admin_dashboard.php"><i class="fa fa-home me-2"></i> Dashboard</a>
        <a href="add_pet.php"><i class="fa fa-plus me-2"></i> Add Pet</a>
        <a href="manage_pets.php"><i class="fa fa-dog me-2"></i> Manage Pets</a>
        <a href="adoption_requests.php"><i class="fa fa-file-alt me-2"></i> Manage Requests</a>

        <?php if ($role === 'super_admin') : ?>
            <a href="manage_AdminUsers.php"><i class="fa fa-users-cog me-2"></i> Manage Admins & Users</a>
        <?php endif; ?>

        <a href="donations.php"><i class="fa fa-hand-holding-heart me-2"></i> Donations Overview</a>
        <a href="admin_profile.php"><i class="fa fa-user me-2"></i> Profile</a>
        <a href="logout.php"><i class="fa fa-sign-out-alt me-2"></i> Logout</a>
    </div>

    <!-- Main Content -->
    <div class="content">
        <div class="welcome mb-4">
            <h1>Welcome, <?php echo htmlspecialchars($admin_name); ?> 👋</h1>
            <h5 class="text-muted"><?php echo date('l, F j, Y'); ?></h5>
        </div>

        <!-- Dashboard Stats -->
<div class="row g-4">
    <div class="col-md-4">
        <div class="stats-card bg-pets">
            <h5><i class="fa fa-dog me-2"></i>Total Pets</h5>
            <p><?php echo $total_pets; ?></p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card bg-requests">
            <h5><i class="fa fa-clock me-2"></i>Pending Requests</h5>
            <p><?php echo $pending_requests; ?></p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card bg-adopted">
            <h5><i class="fa fa-heart me-2"></i>Adopted Pets</h5>
            <p><?php echo $adopted_pets; ?></p>
        </div>
    </div>
</div>

        <!-- Quick Actions -->
        <div class="quick-actions mt-5">
    <h4>Quick Actions</h4>
    <a href="add_pet.php" class="btn btn-primary btn-custom me-2">
        <i class="fa fa-plus me-1"></i> Add Pet
    </a>
    <a href="adoption_requests.php" class="btn btn-warning btn-custom me-2">
        <i class="fa fa-eye me-1"></i> View Requests
    </a>
</div>


        <footer class="mt-5">
            <p>© <?php echo date("Y"); ?> PetAdopt | Developed with ❤️ for helping pets find homes</p>
        </footer>
    </div>

</body>
</html>
