<!-- PHP Logic (Authentication, Session, etc.) -->
<?php 
  /*
    index.php - CvSU Marketplace Home Page
    Visible to both guests and logged-in users.
  */

  session_start();
  
  // Test Code for user log in (Remove after adding real log in authentication and db)
  //unset($_SESSION['user']); // Test for guest navigation
  $_SESSION['user'] = ["name" => "Spenzer", "id" => 17]; // Test for logged-in user navigation
  $user = $_SESSION['user'] ?? null; 
?>

<!-- PHP UI/UX Logic -->
<?php 
  $activePage = "home"; 
  include "includes/header.php"; 
?>

<!-- PHP Database Query -->
<?php
  // Mock featured listings (replace with real DB query later)
  $featured = [
    ['title' => 'Ethics Book', 'price' => 400, 'category' => 'Books', 'seller' => 'Gypsy', 'location' => 'CAS', 'img' => $imgNotAvailableIcon, 'id' => 124],
    ['title' => 'CvSU ID Lace','price' => 80, 'category' => 'Supplies', 'seller' => 'Charlie',  'location' => 'CEIT', 'img' => $imgNotAvailableIcon, 'id' => 155],
    ['title' => 'Iphone 67 Pro Max Fully Paid', 'price' => 6700, 'category' => 'Electronics', 'seller' => 'Jedhorse', 'location' => 'DIT','img' => $imgNotAvailableIcon, 'id' => 267],
    ['title' => 'Lomi', 'price' => 99, 'category' => 'Food', 'seller' => 'Nuggets', 'location' => 'DIET', 'img' => $imgNotAvailableIcon, 'id' => 655],
    ['title' => 'CvSU Uniform M (Large)', 'price' => 500, 'category' => 'Clothing', 'seller' => 'Blessie', 'location' => 'CAS','img' => $imgNotAvailableIcon, 'id' => 430],
    ['title' => 'Laundry Services', 'price' => 350, 'category' => 'Services', 'seller' => 'Moglie', 'location' => 'Bancod',  'img' => $imgNotAvailableIcon, 'id' => 886],
    ['title' => 'Understanding The Self Book', 'price' => 450, 'category' => 'Books', 'seller' => 'Happy', 'location' => 'Oval',  'img' => $imgNotAvailableIcon, 'id' => 987],
    ['title' => 'Keychain', 'price' => 25, 'category' => 'Other', 'seller' => 'Pumpkin', 'location' => 'Grand Stand',  'img' => $imgNotAvailableIcon, 'id' => 101]
  ];
    
  $categories = [
    ['label' => 'Books', 'icon' => $bookIcon, 'count' => 142],
    ['label' => 'Electronics', 'icon' => $electronicsIcon, 'count' => 87],
    ['label' => 'Supplies', 'icon' => $suppliesIcon,  'count' => 65],
    ['label' => 'Clothing', 'icon' => $clothesIcon, 'count' => 53],
    ['label' => 'Food', 'icon' => $foodIcon, 'count' => 39],
    ['label' => 'Services', 'icon' => $servicesIcon, 'count' => 28],
  ];
?>

<!-- Hero Section -->
<section class="hero">
  <h1>CvSU Student Marketplace</h1>
  <p>Buy and sell within the Cavite State University community - safe, easy, and free.</p>
 
  <!-- Show different CTA depending on login state -->
  <?php if ($user): ?>
    <!-- Search bar -->
    <form class="hero-search" action="browse.php" method="GET">
      <input type="text" name="q" placeholder="Search for books, gadgets, supplies…" />
      <button type="submit">Search</button>
    </form>

    <div class="hero-logged-in">
      Welcome back, <strong><?= htmlspecialchars($user['name']) ?></strong>! |
      <a href="dashboard.php" style="color: #C0B87A; font-weight: 600;">Go to Dashboard</a>
    </div>
  <?php else: ?>
    <div class="hero-actions">
      <a href="signup.php" class="btn-hero-primary">Create Free Account</a>
      <a href="#featured-listings-part" class="btn-hero-secondary">Browse Listings</a>
    </div>
  <?php endif; ?>
</section>
 
<!-- Browse by Categories Section -->
<div class="home-section-full">
  <div class="inner">
    <div class="section-heading">
      <h2>Browse by Category</h2>
      <a <?= ($user) ? 'href="browse.php"' : 'href="login.php"' ?>>View all ></a>
    </div>
    <div class="categories-grid">
      <?php foreach ($categories as $cat): ?>
        <a href="<?= ($user) ? 'browse.php?category=' . urlencode($cat['label']) : 'login.php' ?>" class="category-card">
          <div class="category-icon"><?= $cat['icon'] ?></div>
          <div class="category-label"><?= htmlspecialchars($cat['label']) ?></div>
          <div class="category-count"><?= htmlspecialchars($cat['count']) ?> listings</div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</div>
 
<!-- Featured Listings Section -->
<div class="home-section" id="featured-listings-part">
  <div class="section-heading">
    <h2>Featured Listings</h2>
    <a href="browse.php">View all ></a>
  </div>
  <div class="listings-grid">
    <?php foreach ($featured as $item): ?>
      <a href="listing.php?id=<?= urlencode($item['id']) ?>" class="listing-card">
        <div class="listing-img"><?= $item['img'] ?></div>
        <div class="listing-content">
          <div class="listing-category"><?= htmlspecialchars($item['category']) ?></div>
          <div class="listing-title"><?= htmlspecialchars($item['title']) ?></div>
          <div class="listing-price">₱<?= number_format($item['price']) ?></div>
          <div class="listing-other-info">
            <span><?= $userIcon ?> &nbsp; <?= htmlspecialchars($item['seller']) ?></span>
            <span><?= $locationIcon ?> &nbsp; <?= htmlspecialchars($item['location']) ?></span>
          </div>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
</div>
 
<!-- How It Works Section -->
<div class="home-section-full">
  <div class="inner">
    <div class="section-heading" style="justify-content: center; margin-bottom: 32px;">
      <h2>How It Works</h2>
    </div>
    <div class="how-it-works">
      <div class="step">
        <div class="step-number">1</div>
        <h3>Create an Account</h3>
        <p>Sign up for free using your CvSU student email. It only takes a minute.</p>
      </div>
      <div class="step">
        <div class="step-number">2</div>
        <h3>Post or Browse</h3>
        <p>List an item to sell or browse listings from fellow students.</p>
      </div>
      <div class="step">
        <div class="step-number">3</div>
        <h3>Finish Transaction</h3>
        <p>Message the seller or buyer, agree on a meetup spot, and make the deal.</p>
      </div>
    </div>
  </div>
</div>
 
<!-- Bottom Call To Action (For Guests Only) -->
<?php if (!$user): ?>
  <div class="home-section" style="padding-top: 48px; padding-bottom: 48px;">
    <div class="cta-banner">
      <h2>Ready to buy or sell?</h2>
      <p>Join with us and other CvSU students already using the marketplace.</p>
      <div class="cta-buttons">
        <a href="signup.php" class="btn-cta-primary">Get Started</a>
        <a href="#featured-listings-part" class="btn-cta-outline">Browse First</a>
      </div>
    </div>
  </div>
<?php endif; ?>
 
<?php include 'includes/footer.php'; ?>