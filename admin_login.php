<?php
session_start();
include 'includes/config.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    // Include status field to check if the admin account is active or not
    $stmt = $conn->prepare("SELECT admin_id, first_name, last_name, password, role, status FROM admins WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        if ($row['status'] !== 'active') {
            $error = "Your account has been deactivated by the Super Admin.";
        } elseif (password_verify($password, $row['password'])) {
            // ✅ Store all needed details in session
            $_SESSION['admin_id']   = $row['admin_id'];
            $_SESSION['admin_name'] = trim($row['first_name'] . ' ' . $row['last_name']);
            $_SESSION['admin_role'] = $row['role'] ?? 'staff';

            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "Admin not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Admin Login | PetNest</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light">
<?php include 'includes/navbar.php'; ?>

<div class="container py-5" style="max-width:560px;">
  <div class="card shadow-sm border-0">
    <div class="card-body p-4">
      <h3 class="mb-3">🔐 Admin Login</h3>

      <?php if ($error): ?>
        <script>
          Swal.fire({
            icon: 'error',
            title: 'Login Failed',
            text: <?= json_encode($error) ?>,
            confirmButtonColor: '#2563eb'
          });
        </script>
      <?php endif; ?>

      <form method="POST" novalidate autocomplete="off">
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input class="form-control" type="email" name="email" required 
                 autocomplete="off" onfocus="this.removeAttribute('readonly');" readonly>
        </div>
        <div class="col-md-12 mb-3 position-relative">
          <input id="password" class="form-control" type="password" name="password" placeholder="Password" required>
          <span id="togglePassword" class="position-absolute top-50 end-0 translate-middle-y me-3" style="cursor:pointer;">
            <i class="bi bi-eye-slash"></i>
          </span>
        </div>
        <button class="btn btn-primary w-100" type="submit">Login</button>
      </form>

      <div class="text-center mt-3">
        <a href="index.php" class="btn btn-link">← Back to Home</a>
      </div>
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
