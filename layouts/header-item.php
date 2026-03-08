<?php

require_once __DIR__ . "/../includes/session.php";

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/config.php";
require_once __DIR__ . '/../includes/cart_functions.php';
$cartItems = getCartItems($pdo);
$cartCount = array_sum(array_column($cartItems, 'quantity'));

require_auth();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="title" content="Gymfit - Aerobics"> <!-- ADD THEME TITLE HERE -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="format-detection" content="telephone=no">
    <meta name=theme-color content="#FFFFFF"> <!-- ADD THEME COLOR HERE -->
    <meta name="description"
        content="You are welcome to visit our center where every person is treated with high attention">
    <!-- ADD THEME DESCRIPTION HERE -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="theme_url"> <!-- ADD THEME URL HERE -->
    <meta property="og:title" content="Gymfit - Aerobics"> <!-- ADD THEME TITLE HERE -->
    <meta property="og:description"
        content="You are welcome to visit our center where every person is treated with high attention">
    <!-- ADD THEME DESCRIPTION HERE -->
    <meta property="og:image" content="assets/images/thumbnail.webp">
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="theme_url"> <!-- ADD THEME URL HERE -->
    <meta property="twitter:title" content="Gymfit - Aerobics"> <!-- ADD THEME TITLE HERE -->
    <meta property="twitter:description"
        content="You are welcome to visit our center where every person is treated with high attention">
    <!-- ADD THEME DESCRIPTION HERE -->
    <meta property="twitter:image" content="assets/images/thumbnail.webp"> <!-- ADD THUMBNAIL PATH HERE -->

    <title>Athletes Gym | Qatar</title>

    <link rel="icon" type="image/x-icon" href="assets/images/favicon.png">

    <link rel="stylesheet" href="<?= BASE_URL ?>assets/vendors/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/vendors/css/swiper.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/vendors/css/animate.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/app.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>auth/auth.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>account/profile.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script>
        const hasSupport = 'loading' in HTMLImageElement.prototype;
        document.documentElement.className = hasSupport ? 'pass' : 'fail';
    </script>

<style>
    .gallery img {
      width: 100%;
      height: auto;
      border-radius: 8px;
    }
    #mini-cart {
        position: relative;
    }
    #cart-dropdown ul {
        max-height: 200px;
        overflow-y: auto;
    }
    .cart-plus, .cart-minus {
    background: #eee;
    border: none;
    width: 30px;
    height: 30px;
    font-weight: bold;
    cursor: pointer;
    border-radius: 4px;
  }
  .cart-plus:hover, .cart-minus:hover {
    background: #ddd;
  }
  </style>
</head>

<body>
    <!-- Header Start -->
    <!-- <video autoplay="" muted="" loop="" style="object-fit: fill;" height="70%" width="100%">
            <source src="assets/athletes/commercial BW 1.mp4" type="video/mp4">
        </video> -->
    <!-- <video autoplay muted loop playsinline style="object-fit: fill;"     height="70%" width="100%">
          <source src="assets/athletes/commercial BW 1.mp4" type="video/mp4">
     </video> -->
    <!-- <div class="videoMain">
         <iframe 
            width="100%" 
         height="760" 
         class="videoFrame" 
         src="https://www.youtube.com/embed/g2qR_8zgwpw?autoplay=1&mute=1&loop=1&       playlist=g2qR_8zgwpw&controls=0&modestbranding=1&rel=0&showinfo=0" 
         title="YouTube video player" 
         frameborder="0" 
         allow="autoplay; encrypted-media;" 
         allowfullscreen>
        </iframe>

    </div> -->
   <!-- <div id="player" style="width: 100%; height: 760px;"></div> -->
        
    <header>
        <div class="container-fluid">
           <nav class="navbar navbar-expand-xl">
    <a class="navbar-brand p-0" href="index.php"> 
        <img src="<?= BASE_URL ?>assets/images/logo/logo-white.png" width="300px" alt="Logo"> 
    </a>
    <button class="menu-btn collapsed" type="button">
        <span class="hamburger">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav navbar-nav-scroll tabActive">
            <li class="nav-item">
                <a class="nav-link" href="<?= BASE_URL ?>index.php">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= BASE_URL ?>about-us.php">About</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= BASE_URL ?>shop.php">Shop</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= BASE_URL ?>contact.php">Contact</a>
            </li>
            <li class="nav-item nav-search" style="position: relative;">
                <form action="<?= BASE_URL ?>shop.php" method="GET" style="margin: 0;">
                    <input type="text" name="search" placeholder="Search products..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
            </li>
            <li class="nav-item nav-cart">
                <a href="<?= BASE_URL ?>cart.php">
                    <i class="fas fa-shopping-cart cart-icon"></i>
                    <span>Cart (<?= isset($_SESSION['cart_session_id']) ? count(getCartItems($pdo)) : 0 ?>)</span>
                </a>
            </li>
            <li class="nav-item">
            <?php if (!empty($_SESSION['user_id'])): ?>
                <a class="nav-link" href="<?= BASE_URL ?>account/profile.php">My Profile</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= BASE_URL ?>auth/logout.php">Logout</a>
            <?php else: ?>
                <a class="nav-link" href="<?= BASE_URL ?>auth/login.php">Sign In/Register</a>
            <?php endif; ?>
            </li>
        </ul>
        <!-- <div id="mini-cart">
        <button id="toggle-cart" class="btn btn-outline-light position-relative">
            🛒
            <span id="cart-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                0
            </span>
        </button>
        <div id="cart-dropdown" style="display: none; position: absolute; right: 10px; top: 60px; background: #fff; border: 1px solid #ccc; padding: 10px; z-index: 1000; width: 250px;">
            <ul id="cart-items" class="list-unstyled mb-2"></ul>
            <p>Total: <span id="cart-total">0.00</span> QAR</p>
            <button id="clear-cart" class="btn btn-sm btn-danger">Clear Cart</button>
        </div>
    </div> -->
    </div>
</nav>

        </div>
    </header>
    <!-- Header End -->
