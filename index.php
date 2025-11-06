<?php
session_start();
include 'includes/config.php';
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

 
    <section class="hero">
    <div class="container text-white">
        <h1 class="display-4 fw-bold">Adopt. Love. Repeat. 🐶🐱</h1>
        <p class="lead mt-3">Welcome to PetNest, your trusted pet adoption platform. Discover loving pets waiting for a forever home and bring happiness to your life today.</p>
        <a href="view_pets.php" class="btn btn-success btn-lg mt-3">Browse Pets for Adoption</a>
    </div>
    </section>


    <div class="container my-5">
    <h2 class="text-center mb-4">Meet Our Lovely Pets 🐾</h2>
    <p class="text-center mb-5">Each pet deserves a loving home. Explore our available pets and find your perfect companion today!</p>
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php
        $query = "SELECT * FROM pets WHERE status='available' ORDER BY RAND() LIMIT 5";
        $result = $conn->query($query);
        if ($result->num_rows > 0) {
            while ($pet = $result->fetch_assoc()) {
                echo '
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm">
                        <img src="assets/images/pet-uploads/' . $pet['image'] . '" class="card-img-top" alt="' . htmlspecialchars($pet['name']) . '">
                        <div class="card-body">
                            <h5 class="card-title">' . htmlspecialchars($pet['name']) . '</h5>
                            <p class="card-text">
                                <strong>Type:</strong> ' . htmlspecialchars($pet['type']) . '<br>
                                <strong>Breed:</strong> ' . htmlspecialchars($pet['breed']) . '<br>
                                <strong>Age:</strong> ' . htmlspecialchars($pet['age']) . ' yrs<br>
                                <strong>Gender:</strong> ' . htmlspecialchars($pet['gender']) . '<br>
                                <strong>Description:</strong> ' . htmlspecialchars($pet['description']) . '<br>
                                <strong>Adoption Fee:</strong> ₹' . htmlspecialchars($pet['price']) . '
                            </p>
                            <a href="adopt_pet.php?pet_id=' . $pet['pet_id'] . '" class="btn btn-primary w-100">Adopt Now</a>
                        </div>
                    </div>
                </div>';
         }
        } else {
            echo "<p class='text-center fw-bold'>No pets are available for adoption at the moment. Please check back soon!</p>";
        }
        ?>
    </div>
        <div class="text-center mt-4">
        <a href="view_pets.php" class="btn btn-outline-success btn-lg">See More Pets</a>
        </div>
    </div>

 <?php include 'includes/footer.php'; ?>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
