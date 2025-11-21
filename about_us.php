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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
    /* GLOBAL DESIGN */
    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #fdfbff, #f3e8ff);
        color: #333;
        overflow-x: hidden;
    }

    nav.navbar {
      background: linear-gradient(90deg, #a18cd1, #fbc2eb);
      font-size: 2rem;
      box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }
    
    /* Soft Fade Animations */
    @keyframes fadeUp {
        from {opacity: 0; transform: translateY(40px);}
        to {opacity: 1; transform: translateY(0);}
    }
    
    @keyframes fadeDown {
        from {opacity: 0; transform: translateY(-40px);}
        to {opacity: 1; transform: translateY(0);}
    }
    
    @keyframes fadeScale {
        from {opacity: 0; transform: scale(0.95);}
        to {opacity: 1; transform: scale(1);}
    }
    
    /* HERO SECTION — same style as index page */
    .hero {
        background: linear-gradient(135deg, rgba(147, 112, 219, 0.75), rgba(255, 182, 193, 0.75)),
        url('assets/images/about-banner.jpg') center/cover no-repeat;
        color: white;
        padding: 120px 25px;
        text-align: center;
        animation: fadeDown 1s ease-out;
    }
    
    .hero h1 {
        font-size: clamp(2.5rem, 5vw, 3.5rem);
        font-weight: 800;
        text-shadow: 0px 4px 10px rgba(0,0,0,0.2);
    }
    
    .hero p {
        font-size: clamp(1.1rem, 2.5vw, 1.4rem);
        opacity: 0.95;
        max-width: 750px;
        margin: auto;
    }
    
    /* ALL CONTENT SECTIONS */
    .section {
        padding: 80px 25px;
        animation: fadeUp 1.1s ease;
    }
    
    .section h2 {
        color: #7a43d2;
        font-weight: 700;
        margin-bottom: 25px;
        text-align: center;
        font-size: clamp(1.6rem, 3vw, 2.2rem);
    }
    
    .section p {
        font-size: clamp(1.1rem, 2.2vw, 1.25rem);
        line-height: 1.8;
        color: #4c4c4c;
        max-width: 900px;
        margin: auto;
    }
    
    /* HOW IT WORKS — Animated Glass Cards */
    .how-it-works .step {
        background: rgba(255, 255, 255, 0.65);
        backdrop-filter: blur(8px);
        border-radius: 18px;
        padding: 30px;
        box-shadow: 0 15px 35px rgba(123, 58, 255, 0.15);
        transition: 0.35s ease;
        animation: fadeScale 1.3s ease;
    }
    
    .how-it-works .step:hover {
        transform: translateY(-10px) scale(1.05);
        box-shadow: 0 20px 45px rgba(123, 58, 255, 0.22);
    }
    
    /* Gradient Icons */
    .how-it-works i {
        font-size: 2.8rem;
        background: linear-gradient(135deg, #a18cd1, #fbc2eb);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    
    /* RESOURCE LINKS */
    .resources a {
        color: #7a43d2;
        font-weight: 600;
        transition: 0.3s ease;
    }
    
    .resources a:hover {
        color: #9b4bff;
        padding-left: 5px;
    }
    
    /* CONTACT SECTION */
    .contact-info i {
        color: #7a43d2;
        margin-right: 10px;
    }
    
    .contact-info a {
        color: #7a43d2;
        font-weight: 500;
    }
    
    /* SOCIAL ICON HOVER */
    .contact-info .fab {
        color: #7a43d2;
        transition: 0.3s ease;
        font-size: 1.5rem;
    }
    
    .contact-info .fab:hover {
        transform: scale(1.25);
        color: #9b4bff;
    }
    
    /* Fade animation for list items */
    .resources li,
    .contact-info p {
        animation: fadeUp 1.2s ease;
    }
    
    /* MOBILE RESPONSIVE */
    @media (max-width: 768px) {
        .hero h1 { font-size: 2.3rem; }
        .hero p { font-size: 1.1rem; }
    }
    @media (max-width: 768px) {
        .step p {
            font-size: 1rem;
        }
        .step h5 {
            font-size: 1.1rem;
        }
    }   
    </style>
</head>
<body>
<?php include 'includes/navbar.php'; ?>



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
        <p>
            Follow us on social media for updates, heartwarming stories, and more!
        </p>
        <a href="#" class="me-3"><i class="fab fa-facebook fa-lg"></i></a>
        <a href="#" class="me-3"><i class="fab fa-instagram fa-lg"></i></a>
        <a href="#"><i class="fab fa-twitter fa-lg"></i></a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

</body>
</html>
