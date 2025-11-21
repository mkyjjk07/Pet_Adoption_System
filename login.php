<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'includes/config.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT user_id, name, password, role FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($user_id, $name, $hashed_password, $role);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['name']    = $name;
            $_SESSION['role']    = $role;

            header("Location: dashboard.php");
            exit();
        } else {
            $message = "Invalid password!";
        }
    } else {
        $message = "User not found!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>User Login | PetNest</title>
  
  <!-- SAME LINKS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #f6d4ff, #e5c9ff, #fbc2eb);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    
    nav.navbar {
      background: linear-gradient(90deg, #a18cd1, #fbc2eb);
      font-size: 2rem;
      box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }

    .login-wrapper {
      margin-top: 7vh;
      animation: fadeIn 1s ease-out;
    }

    @keyframes fadeIn {
      from {opacity: 0; transform: translateY(20px);}
      to   {opacity: 1; transform: translateY(0);}
    }

    .login-card {
      backdrop-filter: blur(14px);
      background: rgba(255, 255, 255, 0.55);
      border-radius: 20px;
      padding: 35px;
      box-shadow: 0 10px 35px rgba(0,0,0,0.15);
      transition: 0.3s;
    }

    .login-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 40px rgba(0,0,0,0.18);
    }

    .login-title {
      font-weight: 600;
      font-size: 1.9rem;
      background: linear-gradient(90deg, #a18cd1, #fbc2eb);
      -webkit-background-clip: text;
      color: transparent;
      text-align: center;
      margin-bottom: 20px;
    }

    .form-control {
      border-radius: 12px;
      padding: 12px 14px;
      border: 1px solid #d0b8ff;
      transition: 0.3s;
    }

    .form-control:focus {
      border-color: #b78df2;
      box-shadow: 0 0 8px rgba(173, 126, 255, 0.45);
    }

    .btn-login {
      background: linear-gradient(90deg, #a18cd1, #fbc2eb);
      border: none;
      padding: 12px;
      border-radius: 12px;
      font-size: 1.1rem;
      font-weight: 600;
      transition: 0.3s;
      color: #fff;
    }

    .btn-login:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 14px rgba(157, 94, 255, 0.4);
    }

    #togglePassword {
      cursor: pointer;
      color: #7f61c6;
    }
  </style>

</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="container login-wrapper" style="max-width: 500px;">

  <div class="login-card">

    <h2 class="login-title">Welcome Back 👋</h2>

    <?php if ($message): ?>
      <div class="alert alert-danger text-center">
        <?= htmlspecialchars($message) ?>
      </div>
    <?php endif; ?>

    <form method="POST" novalidate autocomplete="off">

      <label class="form-label">Email</label>
      <input class="form-control mb-3" type="email" name="email" required>

      <label class="form-label">Password</label>
      <div class="position-relative mb-4">
        <input id="password" class="form-control" type="password" name="password" required>
        <span id="togglePassword" class="position-absolute top-50 end-0 translate-middle-y me-3">
          <i class="bi bi-eye-slash"></i>
        </span>
      </div>

      <button class="btn btn-login w-100" type="submit">Login</button>
    </form>

    <div class="text-center mt-4">
      <a href="forgot_password.php" class="btn btn-warning w-100 mb-2">Forgot Password?</a>
      <a href="register.php" class="text-decoration-none fw-semibold">New here? Create an account</a>
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
