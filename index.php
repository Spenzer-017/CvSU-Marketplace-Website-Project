<?php
/*
  index.php — CvSU Marketplace Home Page
  Visible to both guests and logged-in users.
*/
 
// --- Mock featured listings (replace with DB query later) ---

$featured = [
  ['title' => 'Ethics Book', 'price' => 400, 'category' => 'Books', 'seller' => 'Gypsy', 'location' => 'CAS', 'img' => '<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#434343"><path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Zm40-337 160-160 160 160 160-160 40 40v-183H200v263l40 40Zm-40 257h560v-264l-40-40-160 160-160-160-160 160-40-40v184Zm0 0v-264 80-376 560Z"/></svg>'],
  ['title' => 'CvSU ID Lace','price' => 80, 'category' => 'Supplies', 'seller' => 'Charlie',  'location' => 'CEIT', 'img' => '<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#434343"><path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Zm40-337 160-160 160 160 160-160 40 40v-183H200v263l40 40Zm-40 257h560v-264l-40-40-160 160-160-160-160 160-40-40v184Zm0 0v-264 80-376 560Z"/></svg>'],
  ['title' => 'Iphone 67 Pro Max Fully Paid', 'price' => 6700, 'category' => 'Electronics', 'seller' => 'Jedhorse', 'location' => 'DIT','img' => '<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#434343"><path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Zm40-337 160-160 160 160 160-160 40 40v-183H200v263l40 40Zm-40 257h560v-264l-40-40-160 160-160-160-160 160-40-40v184Zm0 0v-264 80-376 560Z"/></svg>'],
  ['title' => 'Lomi', 'price' => 99, 'category' => 'Food', 'seller' => 'Nuggets', 'location' => 'DIET', 'img' => '<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#434343"><path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Zm40-337 160-160 160 160 160-160 40 40v-183H200v263l40 40Zm-40 257h560v-264l-40-40-160 160-160-160-160 160-40-40v184Zm0 0v-264 80-376 560Z"/></svg>'],
  ['title' => 'CvSU Uniform M (Large)', 'price' => 500, 'category' => 'Clothing', 'seller' => 'Blessie', 'location' => 'CAS','img' => '<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#434343"><path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Zm40-337 160-160 160 160 160-160 40 40v-183H200v263l40 40Zm-40 257h560v-264l-40-40-160 160-160-160-160 160-40-40v184Zm0 0v-264 80-376 560Z"/></svg>'],
  ['title' => 'Laundry Services', 'price' => 350, 'category' => 'Services', 'seller' => 'Moglie', 'location' => 'Bancod',  'img' => '<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#434343"><path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Zm40-337 160-160 160 160 160-160 40 40v-183H200v263l40 40Zm-40 257h560v-264l-40-40-160 160-160-160-160 160-40-40v184Zm0 0v-264 80-376 560Z"/></svg>'],
  ['title' => 'Understanding The Self Book', 'price' => 450, 'category' => 'Books', 'seller' => 'Happy', 'location' => 'Oval',  'img' => '<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#434343"><path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Zm40-337 160-160 160 160 160-160 40 40v-183H200v263l40 40Zm-40 257h560v-264l-40-40-160 160-160-160-160 160-40-40v184Zm0 0v-264 80-376 560Z"/></svg>'],
  ['title' => 'Keychain', 'price' => 25, 'category' => 'Other', 'seller' => 'Pumpkin', 'location' => 'Grand Stand',  'img' => '<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#434343"><path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Zm40-337 160-160 160 160 160-160 40 40v-183H200v263l40 40Zm-40 257h560v-264l-40-40-160 160-160-160-160 160-40-40v184Zm0 0v-264 80-376 560Z"/></svg>']
];
 
$categories = [
  ['label' => 'Books', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#434343"><path d="M300-80q-58 0-99-41t-41-99v-520q0-58 41-99t99-41h500v600q-25 0-42.5 17.5T740-220q0 25 17.5 42.5T800-160v80H300Zm-60-267q14-7 29-10t31-3h20v-440h-20q-25 0-42.5 17.5T240-740v393Zm160-13h320v-440H400v440Zm-160 13v-453 453Zm60 187h373q-6-14-9.5-28.5T660-220q0-16 3-31t10-29H300q-26 0-43 17.5T240-220q0 26 17 43t43 17Z"/></svg>', 'count' => 142],
  ['label' => 'Electronics', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#434343"><path d="M80-160q-33 0-56.5-23.5T0-240h160q-33 0-56.5-23.5T80-320v-440q0-33 23.5-56.5T160-840h640q33 0 56.5 23.5T880-760v440q0 33-23.5 56.5T800-240h160q0 33-23.5 56.5T880-160H80Zm428.5-51.5Q520-223 520-240t-11.5-28.5Q497-280 480-280t-28.5 11.5Q440-257 440-240t11.5 28.5Q463-200 480-200t28.5-11.5ZM160-320h640v-440H160v440Zm0 0v-440 440Z"/></svg>', 'count' => 87],
  ['label' => 'Supplies', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#434343"><path d="m490-527 37 37 217-217-37-37-217 217ZM200-200h37l233-233-37-37-233 233v37Zm355-205L405-555l167-167-29-29-219 219-56-56 218-219q24-24 56.5-24t56.5 24l29 29 50-50q12-12 28.5-12t28.5 12l93 93q12 12 12 28.5T828-678L555-405ZM270-120H120v-150l285-285 150 150-285 285Z"/></svg>',  'count' => 65],
  ['label' => 'Clothing', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#434343"><path d="m240-522-40 22q-14 8-30 4t-24-18L66-654q-8-14-4-30t18-24l230-132h70q9 0 14.5 5.5T400-820v20q0 33 23.5 56.5T480-720q33 0 56.5-23.5T560-800v-20q0-9 5.5-14.5T580-840h70l230 132q14 8 18 24t-4 30l-80 140q-8 14-23.5 17.5T760-501l-40-20v361q0 17-11.5 28.5T680-120H280q-17 0-28.5-11.5T240-160v-362Zm80-134v456h320v-456l124 68 42-70-172-100q-15 51-56.5 84.5T480-640q-56 0-97.5-33.5T326-758L154-658l42 70 124-68Zm160 177Z"/></svg>', 'count' => 53],
  ['label' => 'Food', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#434343"><path d="M280-80v-366q-51-14-85.5-56T160-600v-280h80v280h40v-280h80v280h40v-280h80v280q0 56-34.5 98T360-446v366h-80Zm400 0v-320H560v-280q0-83 58.5-141.5T760-880v800h-80Z"/></svg>', 'count' => 39],
  ['label' => 'Services', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#434343"><path d="M756-120 537-339l84-84 219 219-84 84Zm-552 0-84-84 276-276-68-68-28 28-51-51v82l-28 28-121-121 28-28h82l-50-50 142-142q20-20 43-29t47-9q24 0 47 9t43 29l-92 92 50 50-28 28 68 68 90-90q-4-11-6.5-23t-2.5-24q0-59 40.5-99.5T701-841q15 0 28.5 3t27.5 9l-99 99 72 72 99-99q7 14 9.5 27.5T841-701q0 59-40.5 99.5T701-561q-12 0-24-2t-23-7L204-120Z"/></svg>', 'count' => 28],
];
?>

<?php 
  session_start();
  $activePage = "home"; 
  include "includes/header.php"; 
  unset($_SESSION['user']); // Test for guest navigation
  // $_SESSION['user'] = ["name" => "Spenzer", "id" => 17]; // Test for logged-in user navigation
  $user = $_SESSION['user'] ?? null; 
?>

<!-- Hero Section -->
<section class="hero">
  <h1>CvSU Student Marketplace</h1>
  <p>Buy and sell within the Cavite State University community - safe, easy, and free.</p>
 
  <!-- Show different CTAs depending on login state -->
  <?php if ($user): ?>
    <!-- Search bar -->
    <form class="hero-search" action="/browse.php" method="GET">
      <input type="text" name="q" placeholder="Search for books, gadgets, supplies…" />
      <button type="submit">Search</button>
    </form>

    <div class="hero-logged-in">
      Welcome back, <strong><?= htmlspecialchars($user['name']) ?></strong>! 
      <a href="/cvsu-marketplace/dashboard.php" style="color: #C0B87A; font-weight: 600;">Go to Dashboard →</a>
    </div>
  <?php else: ?>
    <div class="hero-actions">
      <a href="/register.php" class="btn-hero-primary">Create Free Account</a>
      <a href="/browse.php" class="btn-hero-outline">Browse Listings</a>
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
        <a href="/cvsu-marketplace/browse.php?category=<?= urlencode($cat['label']) ?>" class="category-card">
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
          <div class="listing-price">₱<?= $item['price'] ?></div>
          <div class="listing-other-info">
            <span><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#434343"><path d="M234-276q51-39 114-61.5T480-360q69 0 132 22.5T726-276q35-41 54.5-93T800-480q0-133-93.5-226.5T480-800q-133 0-226.5 93.5T160-480q0 59 19.5 111t54.5 93Zm146.5-204.5Q340-521 340-580t40.5-99.5Q421-720 480-720t99.5 40.5Q620-639 620-580t-40.5 99.5Q539-440 480-440t-99.5-40.5ZM480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm100-95.5q47-15.5 86-44.5-39-29-86-44.5T480-280q-53 0-100 15.5T294-220q39 29 86 44.5T480-160q53 0 100-15.5ZM523-537q17-17 17-43t-17-43q-17-17-43-17t-43 17q-17 17-17 43t17 43q17 17 43 17t43-17Zm-43-43Zm0 360Z"/></svg><?= htmlspecialchars($item['seller']) ?></span>
            <span><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#434343"><path d="M536.5-503.5Q560-527 560-560t-23.5-56.5Q513-640 480-640t-56.5 23.5Q400-593 400-560t23.5 56.5Q447-480 480-480t56.5-23.5ZM480-186q122-112 181-203.5T720-552q0-109-69.5-178.5T480-800q-101 0-170.5 69.5T240-552q0 71 59 162.5T480-186Zm0 106Q319-217 239.5-334.5T160-552q0-150 96.5-239T480-880q127 0 223.5 89T800-552q0 100-79.5 217.5T480-80Zm0-480Z"/></svg><?= htmlspecialchars($item['location']) ?></span>
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