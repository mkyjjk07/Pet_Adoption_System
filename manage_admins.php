<?php 
session_start();
if (!isset($_SESSION['admin_id'])) { 
    header("Location: admin_login.php"); 
    exit(); 
}

// Only super admin can access
if (($_SESSION['admin_role'] ?? 'staff') !== 'super_admin') { 
    header("Location: admin_dashboard.php"); 
    exit(); 
}

include 'includes/config.php';
$notice = "";

// -------------------- Add New Admin (Staff) --------------------
if (isset($_POST['add_admin'])) {
    $first = trim($_POST['first_name']);
    $last  = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $mobile= trim($_POST['mobile_no']);
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'staff';
    $status = 'active';

    $stmt = $conn->prepare("INSERT INTO admins (username, password, role, first_name, last_name, email, mobile_no, status) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $username, $password, $role, $first, $last, $email, $mobile, $status);

    if ($stmt->execute()) {
        $notice = "✅ New staff admin added successfully!";
    } else {
        $notice = "❌ Error: " . $stmt->error;
    }
}

// -------------------- Toggle Activation --------------------
if (isset($_POST['toggle_status'], $_POST['admin_id'])) {
    $aid = (int) $_POST['admin_id'];
    $newStatus = $_POST['current_status'] === 'active' ? 'inactive' : 'active';

    $upd = $conn->prepare("UPDATE admins SET status=? WHERE admin_id=?");
    $upd->bind_param("si", $newStatus, $aid);
    if ($upd->execute()) $notice = "Admin status updated to '$newStatus'.";
}

// -------------------- Fetch all admins --------------------
$list = $conn->query("SELECT admin_id, first_name, last_name, email, mobile_no, username, role, status
                      FROM admins ORDER BY admin_id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Manage Admins | PetAdopt</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light">
<?php include 'includes/navbar.php'; ?>

<div class="container py-5">
  <h3 class="mb-3">🛡️ Manage Admins</h3>

  <?php if ($notice): ?>
    <div class="alert alert-info"><?= htmlspecialchars($notice) ?></div>
  <?php endif; ?>

  <!-- Admins Table -->
  <div class="table-responsive card shadow-sm border-0 mb-4">
    <table class="table table-striped align-middle mb-0">
      <thead class="table-dark">
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Username</th>
          <th>Email</th>
          <th>Mobile</th>
          <th>Status</th>
          <th>Role</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($a = $list->fetch_assoc()): ?>
          <tr>
            <td><?= $a['admin_id'] ?></td>
            <td><?= htmlspecialchars($a['first_name'].' '.$a['last_name']) ?></td>
            <td><?= htmlspecialchars($a['username']) ?></td>
            <td><?= htmlspecialchars($a['email']) ?></td>
            <td><?= htmlspecialchars($a['mobile_no']) ?></td>
            <td>
              <span class="badge bg-<?= $a['status']==='active'?'success':'danger' ?>">
                <?= ucfirst($a['status']) ?>
              </span>
            </td>
            <td><span class="badge bg-secondary"><?= $a['role'] ?></span></td>
            <td>
              <?php if ($a['admin_id'] != $_SESSION['admin_id']): ?>
                <form method="post" class="d-inline">
                  <input type="hidden" name="admin_id" value="<?= $a['admin_id'] ?>">
                  <input type="hidden" name="current_status" value="<?= $a['status'] ?>">
                  <button class="btn btn-sm btn-<?= $a['status']==='active'?'warning':'success' ?>" 
                          name="toggle_status" value="1">
                    <i class="bi bi-power"></i> <?= $a['status']==='active'?'Deactivate':'Activate' ?>
                  </button>
                </form>
                <button class="btn btn-sm btn-info text-white" 
                        onclick="viewDetails('<?= htmlspecialchars($a['first_name'].' '.$a['last_name']) ?>', 
                                             '<?= htmlspecialchars($a['email']) ?>', 
                                             '<?= htmlspecialchars($a['mobile_no']) ?>', 
                                             '<?= htmlspecialchars($a['username']) ?>', 
                                             '<?= ucfirst($a['status']) ?>')">
                  <i class="bi bi-eye"></i> View
                </button>
              <?php else: ?>
                <em>—</em>
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <!-- Add New Admin Form -->
  <div class="card p-4 shadow-sm">
    <h5>Add New Staff Admin</h5>
    <p class="text-muted small">Only Super Admin can create new staff accounts. Role and privileges are automatically assigned.</p>

    <form method="post" class="row g-3">
      <div class="col-md-6">
        <label class="form-label">First Name</label>
        <input type="text" class="form-control" name="first_name" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Last Name</label>
        <input type="text" class="form-control" name="last_name" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" class="form-control" name="email" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Mobile No</label>
        <input type="text" class="form-control" name="mobile_no" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Username</label>
        <input type="text" class="form-control" name="username" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Password</label>
        <div class="position-relative">
          <input id="password" class="form-control" type="password" name="password" required>
          <span id="togglePassword" class="position-absolute top-50 end-0 translate-middle-y me-3" style="cursor:pointer;">
            <i class="bi bi-eye-slash"></i>
          </span>
        </div>
      </div>
      <div class="col-12">
        <button type="submit" name="add_admin" class="btn btn-success">
          <i class="bi bi-person-plus"></i> Add Admin
        </button>
      </div>
    </form>
  </div>

  <div class="mt-3">
    <a class="btn btn-outline-secondary" href="admin_dashboard.php">← Back to Dashboard</a>
  </div>
</div>

<script>
  // Password toggle
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

  // View details modal
  function viewDetails(name, email, mobile, username, status) {
    Swal.fire({
      title: '👤 Staff Details',
      html: `
        <p><b>Name:</b> ${name}</p>
        <p><b>Username:</b> ${username}</p>
        <p><b>Email:</b> ${email}</p>
        <p><b>Mobile:</b> ${mobile}</p>
        <p><b>Status:</b> <span class="badge bg-${status==='Active'?'success':'danger'}">${status}</span></p>
      `,
      icon: 'info',
      confirmButtonText: 'Close'
    });
  }
</script>
</body>
</html>
