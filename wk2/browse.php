<!-- PHP Logic (Authentication, Session, etc.) -->
<?php 
  /*
    browse.php - CvSU Marketplace Browse Page
    Shows all listings with filters and infinite scroll.
  */

  session_start();

  // Forced user login (Remove/change later on for authentication)
  // $_SESSION['user'] = ["name" => "Spenzer", 'course' => 'BS Computer Science', "id" => 17];
  $user = $_SESSION['user'] ?? null;
?>

<!-- PHP UI/UX Logic -->
<?php
  $activePage = "browse"; 
  include "includes/header.php";
?>

<!-- PHP Database Query -->
<?php 
  // Mock listings data (replace with real DB query later)
  // In real use: SELECT * FROM listings WHERE ... LIMIT 12 OFFSET $offset (To load only 12 listing per)
  $all_listings = [
    ['id' => 1641, 'title' => 'Art Appreciation Book', 'price' => 280, 'category' => 'Books', 'seller' => 'Nuggets', 'location' => 'Library', 'img' => $imgNotAvailableIcon, 'condition' => 'Good'],
    ['id' => 2457, 'title' => 'Mini Fan', 'price' => 150, 'category' => 'Electronics', 'seller' => 'Bona', 'location' => 'CAS', 'img' => $imgNotAvailableIcon, 'condition' => 'Like New'],
    ['id' => 3312, 'title' => 'BT Earphones', 'price' => 250, 'category' => 'Supplies', 'seller' => 'Mikel', 'location' => 'CEMDS', 'img' => $imgNotAvailableIcon, 'condition' => 'Good'],
    ['id' => 4098, 'title' => 'CvSU ID Lace','price' => 80, 'category' => 'Supplies', 'seller' => 'Charlie',  'location' => 'CEIT', 'img' => $imgNotAvailableIcon, 'condition' => 'Like New'],
    ['id' => 5156, 'title' => 'Iphone 67 Pro Max Fully Paid', 'price' => 6700, 'category' => 'Electronics', 'seller' => 'Jedhorse', 'location' => 'DIT','img' => $imgNotAvailableIcon, 'condition' => 'Good'],
    ['id' => 6230, 'title' => 'Lomi', 'price' => 99, 'category' => 'Food', 'seller' => 'Nuggets', 'location' => 'DIET', 'img' => $imgNotAvailableIcon, 'condition' => 'N/A'],
    ['id' => 7998, 'title' => 'CvSU Uniform M (Large)', 'price' => 300, 'category' => 'Clothing', 'seller' => 'Blessie', 'location' => 'CAS','img' => $imgNotAvailableIcon, 'condition' => 'Like New'],
    ['id' => 8008, 'title' => 'Silver Necklace', 'price' => 350, 'category' => 'Accessories', 'seller' => 'Moglie', 'location' => 'Bancod',  'img' => $imgNotAvailableIcon, 'condition' => 'N/A'],
    ['id' => 9669, 'title' => 'Understanding The Self Book', 'price' => 450, 'category' => 'Books', 'seller' => 'Happy', 'location' => 'Oval',  'img' => $imgNotAvailableIcon, 'condition' => 'Good'],
    ['id' => 1010, 'title' => 'Keychain', 'price' => 25, 'category' => 'Other', 'seller' => 'Pumpkin', 'location' => 'Grand Stand',  'img' => $imgNotAvailableIcon, 'condition' => 'Like New'],
    ['id' => 1122, 'title' => 'Pins', 'price' => 30, 'category' => 'Other', 'seller' => 'Jettie', 'location' => 'Dorm A', 'img' => $imgNotAvailableIcon, 'condition' => 'Good'],
    ['id' => 1200, 'title' => 'CvSU PE Uniform Set', 'price' => 300, 'category' => 'Clothing', 'seller' => 'Wiffie', 'location' => 'Gym', 'img' => $imgNotAvailableIcon, 'condition' => 'Good'],
];
  
  $categories = ['All', 'Books', 'Electronics', 'Supplies', 'Clothing', 'Food', 'Accessories', 'Other'];
  $conditions = ['Any Condition', 'New', 'Like New', 'Good', 'Fair', 'N/A'];
  
  // Active filters from URL
  $active_cat = $_GET['category'] ?? 'All';
  $active_sort = $_GET['sort'] ?? 'newest';
  $search_q = $_GET['q'] ?? '';
?>

<!-- Browse Body -->
<div class="browse-page">
 
  <!-- Page title -->
  <div class="browse-header">
    <h1>Browse Listings</h1>
    <p>Find what you need from fellow CvSU students.</p>
  </div>
 
  <!-- Filter bar -->
  <div class="filter-bar">
    <!-- Search -->
    <div class="filter-search">
      <?= $searchIcon ?>
      <input type="text" id="searchInput" placeholder="Search listings…" value="<?= htmlspecialchars($search_q) ?>" />
    </div>
 
    <!-- Condition filter -->
    <select class="filter-select" id="conditionFilter">
      <?php foreach ($conditions as $con): ?>
        <option value="<?= $con ?>"><?= $con ?></option>
      <?php endforeach; ?>
    </select>
 
    <!-- Sort -->
    <select class="filter-select" id="sortFilter">
      <option value="newest" <?= $active_sort === 'newest' ? 'selected' : '' ?>>Newest First</option>
      <option value="price_asc"<?= $active_sort === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
      <option value="price_desc" <?= $active_sort === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
    </select>
  </div>
 
  <!-- Category tabs -->
  <div class="category-tabs">
    <?php foreach ($categories as $cat): ?>
      <a href="?category=<?= urlencode($cat) ?>&q=<?= urlencode($search_q) ?>&sort=<?= urlencode($active_sort) ?>" class="cat-tab <?= $active_cat === $cat ? 'active' : '' ?>"> <?= htmlspecialchars($cat) ?>
      </a>
    <?php endforeach; ?>
  </div>
 
  <!-- Result count (Updated in JS script) -->
  <p class="result-count" id="resultCount">
    Showing <span id="shownCount">0</span> listings
  </p>
 
  <!-- Listings grid (Card listings are put here by JS script) -->
  <div class="listings-grid" id="listingsGrid"></div>
 
  <!-- Empty state (Shown when no results match) -->
  <div class="empty-state" id="emptyState" style="display:none;">
    <div class="empty-icon"><?= $searchIcon ?></div>
    <h3>No listings found</h3>
    <p>Try a different search term or category.</p>
  </div>
 
  <!-- Invisible element at the bottom that triggers loading more -->
  <div id="scroll-sentinel"></div>
 
  <!-- Loading animation -->
  <div class="loader" id="loader">
    <div class="loader-dots">
      <span></span><span></span><span></span>
    </div>
  </div>
 
  <!-- Shown when all items are loaded -->
  <div id="end-message">You've seen all listings!</div>
 
</div>

<script>
  //  All Listing Data
  //  Fetch this from a real server later on
  
  const allListings = <?= json_encode($all_listings) ?>;
 
  // How many cards to show per batch (each scroll load)
  const perBatchAmt = 8;
 
  let currentBatch = 0;
  let isLoading = false;
  let allLoaded = false;
  let filteredItems = [];
 
  // DOM Variables
  const grid = document.getElementById('listingsGrid');
  const loader = document.getElementById('loader');
  const endMsg = document.getElementById('end-message');
  const emptyState = document.getElementById('emptyState');
  const shownCount = document.getElementById('shownCount');
  const searchInput = document.getElementById('searchInput');
  const conditionFil = document.getElementById('conditionFilter');
  const sortFil = document.getElementById('sortFilter');

  // PHP Variables (SVGs)
  const userIcon = <?= json_encode($userIcon) ?>;
  const locationIcon = <?= json_encode($locationIcon) ?>;
 
  // Build one card to the HTML
  function buildCard(item) {
    return `
      <a href="listing.php?id=${item.id}" class="listing-card">
        <div class="listing-img">
          <span class="condition-badge">${item.condition}</span>
          ${item.img}
        </div>
        <div class="listing-content">
          <div class="listing-category">${item.category}</div>
          <div class="listing-title">${item.title}</div>
          <div class="listing-price">₱${item.price.toLocaleString()}</div>
          <div class="listing-other-info">
            <span> ${userIcon}&nbsp;${item.seller}</span>
            <span> ${locationIcon}&nbsp;${item.location}</span>
          </div>
        </div>
      </a>`;
  }
 
  // Apply search, condition, sort filters
  function applyFilters() {
    const q = searchInput.value.trim().toLowerCase();
    const condition = conditionFil.value;
    const sort = sortFil.value;
 
    // Get active category from URL
    const params = new URLSearchParams(window.location.search);
    const category = params.get('category') || 'All';
 
    let items = [...allListings];
 
    // Filter by category
    if (category !== 'All') {
      items = items.filter(i => i.category === category);
    }
 
    // Filter by search query
    if (q) {
      items = items.filter(i =>
        i.title.toLowerCase().includes(q) ||
        i.category.toLowerCase().includes(q)
      );
    }
 
    // Filter by condition
    if (condition !== 'Any Condition') {
      items = items.filter(i => i.condition === condition);
    }
 
    // Sort
    if (sort === 'price_asc') items.sort((a, b) => a.price - b.price);
    if (sort === 'price_desc') items.sort((a, b) => b.price - a.price);
 
    filteredItems = items;
  }
 
  // Reset and reload from page 0
  function resetGrid() {
    grid.innerHTML = '';
    currentBatch = 0;
    allLoaded = false;
    endMsg.style.display = 'none';
    emptyState.style.display = 'none';
    applyFilters();
    loadNextBatch();
  }
 
  // Load the next batch of items
  function loadNextBatch() {
    if (isLoading || allLoaded) return;
 
    isLoading = true;
    loader.style.display = 'block';
 
    // Simulate a small network delay (remove this in production, and replace with a real fetch() call to PHP API)
    setTimeout(() => {
      const start = currentBatch * perBatchAmt;
      const batch = filteredItems.slice(start, start + perBatchAmt);
 
      if (batch.length === 0 && currentBatch === 0) {
        // No results at all
        emptyState.style.display = 'block';
      } else {
        batch.forEach(item => {
          grid.insertAdjacentHTML('beforeend', buildCard(item));
        });
        currentBatch++;
      }
 
      // Update count
      const shown = Math.min(currentBatch * perBatchAmt, filteredItems.length);
      shownCount.textContent = shown;
 
      // Check if loaded everything
      if (start + batch.length >= filteredItems.length) {
        allLoaded = true;
        if (filteredItems.length > 0) {
          endMsg.style.display = 'block';
        }
      }
 
      isLoading = false;
      loader.style.display = 'none';
    }, 400); // 400ms simulated delay
  }
 
  // Infinite Scroll via IntersectionObserver
  // This fires loadNextBatch() function whenever the sentinel element scrolls into view at the bottom of the page.
  const observer = new IntersectionObserver((entries) => {
    if (entries[0].isIntersecting) {
      loadNextBatch();
    }
  }, {
    rootMargin: '200px' // start loading 200px before reaching the bottom
  });
 
  observer.observe(document.getElementById('scroll-sentinel'));
 
  // Filter event listeners
  // Debounce the search so it doesn't fire on every keypress
  let searchTimer;
  searchInput.addEventListener('input', () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(resetGrid, 300);
  });
 
  conditionFil.addEventListener('change', resetGrid);
  sortFil.addEventListener('change', resetGrid);
 
  // Initial load
  resetGrid();
</script>
 
<?php include 'includes/footer.php'; ?>