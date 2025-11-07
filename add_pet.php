<?php
session_start();
include 'includes/config.php';

if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_id'])) {
    header("Location: login.php?message=login_required");
    exit();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $type = $_POST['type'];
    $breed = $_POST['breed'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    $image = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $image_path = "assets/images/pet-uploads/" . basename($image);

    $added_by = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : $_SESSION['user_id'];

    if (move_uploaded_file($image_tmp, $image_path)) {
        $stmt = $conn->prepare("INSERT INTO pets (name, type, breed, age, gender, description, image, price, added_by_user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssisssdi", $name, $type, $breed, $age, $gender, $description, $image, $price, $added_by);

        if ($stmt->execute()) {
            $message = "✅ Pet added successfully!";
        } else {
            $message = "❌ Error adding pet.";
        }
    } else {
        $message = "❌ Failed to upload image.";
    }
}

$dashboard_link = isset($_SESSION['admin_id']) ? "admin_dashboard.php" : "dashboard.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add New Pet</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="card p-4 mx-auto" style="max-width: 700px;">
        <h2 class="mb-4 text-center"><i class="fa fa-paw me-2"></i>Add a New Pet</h2>

        <?php if ($message): ?>
            <div class="alert alert-info"><?= $message ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label>Pet Name:</label>
                <input type="text" name="name" class="form-control" placeholder="Enter pet name" required>
            </div>

            <div class="mb-3">
                <label>Type (Dog, Cat, etc.):</label>
                <input type="text" name="type" class="form-control" placeholder="Enter pet type" required>
            </div>

            <div class="mb-3">
                <label>Breed:</label>
                <input type="text" name="breed" class="form-control" placeholder="Enter breed" required>
            </div>

            <div class="mb-3">
                <label>Age:</label>
                <input type="number" name="age" class="form-control" placeholder="Enter age in years" required>
            </div>

            <div class="mb-3">
                <label>Gender:</label>
                <select name="gender" class="form-select" required>
                    <option value="">-- Select Gender --</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>

            <div class="mb-3">
                <label>Price (₹):</label>
                <input type="number" name="price" class="form-control" placeholder="Enter price" required>
            </div>

            <div class="mb-3">
                <label>Description:</label>
                <textarea name="description" class="form-control" rows="3" placeholder="Write a brief description" required></textarea>
            </div>

            <div class="mb-3">
                <label>Upload Image:</label>
                <input type="file" name="image" class="form-control" accept="image/*" required>
            </div>

            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary"><i class="fa fa-plus me-1"></i> Add Pet</button>
                <a href="<?= $dashboard_link ?>" class="btn btn-secondary"><i class="fa fa-arrow-left me-1"></i> Back</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
