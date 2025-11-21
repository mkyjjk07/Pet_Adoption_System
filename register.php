<?php
session_start();
include 'includes/config.php';

$message = '';
$messageType = 'info';
$registered = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password_raw = $_POST['password'];
    $confirm_raw  = $_POST['confirm_password'];
    $phone    = trim($_POST['phone']);
    $city     = trim($_POST['city']);
    $role     = $_POST['role'];

    if ($password_raw !== $confirm_raw) {
        $message = "Passwords do not match!";
        $messageType = "danger";
    } else {

        $password = password_hash($password_raw, PASSWORD_DEFAULT);

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
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>User Registration | PetNest</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #f3e7ff, #fbc2eb, #e7d9ff);
      min-height: 100vh;
    }

    nav.navbar {
      background: linear-gradient(90deg, #a18cd1, #fbc2eb);
      font-size: 2rem;
      box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }

    .register-wrapper {
      max-width: 650px;
      margin: 6vh auto;
      animation: fadeUp 0.9s ease-out;
    }

    @keyframes fadeUp {
      from { opacity:0; transform: translateY(25px); }
      to   { opacity:1; transform: translateY(0); }
    }

    .register-card {
      backdrop-filter: blur(15px);
      background: rgba(255,255,255,0.55);
      border-radius: 20px;
      padding: 35px;
      box-shadow: 0 10px 35px rgba(0,0,0,0.15);
      transition: 0.3s;
    }

    .register-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 45px rgba(0,0,0,0.18);
    }

    .register-title {
      font-weight: 700;
      background: linear-gradient(120deg, #a78bfa, #f472b6);
      -webkit-background-clip: text;
      color: transparent;
      text-align: center;
      font-size: 2rem;
      margin-bottom: 15px;
    }

    .form-control, .form-select {
      border-radius: 12px;
      padding: 12px;
      border: 1px solid #d0b8ff;
      transition: 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
      border-color: #b78df2;
      box-shadow: 0 0 8px rgba(173, 126, 255, 0.45);
    }

    .btn-register {
      background: linear-gradient(120deg, #a18cd1, #fbc2eb);
      border: none;
      padding: 12px;
      border-radius: 12px;
      font-size: 1.1rem;
      font-weight: 600;
      color: #fff;
      transition: 0.3s;
    }

    .btn-register:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 14px rgba(157,94,255,0.4);
    }

    .toggle-eye {
      cursor: pointer;
      color:#7f61c6;
    }
  </style>
</head>

<body>
<?php include 'includes/navbar.php'; ?>

<div class="container register-wrapper">

  <div class="register-card">

    <h2 class="register-title">Create Your Account ✨</h2>

    <?php if ($message): ?>
      <div class="alert alert-<?= $messageType ?> text-center mb-3">
        <?= $message ?>
      </div>
    <?php endif; ?>

    <?php if ($registered): ?>

      <div class="d-flex gap-2 mt-3">
        <a class="btn btn-success w-50" href="login.php">Go to Login</a>
        <a class="btn btn-outline-secondary w-50" href="index.php">Back to Home</a>
      </div>

    <?php else: ?>

      <form method="POST" autocomplete="off">

        <div class="mb-3">
          <input class="form-control" type="text" name="name" placeholder="Full Name"
            required value="<?= isset($name)?htmlspecialchars($name):'' ?>">
        </div>

        <div class="mb-3">
          <input class="form-control" type="email" name="email" placeholder="Email"
            required value="<?= isset($email)?htmlspecialchars($email):'' ?>">
        </div>

        <div class="mb-3 position-relative">
          <input id="password" class="form-control" type="password" name="password" placeholder="Password" required>
          <span id="togglePassword1" class="toggle-eye position-absolute top-50 end-0 translate-middle-y me-3">
            <i class="bi bi-eye-slash"></i>
          </span>
        </div>

        <div class="mb-3 position-relative">
          <input id="confirm_password" class="form-control" type="password" name="confirm_password"
            placeholder="Confirm Password" required>
          <span id="togglePassword2" class="toggle-eye position-absolute top-50 end-0 translate-middle-y me-3">
            <i class="bi bi-eye-slash"></i>
          </span>
        </div>

        <div class="mb-3">
          <input class="form-control" type="text" name="phone" placeholder="Phone Number"
            value="<?= isset($phone)?htmlspecialchars($phone):'' ?>">
        </div>

        <div class="mb-3">
          <input class="form-control" type="text" name="city" placeholder="City"
            value="<?= isset($city)?htmlspecialchars($city):'' ?>">
        </div>

        <div class="mb-3">
          <select name="role" class="form-select">
            <option value="adopter"   <?= (isset($role)&&$role==='adopter')?'selected':'' ?>>Adopter</option>
            <option value="guest"     <?= (isset($role)&&$role==='guest')?'selected':'' ?>>Guest</option>
            <option value="volunteer" <?= (isset($role)&&$role==='volunteer')?'selected':'' ?>>Volunteer</option>
          </select>
        </div>

        <button class="btn btn-register w-100" type="submit">Register</button>

        <div class="text-center mt-3">
          <a href="login.php" class="fw-semibold text-decoration-none">Already have an account? Login</a>
        </div>

      </form>

    <?php endif; ?>

  </div>
</div>


<script>
function attachToggle(idInput, idIcon) {
    const input = document.getElementById(idInput);
    const icon  = document.getElementById(idIcon);

    icon.addEventListener("click", () => {
        const type = input.type === "password" ? "text" : "password";
        input.type = type;
        icon.innerHTML = type === "password"
            ? '<i class="bi bi-eye-slash"></i>'
            : '<i class="bi bi-eye"></i>';
    });
}

attachToggle("password", "togglePassword1");
attachToggle("confirm_password", "togglePassword2");
</script>

</body>
</html>
