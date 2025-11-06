<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
include 'includes/config.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Donations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-0">
<?php include 'includes/navbar.php'; ?>
<br>
<br>
<h2>💰 All Donations</h2>

<table class="table table-bordered table-striped  m-4">
    <thead class="table-dark">
        <tr>
            <th>Donation ID</th>
            <th>User Name</th>
            <th>Amount (₹)</th>
            <th>Message</th>
            <th>Donated On</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $query = "
            SELECT d.donation_id, d.amount, d.message, d.donated_on, 
                   u.name AS user_name 
            FROM donations d
            LEFT JOIN users u ON d.user_id = u.user_id
            ORDER BY d.donated_on DESC";

        $result = $conn->query($query);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['donation_id']}</td>
                        <td>" . ($row['user_name'] ?? '<i>Guest / Deleted</i>') . "</td>
                        <td>₹{$row['amount']}</td>
                        <td>{$row['message']}</td>
                        <td>{$row['donated_on']}</td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No donations found.</td></tr>";
        }
        ?>
    </tbody>
</table>

<a href="admin_dashboard.php" class="btn btn-secondary mt-3">← Back to Dashboard</a>

</body>
</html>
