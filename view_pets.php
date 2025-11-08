<?php  
session_start();
include 'includes/config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>All Pets | PetNest</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<div class="container my-5">
  <h2 class="text-center mb-4">Browse Pets</h2>

  <form id="filterForm" class="row g-3 mb-4">
    <div class="col-md-3">
      <select name="type" class="form-select">
        <option value="">All Types</option>
        <option value="Dog">Dog</option>
        <option value="Cat">Cat</option>
        <option value="Bird">Bird</option>
        <option value="Rabbit">Rabbit</option>
      </select>
    </div>
    <div class="col-md-3">
      <input type="text" name="breed" class="form-control" placeholder="Breed">
    </div>
    <div class="col-md-3">
      <select name="gender" class="form-select">
        <option value="">Any Gender</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
      </select>
    </div>
    <div class="col-md-3">
      <input type="text" name="name" class="form-control" placeholder="Pet name">
    </div>
    <div class="col-md-2">
      <input type="number" name="age" class="form-control" placeholder="Max Age">
    </div>
    <div class="col-md-2">
      <input type="number" name="min_price" class="form-control" placeholder="Min Price">
    </div>
    <div class="col-md-2">
      <input type="number" name="max_price" class="form-control" placeholder="Max Price">
    </div>
  </form>

  <div class="row row-cols-1 row-cols-md-3 g-4" id="pets-container">
  </div>
</div>

<div class="modal fade" id="petModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="petModalLabel">Pet Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <img id="petImage" src="" class="img-fluid rounded shadow" alt="Pet Image">
          </div>
          <div class="col-md-6">
            <h3 id="petName"></h3>
            <p><strong>Type:</strong> <span id="petType"></span></p>
            <p><strong>Breed:</strong> <span id="petBreed"></span></p>
            <p><strong>Age:</strong> <span id="petAge"></span> yrs</p>
            <p><strong>Gender:</strong> <span id="petGender"></span></p>
            <p><strong>Price:</strong> ₹<span id="petPrice"></span></p>
            <p><strong>Description:</strong><br><span id="petDescription"></span></p>
            <a href="#" id="adoptBtn" class="btn btn-success btn-lg mt-3">Adopt Now</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const userRole = "<?php echo $_SESSION['role'] ?? 'guest'; ?>"; // get user role

function fetchPets() {
    const formData = new FormData(document.querySelector("#filterForm"));
    const params = new URLSearchParams(formData).toString();

    fetch("ajax_fetch_pets.php?" + params)
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById("pets-container");
            container.innerHTML = "";
            if (data.length > 0) {
                data.forEach(pet => {
                    container.innerHTML += `
                        <div class="col">
                          <div class="card h-100 border-0 shadow-sm">
                            <img src="assets/images/pet-uploads/${pet.image}" class="card-img-top" alt="Pet Image">
                            <div class="card-body">
                              <h5 class="card-title">${pet.name}</h5>
                              <p class="card-text">
                                <strong>Type:</strong> ${pet.type}<br>
                                <strong>Breed:</strong> ${pet.breed}<br>
                                <strong>Age:</strong> ${pet.age} yrs<br>
                                <strong>Gender:</strong> ${pet.gender}<br>
                                <strong>Price:</strong> ₹${pet.price}<br>
                                <small class="text-muted">${pet.description ? pet.description.substring(0, 70) + "..." : ""}</small>
                              </p>
                              <button class="btn btn-outline-info w-100" onclick='showPetDetails(${JSON.stringify(pet)})'>View Details</button>
                            </div>
                          </div>
                        </div>`;
                });
            } else {
                container.innerHTML = "<p class='text-center'>No pets found matching your filters.</p>";
            }
        })
        .catch(err => {
            document.getElementById("pets-container").innerHTML = "<p class='text-danger'>Error loading pets.</p>";
            console.error(err);
        });
}

document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("#filterForm");

    fetchPets();

    form.querySelectorAll("select, input").forEach(el => {
        el.addEventListener("input", fetchPets);
        el.addEventListener("change", fetchPets);
    });
});

function showPetDetails(pet) {
    document.getElementById("petImage").src = "assets/images/pet-uploads/" + pet.image;
    document.getElementById("petName").textContent = pet.name;
    document.getElementById("petType").textContent = pet.type;
    document.getElementById("petBreed").textContent = pet.breed;
    document.getElementById("petAge").textContent = pet.age;
    document.getElementById("petGender").textContent = pet.gender;
    document.getElementById("petPrice").textContent = pet.price;
    document.getElementById("petDescription").textContent = pet.description;

    const adoptBtn = document.getElementById("adoptBtn");
    
    if (userRole === "guest") {
    adoptBtn.href = "#";
    adoptBtn.onclick = function(e){
        e.preventDefault();
        Swal.fire({
            icon: 'info',
            title: 'Upgrade Required',
            text: 'You need to upgrade to Adopter to adopt a pet!',
            confirmButtonText: 'OK'
        });
    };
    } else if (pet.already_requested === 1) {
        adoptBtn.href = "#";
        adoptBtn.textContent = "Request Pending";
        adoptBtn.classList.remove("btn-success");
        adoptBtn.classList.add("btn-secondary");
        adoptBtn.disabled = true;
    } else {
        adoptBtn.href = "adopt_pet.php?pet_id=" + pet.pet_id;
        adoptBtn.textContent = "Adopt Now";
        adoptBtn.classList.remove("btn-secondary");
        adoptBtn.classList.add("btn-success");
        adoptBtn.disabled = false;
        adoptBtn.onclick = null;
    }

    var petModal = new bootstrap.Modal(document.getElementById('petModal'));
    petModal.show();
}
</script>
</body>
</html>
