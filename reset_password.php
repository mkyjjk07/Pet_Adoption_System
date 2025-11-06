<?php
session_start();
include 'includes/config.php';

$message = "";

if (!isset($_SESSION['otp_verified']) || !isset($_SESSION['reset_user_id'])) {
    header("Location: forgot_password.php");
    exit();
}

$user_id = (int) $_SESSION['reset_user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new = trim($_POST['new_password']);
    $confirm = trim($_POST['confirm_password']);

    if ($new !== $confirm) {
        $message = "❌ Passwords do not match!";
    } elseif (strlen($new) < 6) {
        $message = "❌ Password must be at least 6 characters long!";
    } else {
        $hashed = password_hash($new, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password=? WHERE user_id=?");
        $stmt->bind_param("si", $hashed, $user_id);

        if ($stmt->execute()) {
            $message = "✅ Password has been reset successfully! You can now login.";
            unset($_SESSION['reset_user_id'], $_SESSION['reset_otp'], $_SESSION['reset_expires'], $_SESSION['otp_verified']);
            header("Refresh:2; url=login.php");
        } else {
            $message = "❌ Failed to reset password. Try again!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Reset Password | PetNest</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include 'includes/navbar.php'; ?>

<div class="container py-5" style="max-width:560px;">
  <div class="card shadow-sm border-0">
    <div class="card-body p-4">
      <h3>🔑 Reset Password</h3>
      <?php if ($message): ?><div class="alert alert-info"><?= $message ?></div><?php endif; ?>

      <form method="POST" class="mt-3" novalidate>
        <div class="mb-3">
          <label class="form-label">New Password</label>
          <input type="password" name="new_password" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Confirm New Password</label>
          <input type="password" name="confirm_password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Reset Password</button>
        <a href="login.php" class="btn btn-secondary w-100 mt-2">← Back to Login</a>
      </form>
    </div>
  </div>
</div>
</body>
</html>
