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
    $mobile = trim($_POST['mobile_no']);
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
$admins = $conn->query("SELECT admin_id, first_name, last_name, email, mobile_no, username, role, status 
                        FROM admins ORDER BY admin_id ASC");

// -------------------- Fetch all users --------------------
$users = $conn->query("SELECT user_id, name, email, phone, role, created_at 
                       FROM users ORDER BY user_id ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Admin & User Management | PetNest</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    body {
        background-color: #f9fafc;
        font-family: "Poppins", sans-serif;
    }
    .page-header {
        text-align: center;
        margin: 30px 0;
    }
    .page-header h3 {
        font-weight: 700;
        color: #2c7a7b;
    }
    .card-custom {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
    }
    .card-custom:hover {
        transform: translateY(-3px);
    }
    .btn-modern {
        background-color: #2c7a7b;
        color: white;
        border: none;
        border-radius: 8px;
        padding: 10px 18px;
        transition: 0.3s;
    }
    .btn-modern:hover {
        background-color: #256c6e;
        transform: translateY(-2px);
    }
    thead {
        background-color: #2c7a7b;
        color: #fff;
    }
    .table-hover tbody tr:hover {
        background-color: #f1f9f9;
    }
</style>
</head>

<body>
<?php include 'includes/navbar.php'; ?>

<div class="container py-5">
    <div class="page-header">
        <h3>🛡️ Admin & User Management</h3>
        <p class="text-muted">Manage all system admins, staff, and registered users.</p>
    </div>

    <?php if ($notice): ?>
        <div class="alert alert-info text-center"><?= htmlspecialchars($notice) ?></div>
    <?php endif; ?>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" id="managementTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" id="admins-tab" data-bs-toggle="tab" data-bs-target="#admins" role="tab">👮‍♂️ Admins</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" role="tab">👥 Users</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="add-tab" data-bs-toggle="tab" data-bs-target="#addAdmin" role="tab">➕ Add Admin</button>
        </li>
    </ul>

    <div class="tab-content" id="managementTabsContent">
        <!-- Admins -->
        <div class="tab-pane fade show active" id="admins" role="tabpanel">
            <div class="card card-custom p-4">
                <h5 class="mb-3">Admin Accounts</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Admin id</th>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Mobile</th>        
                                <th>Role</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($a = $admins->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $a['admin_id'] ?></td>
                                    <td><?= htmlspecialchars($a['first_name'].' '.$a['last_name']) ?></td>
                                    <td><?= htmlspecialchars($a['username']) ?></td>
                                    <td><?= htmlspecialchars($a['email']) ?></td>
                                    <td><?= htmlspecialchars($a['mobile_no']) ?></td>
                                    <td><span class="badge bg-secondary"><?= ucfirst($a['role']) ?></span></td>
                                    <td>
                                        <?php if ($a['admin_id'] != $_SESSION['admin_id']): ?>
                                            <form method="post" class="d-inline">
                                                <input type="hidden" name="admin_id" value="<?= $a['admin_id'] ?>">
                                                <input type="hidden" name="current_status" value="<?= $a['status'] ?>">
                                                <button class="btn btn-sm btn-<?= $a['status']==='active'?'warning':'success' ?>" name="toggle_status" value="1">
                                                    <i class="bi bi-power"></i> <?= $a['status']==='active'?'Deactivate':'Activate' ?>
                                                </button>
                                            </form>
                                            <button class="btn btn-sm btn-info text-white" 
                                                    onclick="viewDetails('<?= htmlspecialchars($a['first_name'].' '.$a['last_name']) ?>', '<?= htmlspecialchars($a['email']) ?>', '<?= htmlspecialchars($a['mobile_no']) ?>', '<?= htmlspecialchars($a['username']) ?>', '<?= ucfirst($a['status']) ?>')">
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
            </div>
        </div>

        <!-- Users -->
        <div class="tab-pane fade" id="users" role="tabpanel">
            <div class="card card-custom p-4">
                <h5 class="mb-3">Registered Users</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>User id</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($u = $users->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $u['user_id'] ?></td>
                                    <td><?= htmlspecialchars($u['name']) ?></td>
                                    <td><?= htmlspecialchars($u['email']) ?></td>
                                    <td><?= htmlspecialchars($u['phone']) ?></td>
                                    <td><span class="badge bg-primary"><?= ucfirst($u['role']) ?></span></td>
                                    <td><?= date("d M Y, h:i A", strtotime($u['created_at'])) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add New Admin -->
        <div class="tab-pane fade" id="addAdmin" role="tabpanel">
            <div class="card card-custom p-4">
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
                    <div class="col-12 text-end">
                        <button type="submit" name="add_admin" class="btn-modern">
                            <i class="bi bi-person-plus"></i> Add Admin
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="text-center mt-4">
        <a href="admin_dashboard.php" class="btn btn-outline-secondary">← Back to Dashboard</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const togglePassword = document.querySelector('#togglePassword');
const password = document.querySelector('#password');
let hideTimeout;
togglePassword.addEventListener('click', function () {
    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
    password.setAttribute('type', type);
    this.innerHTML = type === 'password' ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
    if (type === 'text') {
        clearTimeout(hideTimeout);
        hideTimeout = setTimeout(() => {
            password.setAttribute('type', 'password');
            togglePassword.innerHTML = '<i class="bi bi-eye-slash"></i>';
        }, 3000);
    }
});

function viewDetails(name, email, mobile, username, status) {
    Swal.fire({
        title: '👤 Admin Details',
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
