<?php
session_start();
include 'includes/config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About Us | PetAdopt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8fafc;
            color: #333;
        }
        .hero {
            background: linear-gradient(rgba(37,99,235,0.8), rgba(37,99,235,0.8)), url('assets/images/about-banner.jpg') center/cover no-repeat;
            color: white;
            padding: 100px 20px;
            text-align: center;
        }
        .hero h1 {
            font-weight: 700;
            font-size: 2.8rem;
        }
        .section {
            padding: 60px 20px;
        }
        .section h2 {
            color: #1e3a8a;
            font-weight: 600;
            margin-bottom: 25px;
        }
        .section p {
            line-height: 1.7;
            font-size: 1.05rem;
        }
        .how-it-works .step {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            padding: 25px;
            text-align: center;
            transition: 0.3s;
        }
        .how-it-works .step:hover {
            transform: translateY(-5px);
        }
        .how-it-works i {
            font-size: 2rem;
            color: #2563eb;
            margin-bottom: 15px;
        }
        .resources a {
            text-decoration: none;
            color: #2563eb;
            font-weight: 500;
        }
        .resources a:hover {
            text-decoration: underline;
        }
        .contact-info i {
            color: #2563eb;
            margin-right: 10px;
        }
    </style>
</head>
<body>

<?php include 'includes/header.php'; ?>

<!-- Hero Section -->
<section class="hero">
    <h1>About PetAdopt</h1>
    <p class="mt-3 lead">Connecting loving homes with pets in need — because every paw deserves care and comfort.</p>
</section>

<!-- Mission & Vision -->
<section class="section container">
    <h2>Our Mission & Vision</h2>
    <p>
        At <strong>PetAdopt</strong>, our mission is to bridge the gap between stray, abandoned, or sheltered pets and the kind-hearted individuals
        ready to give them a home. We believe that adoption is not just a process — it’s a promise of love, responsibility, and care.
    </p>
    <p>
        Our vision is to create a world where every pet finds a forever home, and every adopter finds unconditional love. 
        We aim to make the pet adoption journey simple, transparent, and emotionally fulfilling through digital empowerment.
    </p>
</section>

<!-- How It Works -->
<section class="section how-it-works bg-light">
    <div class="container text-center">
        <h2>How It Works</h2>
        <div class="row g-4 mt-4">
            <div class="col-md-3">
                <div class="step">
                    <i class="fa fa-search"></i>
                    <h5>1. Browse Pets</h5>
                    <p>Explore available pets by type, breed, age, or gender. Every profile shares details, photos, and personality insights.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="step">
                    <i class="fa fa-heart"></i>
                    <h5>2. Send Adoption Request</h5>
                    <p>Found a furry friend you love? Submit your request easily through our platform to express your interest.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="step">
                    <i class="fa fa-comments"></i>
                    <h5>3. Connect & Verify</h5>
                    <p>Our volunteer or admin team reviews the request, connects you, and ensures every adoption is safe and genuine.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="step">
                    <i class="fa fa-home"></i>
                    <h5>4. Bring Them Home</h5>
                    <p>Once approved, you can welcome your new companion home and start your journey together with love.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Support & Process Info -->
<section class="section container">
    <h2>Support, Care & Adoption Process</h2>
    <p>
        PetAdopt not only connects adopters with pets — we stand by you throughout the adoption process. 
        From learning about pet needs to ensuring smooth communication with shelters and volunteers, 
        our system simplifies everything. 
    </p>
    <p>
        You can donate to help animals in need, volunteer with local shelters, or simply spread awareness about responsible adoption. 
        Together, we can build a compassionate community that supports animal welfare every day.
    </p>
</section>

<!-- External Blogs -->
<section class="section bg-light resources">
    <div class="container">
        <h2>Read More About Pet Adoptions</h2>
        <p class="mb-4">Learn more from trusted sources and inspiring stories across the web:</p>
        <ul>
            <li><a href="https://www.petfinder.com/adopt-or-get-involved/adopting-pets/" target="_blank">Petfinder – Adopting a Pet</a></li>
            <li><a href="https://theshelterpetproject.org/" target="_blank">The Shelter Pet Project – Why Adoption Matters</a></li>
            <li><a href="https://www.aspca.org/adopt-pet/adoption-tips" target="_blank">ASPCA – Adoption Tips and Resources</a></li>
            <li><a href="https://iadopt.in/blogs/" target="_blank">iAdopt – Adoption Stories & Blogs</a></li>
        </ul>
    </div>
</section>

<!-- Contact & Social Links -->
<section class="section container contact-info text-center">
    <h2>Contact & Stay Connected</h2>
    <p>
        Have questions, feedback, or collaboration ideas? We’d love to hear from you!  
        Reach out to us through any of the following:
    </p>
    <p class="mt-4">
        <i class="fa fa-envelope"></i> <a href="mailto:contact@petadopt.com">contact@petadopt.com</a><br>
        <i class="fa fa-phone"></i> +91 98765 43210
    </p>
    <div class="mt-3">
        <a href="#" class="me-3"><i class="fab fa-facebook fa-lg"></i></a>
        <a href="#" class="me-3"><i class="fab fa-instagram fa-lg"></i></a>
        <a href="#"><i class="fab fa-twitter fa-lg"></i></a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

</body>
</html>
