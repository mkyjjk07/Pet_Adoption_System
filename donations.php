<?php
session_start();
include 'includes/config.php';

// Only admins can access
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];

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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation Overview | PetNest</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f9fafc;
            font-family: "Poppins", sans-serif;
            color: #333;
        }

        .page-title {
            text-align: center;
            margin-top: 20px;
            font-weight: 700;
            color: #333;
        }

        .summary-card {
            background: #fff;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
            transition: 0.3s ease;
        }

        .summary-card:hover {
            transform: translateY(-3px);
        }

        .summary-card h5 {
            color: #666;
        }

        .summary-value {
            font-size: 2rem;
            font-weight: 700;
            color: #2c7a7b;
        }

        table {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        thead {
            background: #2c7a7b;
            color: #fff;
        }

        tbody tr:hover {
            background: #f1f9f9;
            transition: 0.2s ease;
        }

        .btn-modern {
            background-color: #2c7a7b;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 18px;
            font-weight: 500;
            transition: 0.3s ease;
        }

        .btn-modern:hover {
            background-color: #256c6e;
            transform: translateY(-2px);
        }

        .no-data {
            text-align: center;
            font-style: italic;
            color: #777;
        }

        footer {
            text-align: center;
            margin-top: 40px;
            color: #888;
            font-size: 0.9rem;
        }
    </style>
</head>

<body class="container py-5">

    <h2 class="page-title">💰 Donation Overview</h2>

    <div class="row text-center mt-4 g-4">
        <div class="col-md-6">
            <div class="summary-card">
                <h5>Total Donations</h5>
                <div class="summary-value"><?= $summary['total_donations'] ?? 0 ?></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="summary-card">
                <h5>Total Amount</h5>
                <div class="summary-value">₹<?= $summary['total_amount'] ?? 0 ?></div>
            </div>
        </div>
    </div>

    <div class="mt-5">
        <h4 class="fw-semibold mb-3">🕒 Recent Donations</h4>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
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
                                <td><strong>₹<?= number_format($row['amount'], 2) ?></strong></td>
                                <td><?= htmlspecialchars(ucfirst($row['donation_type'])) ?></td>
                                <td><?= htmlspecialchars($row['message'] ?: "—") ?></td>
                                <td><?= date("d M Y, h:i A", strtotime($row['created_at'])) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="no-data">No donations yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="text-center mt-4">
        <a href="admin_dashboard.php" class="btn-modern">← Back to Dashboard</a>
    </div>

    <footer class="mt-5">
        © <?= date("Y") ?> PetNest | All Rights Reserved 🐾
    </footer>

</body>
</html>
