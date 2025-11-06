<?php
session_start();
include 'includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT amount, donation_type, message, created_at 
                        FROM donations 
                        WHERE user_id = ? 
                        ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Donation History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

<h2>📜 My Donation History</h2>

<a href="donate.php" class="btn btn-primary mb-3">💝 Make a New Donation</a>
<a href="dashboard.php" class="btn btn-secondary mb-3">← Back to Dashboard</a>

<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>Date</th>
            <th>Amount (₹)</th>
            <th>Donation Type</th>
            <th>Message</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= date("d M Y, h:i A", strtotime($row['created_at'])) ?></td>
                    <td><?= $row['amount'] ?></td>
                    <td><?= $row['donation_type'] ?></td>
                    <td><?= $row['message'] ?: "—" ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" class="text-center">No donations yet.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
