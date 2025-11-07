<?php
session_start();
include 'includes/config.php';

$successMsg = '';
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name  = trim($_POST['last_name']);
    $email      = trim($_POST['email']);
    $message    = trim($_POST['message']);
    $country    = $_POST['country'] ?? 'Unknown';

    // Validate basic inputs
    if ($first_name && $last_name && $email && $message) {
        // Create a formatted string
        $entry  = "---------------------------\n";
        $entry .= "Date: " . date('Y-m-d H:i:s') . "\n";
        $entry .= "Name: $first_name $last_name\n";
        $entry .= "Email: $email\n";
        $entry .= "Country: $country\n";
        $entry .= "Message:\n$message\n";
        $entry .= "---------------------------\n\n";

        // Save to file
        $file = __DIR__ . '/contact_messages.txt';
        if (file_put_contents($file, $entry, FILE_APPEND | LOCK_EX)) {
            $successMsg = "Thank you! Your message has been received.";
        } else {
            $errorMsg = "Oops! Something went wrong. Please try again.";
        }
    } else {
        $errorMsg = "Please fill all required fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PetNest | Adopt a Pet</title>
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>

    <?php include 'includes/navbar.php'; ?>

    <div class="container my-5">
        <div class="row">
            <!-- Contact Form -->
            <div class="col-md-6 mb-4">
                <h2 class="mb-3">Get in Touch with Us</h2>
                <p>Have questions about adopting a pet?   OR want to give feedback, feel free to fill out the form below and our team will get back to you shortly.</p>
                <form method="POST" class="mt-3">
                    <?php if($successMsg): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($successMsg); ?></div>
                    <?php endif; ?>
                    <?php if($errorMsg): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($errorMsg); ?></div>
                    <?php endif; ?>

                    <div class="row mb-2">
                        <div class="col">
                            <input type="text" class="form-control" name="first_name" placeholder="First Name" required>
                        </div>
                        <div class="col">
                            <input type="text" class="form-control" name="last_name" placeholder="Last Name" required>
                        </div>
                    </div>
                    <input type="email" class="form-control mb-2" name="email" placeholder="Email Address" required>
                    <textarea class="form-control mb-2" name="message" placeholder="Your Message" rows="4" required></textarea>
                    <select class="form-select mb-2" name="country" required>
                        <option value="" disabled selected>Select Country</option>
                        <option>United States</option>
                        <option>India</option>
                        <option>United Kingdom</option>
                        <option>Canada</option>
                        <option>Other</option>
                    </select>
                    <button class="btn btn-primary w-100" type="submit">Send Message</button>
                </form>
                <p class="mt-2 text-muted" style="font-size: 0.9rem;">
                    By submitting, you consent to PetNest collecting and storing your information in accordance with our <a href="#">Privacy Policy</a>.
                </p>
            </div>

            <!-- Support Info -->
            <div class="col-md-6">
                <h2 class="mb-3">Support & Assistance</h2>
                
                <h5 class="mt-4">Talk with Us</h5>
                <p>Give us a call at <strong>+91-123-456-7890</strong><br>Available Mon-Fri, 9am-6pm IST</p>

                <h5 class="mt-4">Visit Us</h5>
                <p>PetNest HQ, 123 Pet Street, Happy Town, India</p>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>