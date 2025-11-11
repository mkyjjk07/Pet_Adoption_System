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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      padding: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #a18cd1, #fbc2eb);
      overflow-x: hidden;
      color: #333;
    }

    /* Navbar */
    nav.navbar {
      background: linear-gradient(90deg, #a18cd1, #fbc2eb);
      font-size: 2rem;
      box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }

    /* Hero Section */
    .hero {
      background: linear-gradient(135deg, rgba(147, 112, 219, 0.9), rgba(255, 182, 193, 0.9)), url('assets/images/banner-pets.jpg') center/cover no-repeat;
      color: #fff;
      padding: 120px 0;
      text-align: center;
      position: relative;
      overflow: hidden;
    }

    .hero h1 {
      font-size: 3.2rem;
      font-weight: 700;
      text-shadow: 0 2px 10px rgba(0,0,0,0.3);
      animation: fadeInDown 1s ease;
    }

    .hero p {
      font-size: 1.2rem;
      max-width: 750px;
      margin: 20px auto;
      line-height: 1.6;
      animation: fadeInUp 1.5s ease;
    }

    .hero .btn {
      background: #fff;
      color: #7a43d2;
      border-radius: 50px;
      padding: 12px 35px;
      font-weight: 600;
      border: none;
      transition: all 0.3s;
    }

    .hero .btn:hover {
      transform: scale(1.08);
      background: #7a43d2;
      color: #fff;
    }

    @keyframes fadeInDown {
      from {opacity:0; transform:translateY(-40px);}
      to {opacity:1; transform:translateY(0);}
    }

    @keyframes fadeInUp {
      from {opacity:0; transform:translateY(40px);}
      to {opacity:1; transform:translateY(0);}
    }

    /* About Section */
    .about-section {
      background: #fff;
      color: #444;
      padding: 80px 0;
      text-align: center;
    }

    .about-section h2 {
      font-weight: 700;
      color: #6c2bbf;
    }

    .about-section p {
      max-width: 850px;
      margin: 20px auto;
      line-height: 1.7;
      color: #555;
    }

    .features {
      margin-top: 50px;
    }

    .features .col-md-4 {
      transition: transform 0.3s;
    }

    .features .col-md-4:hover {
      transform: translateY(-10px);
    }

    .features i {
      font-size: 3em;
      background: linear-gradient(135deg, #a18cd1, #fbc2eb);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    /* Pet Cards */
    section#pets {
      padding: 70px 0;
      background: linear-gradient(180deg, #fefcff, #f7e9ff);
    }

    section#pets h2 {
      color: #6c2bbf;
      font-weight: 700;
    }

    .pet-grid {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 30px;
      margin-top: 40px;
    }

    .pet-card {
      position: relative;
      width: 300px;
      height: 350px;
      overflow: hidden;
      border-radius: 15px;
      box-shadow: 0 15px 35px rgba(0,0,0,0.15);
      transition: transform 0.4s ease;
      cursor: pointer;
      background: #fff;
    }

    .pet-card:hover {
      transform: scale(1.05);
    }

    .pet-card img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: 0.5s ease;
    }

    .pet-card:hover img {
      filter: brightness(60%) blur(1px);
      transform: scale(1.1);
    }

    .pet-info {
      position: absolute;
      bottom: -100%;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(180deg, rgba(255,255,255,0.2), rgba(147,112,219,0.95));
      color: #fff;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      opacity: 0;
      transition: all 0.4s ease;
      text-align: center;
      padding: 15px;
    }

    .pet-card:hover .pet-info {
      bottom: 0;
      opacity: 1;
    }

    .pet-info h5 {
      font-size: 1.3em;
      font-weight: 700;
    }

    .pet-info p {
      font-size: 0.9em;
      margin-top: 10px;
      line-height: 1.4;
    }

    .pet-info a {
      background: #fff;
      color: #6c2bbf;
      text-decoration: none;
      padding: 8px 18px;
      border-radius: 20px;
      font-weight: 600;
      margin-top: 10px;
      transition: 0.3s;
    }

    .pet-info a:hover {
      background: #6c2bbf;
      color: #fff;
    }

    /* CTA Section */
    .cta {
      background: linear-gradient(135deg, #a18cd1, #fbc2eb);
      color: #fff;
      text-align: center;
      padding: 80px 20px;
    }

    .cta h2 {
      font-size: 2.2rem;
      margin-bottom: 20px;
      font-weight: 700;
    }

    .cta a {
      background: #fff;
      color: #6c2bbf;
      font-weight: 600;
      padding: 10px 25px;
      border-radius: 30px;
      text-decoration: none;
      transition: 0.3s;
    }

    .cta a:hover {
      background: #6c2bbf;
      color: #fff;
    }

    /* Footer */
    footer {
      background: #6c2bbf;
      color: #fff;
      text-align: center;
      padding: 25px 0;
      font-size: 0.9rem;
    }

    @media (max-width:768px) {
      .hero h1 {font-size: 2.3rem;}
      .pet-card {width: 90%;}
    }
  </style>
</head>
<body>
  <?php include 'includes/navbar.php'; ?>

  <!-- Hero Section -->
  <section class="hero">
    <div class="container">
      <h1>Find Your Perfect Companion 💜</h1>
      <p>Welcome to <strong>PetNest</strong> — where loving hearts meet furry friends. Adopt, care, and create memories that last a lifetime.</p>
      <a href="about_us.php" class="btn mt-3">Know More About Us</a>
    </div>
  </section>

  <!-- About Section -->
  <section class="about-section">
    <div class="container">
      <h2>Why Choose PetNest?</h2>
      <p>At PetNest, we believe every pet deserves a home filled with love. Our mission is to connect kind-hearted adopters with adorable pets in need. Transparent adoptions, genuine care, and community support — that’s what defines us.</p>
      <div class="row features">
        <div class="col-md-4">
          <i class="fa-solid fa-heart"></i>
          <h5 class="mt-3">Loving Companions</h5>
          <p>Each pet has been given care, affection, and is ready to bring joy to your home.</p>
        </div>
        <div class="col-md-4">
          <i class="fa-solid fa-paw"></i>
          <h5 class="mt-3">Safe Adoptions</h5>
          <p>We ensure a safe, verified, and smooth adoption process for every family and animal.</p>
        </div>
        <div class="col-md-4">
          <i class="fa-solid fa-hands-holding"></i>
          <h5 class="mt-3">Community Support</h5>
          <p>Join a loving community of adopters and volunteers making a difference together.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Pet Section -->
  <section id="pets">
    <div class="container text-center">
      <h2>Meet Our Adorable Pets 🐾</h2>
      <p>Hover over to know more about each furry friend waiting for you!</p>
      <div class="pet-grid">
        <?php
        $query = "SELECT * FROM pets WHERE status='available' ORDER BY RAND() LIMIT 6";
        $result = $conn->query($query);
        if ($result->num_rows > 0) {
          while ($pet = $result->fetch_assoc()) {
            echo '
            <div class="pet-card">
              <img src="assets/images/pet-uploads/' . $pet['image'] . '" alt="' . htmlspecialchars($pet['name']) . '">
              <div class="pet-info">
                <h5>' . htmlspecialchars($pet['name']) . '</h5>
                <p>
                  <strong>Type:</strong> ' . htmlspecialchars($pet['type']) . '<br>
                  <strong>Breed:</strong> ' . htmlspecialchars($pet['breed']) . '<br>
                  <strong>Age:</strong> ' . htmlspecialchars($pet['age']) . ' yrs<br>
                  <strong>Fee:</strong> ₹' . htmlspecialchars($pet['price']) . '
                </p>
                <a href="adopt_pet.php?pet_id=' . $pet['pet_id'] . '">Adopt Now</a>
              </div>
            </div>';
          }
        } else {
          echo "<p>No pets available right now. Please check back soon!</p>";
        }
        ?>
      </div>
      <div class="text-center mt-5">
        <a href="view_pets.php" class="btn btn-outline-dark btn-lg">See More Pets</a>
      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="cta">
    <h2>Adopt. Love. Repeat. 🐶🐱</h2>
    <p>Be part of the change — give a home, receive unconditional love. Join our family of adopters today!</p>
    <a href="register.php">Join Now</a>
  </section>

  <footer>
    <p>&copy; <?php echo date('Y'); ?> PetNest | Made with 💜 for animals and humans alike.</p>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
