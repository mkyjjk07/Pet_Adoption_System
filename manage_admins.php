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

// -------------------- Add New Admin --------------------
if (isset($_POST['add_admin'])) {
    $first = trim($_POST['first_name']);
    $last  = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $mobile= trim($_POST['mobile_no']);
    $username = trim($_POST['username']);
    $role = $_POST['role'] === 'super_admin' ? 'super_admin' : 'staff';
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO admins (username, password, role, first_name, last_name, email, mobile_no) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $username, $password, $role, $first, $last, $email, $mobile);

    if ($stmt->execute()) {
        $notice = "New admin added successfully!";
    } else {
        $notice = "Error: " . $stmt->error;
    }
}

// -------------------- Update Admin Role --------------------
if (isset($_POST['change_role'], $_POST['admin_id'], $_POST['role'])) {
    $aid = (int) $_POST['admin_id'];
    $role = $_POST['role'] === 'super_admin' ? 'super_admin' : 'staff';
    $upd = $conn->prepare("UPDATE admins SET role=? WHERE admin_id=?");
    $upd->bind_param("si", $role, $aid);
    if ($upd->execute()) $notice = "Role updated.";
}

// -------------------- Fetch all admins --------------------
$list = $conn->query("SELECT admin_id, first_name, last_name, email, mobile_no, username, role FROM admins");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Manage Admins | PetNest</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include 'includes/navbar.php'; ?>

<div class="container py-5">
  <h3 class="mb-3">🛡️ Manage Admins</h3>

  <?php if ($notice): ?>
    <div class="alert alert-success"><?= htmlspecialchars($notice) ?></div>
  <?php endif; ?>

  <!-- Existing Admins Table -->
  <div class="table-responsive card shadow-sm border-0">
    <table class="table table-striped align-middle mb-0">
      <thead class="table-dark">
        <tr>
          <th>#</th><th>Name</th><th>Username</th><th>Email</th><th>Mobile</th><th>Role</th><th>Joined</th><th>Action</th>
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
            <td><span class="badge bg-<?= $a['role']==='super_admin'?'success':'secondary' ?>"><?= $a['role'] ?></span></td>
            <td>
              <?php if ($a['admin_id'] != $_SESSION['admin_id']): ?>
                <form method="post" class="d-flex gap-2">
                  <input type="hidden" name="admin_id" value="<?= $a['admin_id'] ?>">
                  <select name="role" class="form-select form-select-sm" style="width:auto;">
                    <option value="staff" <?= $a['role']==='staff'?'selected':''; ?>>staff</option>
                    <option value="super_admin" <?= $a['role']==='super_admin'?'selected':''; ?>>super_admin</option>
                  </select>
                  <button class="btn btn-sm btn-primary" name="change_role" value="1">Save</button>
                </form>
              <?php else: ?>
                <em>—</em>
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <div class="container py-5">
  <h3 class="mb-3">🛡️ Manage Admins</h3>

  <?php if ($notice): ?>
    <div class="alert alert-success"><?= htmlspecialchars($notice) ?></div>
  <?php endif; ?>

  <!-- Add New Admin Form -->
  <div class="card mb-4 p-4 shadow-sm">
    <h5>Add New Admin</h5>
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
        <input type="password" class="form-control" name="password" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Role</label>
        <select name="role" class="form-select">
          <option value="staff">Staff</option>
          <option value="super_admin">Super Admin</option>
        </select>
      </div>
      <div class="col-12">
        <button type="submit" name="add_admin" class="btn btn-success">Add Admin</button>
      </div>
    </form>
  </div>

  <div class="mt-3">
    <a class="btn btn-outline-secondary" href="admin_dashboard.php">← Back to Dashboard</a>
  </div>
</div>
</body>
</html>
