<?php
session_start();
include 'includes/config.php';

$message = '';
$messageType = 'info';
$registered = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone    = trim($_POST['phone']);
    $city     = trim($_POST['city']);
    $role     = $_POST['role']; // adopter, guest, volunteer

    $allowedRoles = ['adopter','guest','volunteer'];
    if (!in_array($role, $allowedRoles, true)) $role = 'adopter';

    $check = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $message = "Email already exists. You can login using your password.";
        $messageType = 'danger';
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone, city, role) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $name, $email, $password, $phone, $city, $role);

        if ($stmt->execute()) {
            $registered = true;
            $message = "✅ Registration successful! Your account has been created.";
            $messageType = 'success';
        } else {
            $message = "Error creating account: " . $stmt->error;
            $messageType = 'danger';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>User Registration | PetNest</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

</head>
<body class="bg-light">
<?php include 'includes/navbar.php'; ?>

<div class="container mt-5" style="max-width:720px;">
  <h2 class="mb-3">Create your account</h2>

  <?php if ($message): ?>
    <div class="alert alert-<?= $messageType; ?>"><?= $message; ?></div>
  <?php endif; ?>

  <?php if ($registered): ?>
    <div class="d-flex gap-2">
      <a class="btn btn-success" href="login.php">Go to Login</a>
      <a class="btn btn-outline-secondary" href="index.php">Back to Home</a>
    </div>
  <?php else: ?>
    <form method="POST" class="card p-3 shadow-sm border-0" autocomplete="off">
      <div class="row">
        <div class="col-md-6 mb-2">
          <input class="form-control" type="text" name="name" placeholder="Full Name" required value="<?= isset($name) ? htmlspecialchars($name) : '' ?>">
        </div>
        <div class="col-md-6 mb-2">
          <input class="form-control" type="email" name="email" placeholder="Email" required value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
        </div>
      </div>
      <div class="row">
        <div class="col-md-6 mb-2 position-relative">
          <input id="password" class="form-control" type="password" name="password" placeholder="Password" required>
          <span id="togglePassword" class="position-absolute top-50 end-0 translate-middle-y me-3" style="cursor:pointer;">
            <i class="bi bi-eye-slash"></i>
          </span>
        </div>

        <div class="col-md-6 mb-2">
          <input class="form-control" type="text" name="phone" placeholder="Phone Number" value="<?= isset($phone) ? htmlspecialchars($phone) : '' ?>">
        </div>
      </div>
      <div class="row">
        <div class="col-md-6 mb-2">
          <input class="form-control" type="text" name="city" placeholder="City" value="<?= isset($city) ? htmlspecialchars($city) : '' ?>">
        </div>
        <div class="col-md-6 mb-3">
          <select name="role" class="form-select">
            <option value="adopter"   <?= (isset($role) && $role==='adopter')?'selected':''; ?>>Adopter</option>
            <option value="guest"     <?= (isset($role) && $role==='guest')?'selected':''; ?>>Guest</option>
            <option value="volunteer" <?= (isset($role) && $role==='volunteer')?'selected':''; ?>>Volunteer</option>
          </select>
        </div>
      </div>
      <button class="btn btn-primary" type="submit">Register</button>
      <a class="btn btn-link" href="login.php">Already have an account? Login</a>
    </form>
  <?php endif; ?>
</div>
<script>
  const togglePassword = document.querySelector('#togglePassword');
  const password = document.querySelector('#password');
  let hideTimeout;

  togglePassword.addEventListener('click', function () {
    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
    password.setAttribute('type', type);
    this.innerHTML = type === 'password'
      ? '<i class="bi bi-eye-slash"></i>'
      : '<i class="bi bi-eye"></i>';

    // Auto hide password after 3 seconds if shown
    if (type === 'text') {
      clearTimeout(hideTimeout);
      hideTimeout = setTimeout(() => {
        password.setAttribute('type', 'password');
        togglePassword.innerHTML = '<i class="bi bi-eye-slash"></i>';
      }, 3000);
    }
  });
</script>

</body>
</html>
