<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'includes/config.php';

$user_id = $_SESSION['user_id'];
$name    = $_SESSION['name'];
$role    = $_SESSION['role']; // 'adopter', 'guest', 'volunteer'

// ===== FETCH DATA =====

// Available pets (all roles can see)
$petsQuery = $conn->query("SELECT COUNT(*) AS total FROM pets WHERE status='available'");
$available_pets = $petsQuery->fetch_assoc()['total'];

// Adoption requests (only for adopter)
$my_requests = 0;
if ($role === 'adopter') {
    $reqQuery = $conn->query("SELECT COUNT(*) AS total FROM adoption_requests WHERE user_id=$user_id");
    $my_requests = $reqQuery->fetch_assoc()['total'];
}

// Total donations (only for adopter)
$total_donations = 0;
$latest = null;
if ($role === 'adopter') {
    $stmt = $conn->prepare("SELECT SUM(amount) as total FROM donations WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $total_donations = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

    $stmt2 = $conn->prepare("SELECT amount, donation_type, created_at 
                             FROM donations 
                             WHERE user_id = ? 
                             ORDER BY created_at DESC LIMIT 1");
    $stmt2->bind_param("i", $user_id);
    $stmt2->execute();
    $latest = $stmt2->get_result()->fetch_assoc();
}

// Volunteer stats
$vol_pets_total = $vol_requests_total = 0;
if ($role === 'volunteer') {
    $vol_pets_total = $conn->query("SELECT COUNT(*) AS total FROM pets")->fetch_assoc()['total'];
    $vol_requests_total = $conn->query("SELECT COUNT(*) AS total FROM adoption_requests")->fetch_assoc()['total'];
}

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
    background-color: #f5f7f9;
    font-family: 'Nunito', sans-serif;
}

/* Sidebar */
.sidebar {
    height: 100vh;
    background: linear-gradient(180deg, #0ea5e9, #0284c7);
    color: white;
    padding-top: 22px;
    position: fixed;
    width: 230px;
    box-shadow: 3px 0 12px rgba(0, 0, 0, 0.15);
}

.sidebar h3 {
    font-weight: 700;
    text-align: center;
    margin-bottom: 25px;
    color: #fff;
    letter-spacing: 0.5px;
}

.sidebar a {
    color: #e0f2fe;
    display: block;
    padding: 10px 18px;
    text-decoration: none;
    margin: 5px 15px;
    border-radius: 10px;
    transition: all 0.3s ease;
    font-weight: 600;
    font-size: 0.96rem;
}

.sidebar a:hover {
    background-color: rgba(255, 255, 255, 0.25);
    color: #fff;
    transform: translateX(6px);
}

/* Content Area */
.content {
    margin-left: 250px;
    padding: 35px;
}

.content h2 {
    font-weight: 700;
    color: #075985;
    font-size: 1.9rem;
}

.text-muted {
    color: #6b7280 !important;
}

/* Stats Cards */
.card {
    border: none;
    border-radius: 18px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-6px);
    box-shadow: 0 8px 18px rgba(0, 0, 0, 0.12);
}

.bg-primary {
    background: linear-gradient(135deg, #0284c7, #0ea5e9) !important;
}

.bg-warning {
    background: linear-gradient(135deg, #facc15, #eab308) !important;
}

.card h5 {
    font-weight: 600;
    font-size: 1.1rem;
    margin-bottom: 8px;
}

.card-text {
    font-size: 1.1rem;
    font-weight: 700;
}

/* Buttons */
.btn {
    border-radius: 12px;
    font-weight: 600;
    padding: 10px 20px;
    transition: all 0.3s ease;
    font-size: 0.95rem;
}

.btn-success {
    background: linear-gradient(135deg, #10b981, #059669);
    border: none;
}
.btn-success:hover {
    background: linear-gradient(135deg, #059669, #047857);
    transform: translateY(-2px);
}

.btn-primary {
    background: linear-gradient(135deg, #0284c7, #0ea5e9);
    border: none;
}
.btn-primary:hover {
    background: linear-gradient(135deg, #0369a1, #0284c7);
    transform: translateY(-2px);
}

.btn-warning {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    border: none;
    color: white;
}
.btn-warning:hover {
    background: linear-gradient(135deg, #d97706, #b45309);
    transform: translateY(-2px);
}

.btn-outline-primary {
    color: #0284c7;
    border: 2px solid #0284c7;
    font-weight: 600;
}
.btn-outline-primary:hover {
    background: #0284c7;
    color: white;
}

/* Pet Cards */
.pet-card {
    border-radius: 18px;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.pet-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
}
.pet-card img {
    border-top-left-radius: 18px;
    border-top-right-radius: 18px;
}

/* Headings */
.quick-actions h4,
h4 {
    font-weight: 700;
    color: #075985;
}

/* SweetAlert Button */
.swal2-confirm {
    background-color: #0ea5e9 !important;
    border-radius: 8px !important;
}

/* Footer */
footer {
    text-align: center;
    color: #9ca3af;
    font-size: 0.9rem;
    margin-top: 50px;
}

    </style>
</head>
<!-- SweetAlert CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Show upgrade success message if user just upgraded
    if (sessionStorage.getItem('upgradeSuccess')) {
        Swal.fire({
            title: '🎉 Upgrade Successful!',
            html: '<b>Welcome to the Adopter Community!</b><br><br>' +
                  'You can now <b>request adoptions</b>, <b>view your requests</b>, ' +
                  'and <b>support our pets</b> through donations. 🐶🐾',
            icon: 'success',
            confirmButtonText: 'Let’s Explore!',
            confirmButtonColor: '#2563eb'
        });
        sessionStorage.removeItem('upgradeSuccess');
    }
});
</script>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h3 class="text-center">🐾 PetAdopt</h3>
        <a href="dashboard.php"><i class="fa fa-home me-2"></i> Dashboard</a>

        <?php if($role === 'volunteer'): ?>
            <a href="manage_pets.php"><i class="fa fa-dog me-2"></i> Manage Pets</a>
            <a href="adoption_requests.php"><i class="fa fa-file-alt me-2"></i>Adoption Request</a>
            <a href="my_requests.php"><i class="fa fa-file-alt me-2"></i>My Request</a>
            <a href="donate.php"><i class="fa fa-donate me-2"></i> Donate</a>
             <a href="donation_history.php"><i class="fa fa-history me-2"></i> My Donations</a>
            <a href="volunteer_donations.php"><i class="fa fa-hand-holding-heart me-2"></i> Donations Overview</a>
        <?php elseif($role === 'guest'): ?>
            <a href="view_pets.php"><i class="fa fa-dog me-2"></i> Browse Pets</a>
            <a href="upgrade.php"><i class="fa fa-dog me-2"></i> Become adopter</a>
        <?php else: ?>
            <!-- Adopter -->
            <a href="view_pets.php"><i class="fa fa-dog me-2"></i> View Pets</a>
            <a href="my_requests.php"><i class="fa fa-file-alt me-2"></i> My Requests</a>
            <a href="donate.php"><i class="fa fa-hand-holding-heart me-2"></i> Donate Funds</a>
            <a href="donation_history.php"><i class="fa fa-history me-2"></i> My Donations</a>
        <?php endif; ?>
        <a href="contact.php"><i class="fa fa-envelope me-2"></i> Contact/Feedback</a>
        <a href="change_password.php"><i class="fa fa-lock me-2"></i> Change Password</a>
        <a href="logout.php"><i class="fa fa-sign-out-alt me-2"></i> Logout</a>
    </div>

    <!-- Main Content -->
    <div class="content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-semibold">Welcome, <?php echo htmlspecialchars($name); ?> 👋</h2>
            <h6 class="text-muted"><?php echo date('l, F j, Y'); ?></h6>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4">
            <?php if($role === 'volunteer'): ?>
                <div class="col-md-4">
                    <div class="card text-white bg-primary p-3">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fa fa-dog me-2"></i>Total Pets</h5>
                            <p class="card-text"><?php echo $vol_pets_total; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-dark bg-warning p-3">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fa fa-file-alt me-2"></i>Pending Requests</h5>
                            <p class="card-text"><?php echo $vol_requests_total; ?></p>
                        </div>
                    </div>
                </div>
            <?php elseif($role === 'guest'): ?>
                <div class="col-md-4">
                    <div class="card text-white bg-primary p-3">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fa fa-dog me-2"></i>Available Pets</h5>
                            <p class="card-text"><?php echo $available_pets; ?></p>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Adopter -->
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
            <?php endif; ?>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions mt-5">
            <h4>Quick Actions</h4>
            <?php if($role === 'volunteer'): ?>
                <a href="manage_pets.php" class="btn btn-success me-2"><i class="fa fa-dog me-1"></i> Manage Pets</a>
                <a href="adoption_requests.php" class="btn btn-primary me-2"><i class="fa fa-file-alt me-1"></i> View Requests</a>
            <?php elseif($role === 'guest'): ?>
                <a href="view_pets.php" class="btn btn-success me-2"><i class="fa fa-eye me-1"></i> Browse Pets</a>
            <?php else: ?>
                <a href="view_pets.php" class="btn btn-success me-2"><i class="fa fa-eye me-1"></i> Browse Pets</a>
                <a href="my_requests.php" class="btn btn-primary me-2"><i class="fa fa-file-alt me-1"></i> My Requests</a>
                <a href="donate.php" class="btn btn-warning"><i class="fa fa-donate me-1"></i> Donate</a>
            <?php endif; ?>
        </div>

        <!-- Latest Pets -->
        <div class="mt-5">
            <h4>Latest Available Pets</h4>
            <div class="row">
                <?php
                $result = $conn->query("SELECT * FROM pets WHERE status='available' ORDER BY added_on DESC LIMIT 5");
                if ($result->num_rows > 0):
                    while ($pet = $result->fetch_assoc()):
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
                <?php endwhile; else: ?>
                    <p class="alert alert-info">No pets available right now.</p>
                <?php endif; ?>
            </div>
            <div class="text-center mt-3">
                <a href="view_pets.php" class="btn btn-outline-primary">See More Pets</a>
            </div>
        </div>

    </div>
</body>
</html>
