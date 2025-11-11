<?php
session_start();
include 'includes/config.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = trim($_POST['current_password']);
    $new = trim($_POST['new_password']);
    $confirm = trim($_POST['confirm_password']);

    // Fetch current hashed password
    $stmt = $conn->prepare("SELECT password FROM users WHERE user_id=? LIMIT 1");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!password_verify($current, $user['password'])) {
        $message = "❌ Current password is incorrect.";
    } elseif ($new !== $confirm) {
        $message = "❌ New passwords do not match.";
    } elseif (strlen($new) < 6) {
        $message = "❌ Password must be at least 6 characters long.";
    } else {
        $hashed = password_hash($new, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password=? WHERE user_id=?");
        $stmt->bind_param("si", $hashed, $user_id);
        if ($stmt->execute()) {
            $message = "✅ Password changed successfully!";
        } else {
            $message = "❌ Failed to update password. Try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password | PetNest</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #a76cf1, #ff8ae2, #89c4ff);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px;
        }

        .card {
            background: rgba(255, 255, 255, 0.85);
            border: none;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            max-width: 500px;
            width: 100%;
            animation: fadeIn 1s ease;
        }

        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(20px);}
            to {opacity: 1; transform: translateY(0);}
        }

        h2 {
            color: #5a009d;
            font-weight: 700;
            text-align: center;
            margin-bottom: 30px;
        }

        label {
            font-weight: 600;
            color: #333;
        }

        .form-control {
            border-radius: 12px;
            padding: 10px 15px;
            border: 1px solid #ccc;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: #a76cf1;
            box-shadow: 0 0 8px rgba(167, 108, 241, 0.4);
        }

        .btn-gradient {
            background: linear-gradient(45deg, #a76cf1, #ff8ae2);
            color: white;
            border: none;
            border-radius: 50px;
            font-weight: 600;
            padding: 10px 25px;
            transition: 0.3s ease;
            width: 100%;
        }

        .btn-gradient:hover {
            transform: scale(1.05);
            background: linear-gradient(45deg, #ff8ae2, #a76cf1);
        }

        .alert {
            text-align: center;
            border-radius: 12px;
            animation: fadeIn 0.8s ease;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #888;
        }

        .back-link {
            text-align: center;
            margin-top: 15px;
            display: block;
            color: #5a009d;
            font-weight: 500;
            text-decoration: none;
            transition: 0.3s;
        }

        .back-link:hover {
            color: #9b30ff;
            text-decoration: underline;
        }

        @media (max-width: 576px) {
            .card {
                padding: 25px;
            }
            h2 {
                font-size: 1.6rem;
            }
        }
    </style>
</head>
<body>

<div class="card">
    <h2><i class="bi bi-shield-lock-fill"></i> Change Password</h2>

    <?php if($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3 position-relative">
            <label for="current_password">Current Password</label>
            <input type="password" name="current_password" id="current_password" class="form-control" required>
            <i class="bi bi-eye-slash toggle-password" data-target="current_password"></i>
        </div>

        <div class="mb-3 position-relative">
            <label for="new_password">New Password</label>
            <input type="password" name="new_password" id="new_password" class="form-control" required>
            <i class="bi bi-eye-slash toggle-password" data-target="new_password"></i>
        </div>

        <div class="mb-3 position-relative">
            <label for="confirm_password">Confirm New Password</label>
            <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
            <i class="bi bi-eye-slash toggle-password" data-target="confirm_password"></i>
        </div>

        <button type="submit" class="btn-gradient mt-3">Update Password</button>

        <a href="dashboard.php" class="back-link mt-3"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
    </form>
</div>

<script>
document.querySelectorAll('.toggle-password').forEach(icon => {
    let hideTimeout; // for auto hide
    
    icon.addEventListener('click', function() {
        const input = document.getElementById(this.dataset.target);
        const type = input.type === 'password' ? 'text' : 'password';
        input.type = type;
        this.classList.toggle('bi-eye');
        this.classList.toggle('bi-eye-slash');

        // Auto hide after 3 seconds if visible
        if (type === 'text') {
            clearTimeout(hideTimeout);
            hideTimeout = setTimeout(() => {
                input.type = 'password';
                this.classList.remove('bi-eye');
                this.classList.add('bi-eye-slash');
            }, 3000); // 3 seconds
        }
    });
});
</script>

</body>
</html>
