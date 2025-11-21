<?php
session_start();
include 'includes/config.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT admin_id, first_name, last_name, password, role, status FROM admins WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        if ($row['status'] !== 'active') {
            $error = "Your account has been deactivated by the Super Admin.";
        } elseif (password_verify($password, $row['password'])) {
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
  <meta charset="utf-8">
  <title>Admin Login | PetNest</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #c8d8ff, #b2c7ff, #d7e3ff);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    nav.navbar {
      background: linear-gradient(90deg, #3c5cff, #6aa6ff);
      font-size: 2rem;
      box-shadow: 0 3px 10px rgba(0,0,0,0.15);
    }

    .admin-wrapper {
      margin-top: 7vh;
      animation: fadeIn 1s ease-out;
    }

    @keyframes fadeIn {
      from {opacity: 0; transform: translateY(20px);}
      to   {opacity: 1; transform: translateY(0);}
    }

    .admin-card {
      backdrop-filter: blur(14px);
      background: rgba(255, 255, 255, 0.55);
      border-radius: 20px;
      padding: 35px;
      box-shadow: 0 10px 35px rgba(0,0,0,0.2);
      transition: 0.3s;
    }

    .admin-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 45px rgba(0,0,0,0.22);
    }

    .admin-title {
      font-weight: 600;
      font-size: 1.9rem;
      background: linear-gradient(90deg, #3c5cff, #6aa6ff);
      -webkit-background-clip: text;
      color: transparent;
      text-align: center;
      margin-bottom: 20px;
    }

    .form-control {
      border-radius: 12px;
      padding: 12px 14px;
      border: 1px solid #99b7ff;
      transition: 0.3s;
    }

    .form-control:focus {
      border-color: #6a8eff;
      box-shadow: 0 0 8px rgba(110, 140, 255, 0.45);
    }

    .btn-admin {
      background: linear-gradient(90deg, #3c5cff, #6aa6ff);
      border: none;
      padding: 12px;
      border-radius: 12px;
      font-size: 1.1rem;
      font-weight: 600;
      transition: 0.3s;
      color: #fff;
    }

    .btn-admin:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 14px rgba(60, 92, 255, 0.4);
    }

    #togglePassword {
      cursor: pointer;
      color: #3c5cff;
    }
  </style>
</head>

<body>
<?php include 'includes/navbar.php'; ?>

<div class="container admin-wrapper" style="max-width:550px;">

    <div class="admin-card">

        <h3 class="admin-title">
            <i class="bi bi-shield-lock-fill me-1"></i> Admin Login
        </h3>

        <?php if ($error): ?>
        <div class="alert alert-danger text-center">
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <form method="POST" autocomplete="off">

            <label class="form-label fw-semibold">Email</label>
            <input class="form-control mb-3" type="email" name="email" required 
                   autocomplete="off" onfocus="this.removeAttribute('readonly');" readonly>

            <label class="form-label fw-semibold">Password</label>
            <div class="position-relative mb-4">
                <input id="password" class="form-control" type="password" name="password" required>
                <span id="togglePassword" class="position-absolute top-50 end-0 translate-middle-y me-3">
                    <i class="bi bi-eye-slash"></i>
                </span>
            </div>

            <button class="btn btn-admin w-100" type="submit">Login</button>
        </form>

        <div class="text-center mt-3">
            <a href="index.php" class="fw-semibold" style="color:#3c5cff; text-decoration:none;">
                ← Back to Home
            </a>
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
