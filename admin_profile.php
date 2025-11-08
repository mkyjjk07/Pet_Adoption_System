<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
include 'includes/config.php';

$admin_id = (int) $_SESSION['admin_id'];
$success = $error = "";

// Fetch
$stmt = $conn->prepare("SELECT first_name, last_name, email, mobile_no, username, role  FROM admins WHERE admin_id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$profile = $stmt->get_result()->fetch_assoc();

// Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first  = trim($_POST['first_name']);
    $last   = trim($_POST['last_name']);
    $email  = trim($_POST['email']);
    $mobile = trim($_POST['mobile_no']);
    $usernm = trim($_POST['username']);

    // Optional password change
    $passSql = ""; $types = "ssssi"; $params = [$first, $last, $email, $mobile, $admin_id];
    if (!empty($_POST['password'])) {
        $hashed = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $passSql = ", password = ?";
        $types .= "s";
        $params = [$first, $last, $email, $mobile, $admin_id, $hashed];
    }

    $sql = "UPDATE admins SET first_name=?, last_name=?, email=?, mobile_no=? $passSql WHERE admin_id=?";
    // reorder params if password present (append at 5th)
    if ($passSql) {
        $sql = "UPDATE admins SET first_name=?, last_name=?, email=?, mobile_no=?, password=? WHERE admin_id=?";
        $types = "sssssi";
        $params = [$first, $last, $email, $mobile, $hashed, $admin_id];
    }

    $upd = $conn->prepare($sql);
    $upd->bind_param($types, ...$params);

    if ($upd->execute()) {
        $success = "Profile updated successfully.";
        $_SESSION['admin_name'] = $first . ' ' . $last;
        // refresh
        $stmt = $conn->prepare("SELECT first_name, last_name, email, mobile_no, username, role FROM admins WHERE admin_id = ?");
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $profile = $stmt->get_result()->fetch_assoc();
    } else {
        $error = "Failed to update profile.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Profile | PetNest</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include 'includes/navbar.php'; ?>

<div class="container py-5" style="max-width:760px;">
  <div class="card shadow-sm border-0">
    <div class="card-body p-4">
      <h3 class="mb-3">👤 Admin Profile</h3>

      <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
      <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

      <form method="POST" novalidate>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">First Name</label>
            <input class="form-control" name="first_name" value="<?= htmlspecialchars($profile['first_name']??'') ?>" required>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Last Name</label>
            <input class="form-control" name="last_name" value="<?= htmlspecialchars($profile['last_name']??'') ?>" required>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Username</label>
            <input class="form-control" name="username" value="<?= htmlspecialchars($profile['username']??'') ?>" disabled>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Role</label>
            <input class="form-control" value="<?= htmlspecialchars($profile['role']??'staff') ?>" disabled>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Email</label>
            <input class="form-control" type="email" name="email" value="<?= htmlspecialchars($profile['email']??'') ?>" required>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Mobile</label>
            <input class="form-control" name="mobile_no" value="<?= htmlspecialchars($profile['mobile_no']??'') ?>">
          </div>
          <div class="col-12 mb-2">
            <label class="form-label">New Password (optional)</label>
          </div>
          <div class="col-md-12 mb-3 position-relative">
          <input id="password" class="form-control" type="password" name="password" placeholder="Leave blank to keep current" required>
          <span id="togglePassword" class="position-absolute top-50 end-0 translate-middle-y me-3" style="cursor:pointer;">
            <i class="bi bi-eye-slash"></i>
          </span>
        </div>
        </div>
        <div class="d-flex gap-2">
          <button class="btn btn-primary" type="submit">Save Changes</button>
          <a class="btn btn-outline-secondary" href="admin_dashboard.php">Back to Dashboard</a>
        </div>
      </form>
    </div>
  </div>
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
