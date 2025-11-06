<?php
session_start();

$message = "";

if (!isset($_SESSION['reset_user_id']) || !isset($_SESSION['reset_otp'])) {
    header("Location: forgot_password.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp_input = trim($_POST['otp']);

    if (time() > ($_SESSION['reset_expires'] ?? 0)) {
        $message = "❌ OTP has expired. Please try again.";
        unset($_SESSION['reset_otp'], $_SESSION['reset_expires'], $_SESSION['reset_user_id']);
    } elseif ($otp_input == $_SESSION['reset_otp']) {
        $_SESSION['otp_verified'] = true;
        header("Location: reset_password.php");
        exit();
    } else {
        $message = "❌ Invalid OTP. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Verify OTP | PetNest</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include 'includes/navbar.php'; ?>

<div class="container py-5" style="max-width:560px;">
  <div class="card shadow-sm border-0">
    <div class="card-body p-4">
      <h3>🔑 Verify OTP</h3>
      <?php if ($message): ?><div class="alert alert-danger"><?= $message ?></div><?php endif; ?>
      <form method="POST" class="mt-3" novalidate>
        <div class="mb-3">
          <input type="text" name="otp" class="form-control" placeholder="Enter OTP" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Verify OTP</button>
        <a href="forgot_password.php" class="btn btn-secondary w-100 mt-2">← Back</a>
      </form>
    </div>
  </div>
</div>
</body>
</html>
