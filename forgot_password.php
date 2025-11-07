<?php
session_start();
include 'includes/config.php';
include 'includes/send_mail.php'; // For sending email

$message = "";
$show_proceed_button = false;

// Generate CAPTCHA if not already set or on initial GET
if (!isset($_SESSION['captcha']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['captcha'] = rand(1000, 9999);
}
$captcha = $_SESSION['captcha'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input  = trim($_POST['input']);
    $captcha_input = trim($_POST['captcha']);

    if ($captcha_input != $_SESSION['captcha']) {
        $message = "❌ CAPTCHA is incorrect!";
    } else {
        $stmt = $conn->prepare("SELECT user_id, name, email FROM users WHERE email=? LIMIT 1");
        $stmt->bind_param("s", $input);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            $otp = rand(100000, 999999);
            $_SESSION['reset_user_id'] = $user['user_id'];
            $_SESSION['reset_otp'] = $otp;
            $_SESSION['reset_expires'] = time() + 300; // 5 minutes

            unset($_SESSION['captcha']); // regenerate next time

           // Email details
          $subject = "PetNest Password Reset OTP";
          $body = "
              <div style='font-family: Arial, sans-serif; line-height:1.6;'>
                  <h2>Hi {$user['name']},</h2>
                  <p>Your OTP for resetting your password is:</p>
                  <h3 style='color:#2c7be5; font-size:24px;'>$otp</h3>
                  <p>This OTP is valid for <strong>5 minutes</strong>.</p>
                  <hr>
                  <p style='margin-top:10px;'>Best regards,<br><strong>PetNest Team</strong></p>
                  <p><strong>Contact us:</strong> 
                      <a href='mailto:contact@petadopt.com' style='color:#2c7be5;'>contact@petadopt.com</a>
                  </p>
                  <p style='color:red; font-size:12px;'>
                      <i>This is an auto-generated email, please do not reply.</i>
                  </p>
              </div>
          ";

            // Try sending email
            if (sendMail($user['email'], $subject, $body)) {
                $message = "✅ OTP has been sent to your email address!";
            } else {
                $message = "⚠️ Failed to send email. Please try again.";
            }

            $show_proceed_button = true;
        } else {
            $message = "❌ No user found with that email address.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Forgot Password | PetNest</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include 'includes/navbar.php'; ?>

<div class="container py-5" style="max-width:560px;">
  <div class="card shadow-sm border-0">
    <div class="card-body p-4">
      <h3>🔑 Forgot Password</h3>

      <?php if ($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
      <?php endif; ?>

      <form method="POST" class="mt-3" novalidate>
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="input" class="form-control" placeholder="Enter Email" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Enter CAPTCHA: <strong><?= $captcha ?></strong></label>
          <input type="text" name="captcha" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Send OTP</button>
        <a href="login.php" class="btn btn-secondary w-100 mt-2">← Back to Login</a>
      </form>

      <?php if ($show_proceed_button): ?>
        <a href="verify_otp.php" class="btn btn-success w-100 mt-3">Proceed to Verify OTP</a>
      <?php endif; ?>
    </div>
  </div>
</div>
</body>
</html>
