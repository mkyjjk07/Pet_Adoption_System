<?php
session_start();
include 'includes/config.php';

$id = intval($_GET['id']);
$message = "";

// Determine if admin or volunteer
if (isset($_SESSION['admin_id'])) {
    // Admin or Super Admin can access any pet
    $stmt = $conn->prepare("SELECT * FROM pets WHERE pet_id=?");
    $stmt->bind_param("i", $id);
} elseif (isset($_SESSION['user_id'])) {
    // Volunteer can access only their added pets
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT * FROM pets WHERE pet_id=? AND added_by_user_id=?");
    $stmt->bind_param("ii", $id, $user_id);
} else {
    die("Unauthorized access. Please log in.");
}

$stmt->execute();
$result = $stmt->get_result();
$pet = $result->fetch_assoc();

// 🔒 Restrict volunteers from editing adopted pets
if (isset($_SESSION['user_id']) && $pet['status'] === 'adopted') {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Access Restricted</title>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
        <script>
            Swal.fire({
                icon: 'warning',
                title: 'Not Allowed',
                text: 'This pet has already been adopted and cannot be updated.',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'dashboard.php';
            });
        </script>
    </body>
    </html>
    <?php
    exit;
}
if (!$pet) {
    die("Pet not found.");
}


// Handle update
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $type = $_POST['type'];
    $breed = $_POST['breed'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    $price = $_POST['price'];

    $old_image = $pet['image'];

    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];
        $target_path = "assets/images/pet-uploads/" . basename($image);

        if (move_uploaded_file($image_tmp, $target_path)) {
            $old_images_folder = "assets/images/old_images/";
            if (!is_dir($old_images_folder)) {
                mkdir($old_images_folder, 0777, true);
            }
            if (!empty($old_image) && file_exists("assets/images/pet-uploads/" . $old_image)) {
                rename("assets/images/pet-uploads/" . $old_image, $old_images_folder . $old_image);
            }
        }
    } else {
        $image = $old_image;
    }

    $update = $conn->prepare("UPDATE pets SET name=?, type=?, breed=?, age=?, gender=?, description=?, status=?, image=?, price=? WHERE pet_id=?");
    $update->bind_param("sssissssdi", $name, $type, $breed, $age, $gender, $description, $status, $image, $price, $id);

    if ($update->execute()) {
        $message = "✅ Pet updated successfully.";
        $dashboard_link = isset($_SESSION['admin_id']) ? "admin_dashboard.php" : "dashboard.php";
        header("refresh:2;url=$dashboard_link");
    } else {
        $message = "❌ Failed to update pet.";
    }
}

$dashboard_link = isset($_SESSION['admin_id']) ? "admin_dashboard.php" : "dashboard.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Pet</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="container mt-5">
<div class="card p-4 mx-auto" style="max-width: 700px;">
<h2 class="mb-4 text-center"><i class="fa fa-edit me-2"></i>Edit Pet</h2>

<?php if ($message): ?>
    <div class="alert alert-info"><?= $message ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
        <label>Pet Name:</label>
        <input type="text" name="name" value="<?= $pet['name'] ?>" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Type:</label>
        <input type="text" name="type" value="<?= $pet['type'] ?>" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Breed:</label>
        <input type="text" name="breed" value="<?= $pet['breed'] ?>" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Age:</label>
        <input type="number" name="age" value="<?= $pet['age'] ?>" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Gender:</label>
        <select name="gender" class="form-select" required>
            <option value="Male" <?= $pet['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
            <option value="Female" <?= $pet['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
        </select>
    </div>

    <div class="mb-3">
        <label>Price (₹):</label>
        <input type="number" name="price" value="<?= $pet['price'] ?>" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Description:</label>
        <textarea name="description" class="form-control" rows="3" required><?= $pet['description'] ?></textarea>
    </div>

    <div class="mb-3">
        <label>Status:</label>
        <select name="status" class="form-select">
            <option value="available" <?= $pet['status'] === 'available' ? 'selected' : '' ?>>Available</option>
            <option value="adopted" <?= $pet['status'] === 'adopted' ? 'selected' : '' ?>>Adopted</option>
            <option value="pending" <?= $pet['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
        </select>
    </div>

    <div class="mb-3">
        <label>Current Image:</label><br>
        <img src="assets/images/pet-uploads/<?= $pet['image'] ?>" width="120" class="mb-2 rounded">
        <input type="file" name="image" class="form-control">
    </div>

    <div class="d-flex justify-content-between">
        <button type="submit" class="btn btn-primary"><i class="fa fa-save me-1"></i> Update</button>
        <a href="<?= $dashboard_link ?>" class="btn btn-secondary"><i class="fa fa-arrow-left me-1"></i> Back</a>
    </div>
</form>
</div>
</body>
</html>
