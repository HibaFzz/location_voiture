<?php

// Check if a session is already active before starting a new session
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start a session if not already active
}

// Check if logout is needed before any output
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    AuthController::logout(); // Call the logout method
}

$currentUser = AuthController::getCurrentUser();

// Function to check if the current page is the active one
function isActive($page) {
    return basename($_SERVER['PHP_SELF']) === $page ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Page Title</title>
    <!-- Include your CSS and JS files -->
    <link rel="stylesheet" href="css/style.css">
    <style>

        .nav-item.active .nav-link {
            font: #f96d00; 
            color: white;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-light" id="ftco-navbar">
  <div class="container">
    <a class="navbar-brand" href="index.php">Car<span>Book</span></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav" aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="oi oi-menu"></span> Menu
    </button>

    <div class="collapse navbar-collapse" id="ftco-nav">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item <?= isActive('about.php'); ?>"><a href="about.php" class="nav-link">About</a></li>
        
        <li class="nav-item <?= isActive('list_cars.php'); ?>"><a href="list_cars.php" class="nav-link">Cars</a></li>
        <?php if ($currentUser['role'] === 'agent'): ?>
          <li class="nav-item <?= isActive('returned_cars.php'); ?>"><a href="returned_cars.php" class="nav-link">Returned Cars</a></li>
          <li class="nav-item <?= isActive('list_contracts.php'); ?>"><a href="list_contracts.php" class="nav-link">Contracts</a></li>
        
          <li class="nav-item <?= isActive('user_list.php'); ?>"><a href="user_list.php" class="nav-link">Users</a></li>
        <?php endif; ?>
        
        <li class="nav-item <?= isActive('contact.php'); ?>"><a href="contact.php" class="nav-link">Contact</a></li>

        <!-- User Profile Dropdown -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <?php if ($currentUser): ?>
              <!-- Display user image from uploads folder -->
              <?php if (!empty($currentUser['photo'])): ?>
                <img src="<?=$currentUser['photo']; ?>" alt="User Image" class="rounded-circle" style="width: 30px; height: 30px;">
              <?php else: ?>
                <img src="uploads/default.png" alt="Default User Image" class="rounded-circle" style="width: 30px; height: 30px;">
              <?php endif; ?>
              <?php echo $currentUser['prenom'] . ' ' . $currentUser['nom']; ?> <!-- Display first and last name -->
            <?php else: ?>
              Guest
            <?php endif; ?>
          </a>
          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
            <a class="dropdown-item" href="Historiques.php">History</a>
            <a class="dropdown-item" href="MyContract.php">My Contract</a>
            <a class="dropdown-item" href="profile.php">Profile</a>
            <a class="dropdown-item" href="logout.php">Logout</a>
          </div>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="hero-wrap ftco-degree-bg" style="background-image: url('images/bg_1.jpg');" data-stellar-background-ratio="0.5">
  <div class="overlay"></div>
  <div class="container">
    <div class="row no-gutters slider-text justify-content-start align-items-center justify-content-center">
      <div class="col-lg-8 ftco-animate">
        <div class="text w-100 text-center mb-md-5 pb-md-5">
          <h1 class="mb-4">Fast &amp; Easy Way To Rent A Car</h1>
          <p style="font-size: 18px;">A small river named Duden flows by their place and supplies it with the necessary regelialia. It is a paradisematic country, in which roasted parts</p>
          <a href="https://vimeo.com/45830194" class="icon-wrap popup-vimeo d-flex align-items-center mt-4 justify-content-center">
            <div class="icon d-flex align-items-center justify-content-center">
              <span class="ion-ios-play"></span>
            </div>
            <div class="heading-title ml-5">
              <span>Easy steps for renting a car</span>
            </div>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="js/jquery.min.js"></script>
<script src="js/jquery-migrate-3.0.1.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.easing.1.3.js"></script>
<script src="js/jquery.waypoints.min.js"></script>
<script src="js/jquery.stellar.min.js"></script>
<script src="js/owl.carousel.min.js"></script>
<script src="js/jquery.magnific-popup.min.js"></script>
<script src="js/aos.js"></script>
<script src="js/jquery.animateNumber.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="js/jquery.timepicker.min.js"></script>
<script src="js/scrollax.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBVWaKrjvy3MaE7SQ74_uJiULgl1JY0H2s&sensor=false"></script>
<script src="js/google-map.js"></script>
<script src="js/main.js"></script>
    
</body>
</html>
