<?php
session_start();
include 'includes/config.php';

// Only volunteers can access
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch total donations and total amount
$summaryQuery = "SELECT COUNT(*) AS total_donations, SUM(amount) AS total_amount FROM donations";
$summaryResult = $conn->query($summaryQuery);
$summary = $summaryResult->fetch_assoc();

// Fetch recent donations
$query = "SELECT u.name AS donor, d.amount, d.donation_type, d.message, d.created_at 
          FROM donations d
          LEFT JOIN users u ON d.user_id = u.user_id
          ORDER BY d.created_at DESC LIMIT 10";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Donation Overview</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

<h2>💰 Donation Overview</h2>

<div class="row text-center mt-4">
    <div class="col-md-6">
        <div class="card p-3 shadow-sm">
            <h5>Total Donations</h5>
            <h3><?= $summary['total_donations'] ?? 0 ?></h3>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card p-3 shadow-sm">
            <h5>Total Amount</h5>
            <h3>₹<?= $summary['total_amount'] ?? 0 ?></h3>
        </div>
    </div>
</div>

<h4 class="mt-5">🕒 Recent Donations</h4>
<table class="table table-bordered table-striped mt-3">
    <thead class="table-dark">
        <tr>
            <th>Donor</th>
            <th>Amount (₹)</th>
            <th>Donation Type</th>
            <th>Message</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['donor'] ?? 'Guest') ?></td>
                    <td><?= $row['amount'] ?></td>
                    <td><?= $row['donation_type'] ?></td>
                    <td><?= $row['message'] ?: "—" ?></td>
                    <td><?= date("d M Y, h:i A", strtotime($row['created_at'])) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="5" class="text-center">No donations yet.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<a href="dashboard.php" class="btn btn-secondary mt-3">← Back to Dashboard</a>
<a href="donate.php" class="btn btn-primary mt-3">💝 Make a Donation</a>

</body>
</html>
