<?php
/*
  index.php — CvSU Marketplace Home Page
  Visible to both guests and logged-in users.
*/
 
// --- Mock featured listings (replace with DB query later) ---
$user = ['name' => 'Spenzer', "id" => 17];

$featured = [
  ['title' => 'Calculus Textbook 10th Ed.',  'price' => '₱280', 'category' => 'Books',       'seller' => 'Maria S.',  'location' => 'Dorm A',    'img' => '📚'],
  ['title' => 'Scientific Calculator fx-991','price' => '₱450', 'category' => 'Electronics', 'seller' => 'Carlo R.',  'location' => 'Main Bldg', 'img' => '🧮'],
  ['title' => 'Engineering Drawing Kit',     'price' => '₱350', 'category' => 'Supplies',    'seller' => 'Anna F.',   'location' => 'Eng\'g Bldg','img' => '📐'],
  ['title' => 'Arduino Uno Starter Kit',     'price' => '₱900', 'category' => 'Electronics', 'seller' => 'Ryan C.',   'location' => 'IT Bldg',   'img' => '🔌'],
  ['title' => 'Nursing Uniform (Med L)',      'price' => '₱500', 'category' => 'Clothing',    'seller' => 'Lea M.',    'location' => 'Health Sci','img' => '🩺'],
  ['title' => 'Physics Lab Manual',          'price' => '₱120', 'category' => 'Books',       'seller' => 'Josh T.',   'location' => 'Sci Bldg',  'img' => '🔬'],
];
 
$categories = [
  ['label' => 'Books',       'icon' => '📚', 'count' => 142],
  ['label' => 'Electronics', 'icon' => '💻', 'count' => 87],
  ['label' => 'Supplies',    'icon' => '✏️',  'count' => 65],
  ['label' => 'Clothing',    'icon' => '👕', 'count' => 53],
  ['label' => 'Food',        'icon' => '🍱', 'count' => 39],
  ['label' => 'Services',    'icon' => '🛠️', 'count' => 28],
];
?>

<?php 
  session_start();
  $activePage = "home"; 
  include "includes/header.php"; 
  //unset($_SESSION['user_id']); // Test for guest navigation
  $_SESSION['user_id'] = 1; // Test for logged-in user navigation
?>

<!-- Hero Section -->
<section class="hero">
  <h1>The CvSU Student Marketplace</h1>
  <p>Buy and sell within the Cavite State University community - safe, easy, and free.</p>
 
  <!-- Search bar -->
  <form class="hero-search" action="/browse.php" method="GET">
    <input type="text" name="q" placeholder="Search for books, gadgets, supplies…" />
    <button type="submit">Search</button>
  </form>
 
  <!-- Show different CTAs depending on login state -->
  <?php if ($user): ?>
    <div class="hero-logged-in">
      👋 Welcome back, <strong><?= htmlspecialchars($user['name']) ?></strong>! &nbsp;
      <a href="/cvsu-marketplace/dashboard.php" style="color: #C0B87A; font-weight: 600;">Go to Dashboard →</a>
    </div>
  <?php else: ?>
    <div class="hero-actions">
      <a href="/register.php" class="btn-hero-primary">Create Free Account</a>
      <a href="/browse.php"   class="btn-hero-outline">Browse Listings</a>
    </div>
  <?php endif; ?>
</section>
 
 
<!-- Categories -->
<div class="home-section-full">
  <div class="inner">
    <div class="section-heading">
      <h2>Browse by Category</h2>
      <a href="/cvsu-marketplace/browse.php">See all →</a>
    </div>
    <div class="categories-grid">
      <?php foreach ($categories as $cat): ?>
        <a href="/browse.php?category=<?= urlencode($cat['label']) ?>" class="category-card">
          <div class="category-icon"><?= $cat['icon'] ?></div>
          <div class="category-label"><?= $cat['label'] ?></div>
          <div class="category-count"><?= $cat['count'] ?> listings</div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</div>
 
 
<!-- Featured Listings -->
<div class="home-section">
  <div class="section-heading">
    <h2>Featured Listings</h2>
    <a href="/browse.php">View all →</a>
  </div>
  <div class="listings-grid">
    <?php foreach ($featured as $item): ?>
      <a href="/listing.php" class="listing-card">
        <div class="listing-thumb"><?= $item['img'] ?></div>
        <div class="listing-body">
          <div class="listing-category"><?= $item['category'] ?></div>
          <div class="listing-title"><?= htmlspecialchars($item['title']) ?></div>
          <div class="listing-price"><?= $item['price'] ?></div>
          <div class="listing-meta">
            <span>👤 <?= htmlspecialchars($item['seller']) ?></span>
            <span>📍 <?= htmlspecialchars($item['location']) ?></span>
          </div>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
</div>
 
 
<!-- How It Works -->
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
        <p>List an item to sell or browse hundreds of listings from fellow students.</p>
      </div>
      <div class="step">
        <div class="step-number">3</div>
        <h3>Meet &amp; Trade</h3>
        <p>Message the seller, agree on a meetup spot on campus, and make the deal.</p>
      </div>
    </div>
  </div>
</div>
 
<!-- Call To Action (For Guests) -->
<?php if (!$user): ?>
  <div class="home-section" style="padding-top: 48px; padding-bottom: 48px;">
    <div class="cta-banner">
      <h2>Ready to buy or sell?</h2>
      <p>Join hundreds of CvSU students already using the marketplace.</p>
      <div class="cta-buttons">
        <a href="/cvsu-marketplace/signup.php" class="btn-cta-primary">Get Started for Free</a>
        <a href="/cvsu-marketplace/browse.php" class="btn-cta-outline">Browse First</a>
      </div>
    </div>
  </div>
<?php endif; ?>
 
<?php include 'includes/footer.php'; ?>