<?php 
session_start();
include 'includes/config.php';

$message = "";

// Show popup if not logged in
if (!isset($_SESSION['user_id'])) {
    $showLoginPopup = true;
} else {
    $showLoginPopup = false;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user_id = $_SESSION['user_id'];
        $amount = $_POST['amount'];
        $donation_message = $_POST['message'];
        $donation_type = $_POST['donation_type'];

        $stmt = $conn->prepare("INSERT INTO donations (user_id, amount, message, donation_type) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("idss", $user_id, $amount, $donation_message, $donation_type);

        if ($stmt->execute()) {
            $message = "🙏 Thank you for donating ₹$amount towards <b>$donation_type</b>!";
        } else {
            $message = "❌ Failed to process donation.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Donate for Animals</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="container mt-5">

<h2>💝 Make a Donation</h2>

<?php if ($message): ?>
    <div class="alert alert-info"><?= $message ?></div>
<?php endif; ?>

<?php if (!$showLoginPopup): ?>
<form method="POST" class="w-75 mt-4">
    <div class="mb-3">
        <label>Amount (INR):</label>
        <input type="number" name="amount" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Donation Type:</label>
        <select name="donation_type" class="form-control" required>
            <option value="">-- Select a Cause --</option>
            <option value="Dog Food">🐶 Dog Food</option>
            <option value="Cat Shelter">🐱 Cat Shelter</option>
            <option value="Medical Care">🐾 Medical Care</option>
        </select>
    </div>

    <div class="mb-3">
        <label>Message (optional):</label>
        <textarea name="message" class="form-control" rows="3"></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Donate Now</button>
    <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
</form>
<?php endif; ?>

<?php if ($showLoginPopup): ?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    Swal.fire({
        title: "🔐 Login Required",
        text: "You must login or register to donate.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Login",
        cancelButtonText: "Register"
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "login.php";
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            window.location.href = "register.php";
        } else {
            window.location.href = "index.php"; // fallback
        }
    });
});
</script>
<?php endif; ?>

</body>
</html>
