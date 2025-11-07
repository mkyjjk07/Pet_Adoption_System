<?php
session_start();
include 'includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'guest') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $update = $conn->prepare("UPDATE users SET role = 'adopter' WHERE user_id = ?");
    $update->bind_param("i", $user_id);

    if ($update->execute()) {
        $_SESSION['role'] = 'adopter';
        echo "<script>
            sessionStorage.setItem('upgradeSuccess', 'true');
            window.location.href = 'dashboard.php';
        </script>";
        exit();
    } else {
        echo "<script>alert('Something went wrong. Please try again later.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upgrade to Adopter | PetAdopt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #e0f2fe, #f8fafc);
            color: #333;
        }
        .container {
            max-width: 900px;
            margin-top: 60px;
            background: #fff;
            padding: 40px 45px;
            border-radius: 20px;
            box-shadow: 0 8px 18px rgba(0,0,0,0.1);
        }
        h2 {
            font-weight: 700;
            color: #1e3a8a;
            margin-bottom: 15px;
        }
        p.lead {
            font-size: 1.05rem;
            line-height: 1.7;
            color: #4b5563;
        }
        ul {
            margin-left: 25px;
            line-height: 1.8;
        }
        .section-title {
            font-weight: 600;
            font-size: 1.1rem;
            color: #1d4ed8;
            margin-top: 25px;
        }
        .warning-box {
            background-color: #fff7ed;
            border-left: 6px solid #f59e0b;
            padding: 15px 20px;
            border-radius: 10px;
            color: #92400e;
            font-size: 0.95rem;
            margin-top: 25px;
        }
        .info-box {
            background-color: #ecfdf5;
            border-left: 6px solid #10b981;
            padding: 15px 20px;
            border-radius: 10px;
            color: #065f46;
            font-size: 0.95rem;
            margin-top: 25px;
        }
        .btn-upgrade {
            background-color: #2563eb;
            color: white;
            border-radius: 10px;
            font-weight: 500;
            padding: 12px 28px;
            font-size: 1rem;
            transition: 0.3s;
        }
        .btn-upgrade:hover {
            background-color: #1e40af;
            transform: translateY(-2px);
        }
        .help-section {
            background: #f0f9ff;
            border: 1px solid #bfdbfe;
            border-radius: 10px;
            padding: 15px 20px;
            margin-top: 40px;
            text-align: center;
        }
        .help-section a {
            color: #1d4ed8;
            text-decoration: none;
            font-weight: 600;
        }
        .help-section a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center"><i class="fa fa-paw me-2 text-primary"></i>Upgrade Your Account to <span class="text-primary">Adopter</span></h2>
    <hr class="mb-4">

    <p class="lead">
        We're thrilled that you're enjoying <strong>PetAdopt</strong>! As a Guest, you can explore our adorable pets and learn about our mission.  
        But by upgrading to an <strong>Adopter Account</strong>, you become an active member of our caring community—able to adopt, contribute, and truly make a difference in a pet’s life. 🐶🐾
    </p>

    <h5 class="section-title">🌟 Why Become an Adopter?</h5>
    <ul>
        <li>Submit official adoption requests directly through our secure platform.</li>
        <li>Track the status of your requests and maintain a personal adoption history.</li>
        <li>Access detailed pet profiles and exclusive adoption events.</li>
        <li>Support pets in need through optional donations and sponsorships.</li>
        <li>Become part of a compassionate network of adopters and animal lovers.</li>
    </ul>

    <h5 class="section-title">📋 What You’ll Need Before Upgrading</h5>
    <ul>
        <li>Ensure you’re ready for the lifelong commitment and responsibility of a pet.</li>
        <li>Provide accurate contact information for verification and follow-ups.</li>
        <li>Understand our adoption process—our volunteers will guide you at every step.</li>
    </ul>

    <div class="info-box">
        <i class="fa fa-info-circle me-2"></i>
        <strong>Tip:</strong> After upgrading, you’ll see new features like <em>“My Requests”</em> and <em>“Donate”</em> in your dashboard. These tools help you manage your adoptions and contribute to animal welfare.
    </div>

    <div class="warning-box">
        <i class="fa fa-exclamation-triangle me-2"></i>
        <strong>Important:</strong> Once your account is upgraded to an Adopter:
        <ul class="mt-2">
            <li>You <strong>cannot switch back</strong> to a Guest account.</li>
            <li>If you no longer wish to continue, you may request <strong>account deletion</strong> through our support team.</li>
            <li>This change ensures authenticity and trust in our adoption network.</li>
        </ul>
    </div>

    <div class="text-center mt-5">
        <form method="POST" id="upgradeForm">
            <button type="button" id="upgradeBtn" class="btn btn-upgrade">
                <i class="fa fa-arrow-up me-2"></i> Upgrade My Account
            </button>
        </form>
    </div>

    <!-- Need Help Section -->
    <div class="help-section mt-4">
        <p><i class="fa fa-question-circle me-2 text-primary"></i>
        Still have questions or need help before upgrading?  
        <a href="contact.php"><i class="fa fa-envelope me-1"></i>Contact our Support Team</a></p>
    </div>
</div>

<script>
document.getElementById('upgradeBtn').addEventListener('click', function() {
    Swal.fire({
        title: 'Upgrade to Adopter?',
        html: "<p style='font-size:15px'>Once upgraded, you'll gain full adoption access and support options.<br><br><strong>This change is permanent</strong> but you can delete your account anytime if needed.</p>",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#2563eb',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, upgrade me!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('upgradeForm').submit();
        }
    });
});
</script>

</body>
</html>
