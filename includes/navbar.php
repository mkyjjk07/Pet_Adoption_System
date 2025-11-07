<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">🐾 PetAdopt</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="view_pets.php">Pets</a></li>
        <li class="nav-item"><a class="nav-link" href="about_us.php">About Us</a></li>
        <li class="nav-item"><a class="nav-link" href="donate.php">Donate</a></li>
        <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>

        <?php if (isset($_SESSION['user_id'])): ?>
            <!-- Normal User -->
            <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>

        <?php elseif (isset($_SESSION['admin_id'])): ?>
            <!-- Admin -->
            <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Admin Panel</a></li>
            <li class="nav-item"><a class="nav-link" href="admin_logout.php">Logout</a></li>

        <?php else: ?>
            <!-- Guest -->
            <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
            <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
            <li class="nav-item"><a class="nav-link" href="admin_login.php">Admin</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
