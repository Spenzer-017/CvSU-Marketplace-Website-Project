<!-- PHP Logic (Authentication, Session, etc.) -->
<?php 
  /*
    browse.php - CvSU Marketplace Browse Page
    Shows all listings with filters and infinite scroll.
  */

  session_start();

  require_once "includes/db.php";
  
  if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
  }

  $user = $_SESSION['user'] ?? null; 
?>

<!-- PHP UI/UX Logic -->
<?php
  $activePage = "browse"; 
  include "includes/header.php";
?>

<!-- PHP Database Query -->
<?php 
  // Pagination Setup
  $perBatch = 8;
  $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
  $offset = ($page - 1) * $perBatch;

  // Items Database Query
  $stmt = $pdo->prepare("
    SELECT 
      items.item_id AS id,
      items.title,
      items.price,
      items.image_path,
      items.condition_type AS `condition`,
      items.meetup_location AS location,
      categories.name AS category,
      users.name AS seller
    FROM items
    JOIN users ON items.seller_id = users.id
    JOIN categories ON items.category_id = categories.category_id
    WHERE items.status = 'active'
    ORDER BY items.created_at DESC
  ");

  $perBatch = 40;
  $offset = 0;

  $stmt = $pdo->prepare("
    SELECT 
      items.item_id AS id,
      items.title,
      items.price,
      items.image_path,
      items.condition_type AS `condition`,
      items.meetup_location AS location,
      categories.name AS category,
      users.name AS seller
    FROM items
    JOIN users ON items.seller_id = users.id
    JOIN categories ON items.category_id = categories.category_id
    WHERE items.status = 'active'
    ORDER BY items.created_at DESC
    LIMIT :limit OFFSET :offset
  ");

  $stmt->bindValue(':limit', $perBatch, PDO::PARAM_INT);
  $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
  $stmt->execute();

  $items = $stmt->fetchAll();

  // Convert to JS friendly format
  $all_listings = [];

  foreach ($items as $item) {
    $imgPath = "uploads/" . $item['image_path'];

    if (!empty($item['image_path']) && file_exists($imgPath)) {
      $img = '<img src="' . htmlspecialchars($imgPath) . '" alt="Item Image">';
    } else {
      $img = $imgNotAvailableIcon;
    }

    $all_listings[] = [
      'id' => $item['id'],
      'title' => $item['title'],
      'price' => (int)$item['price'],
      'category' => $item['category'],
      'seller' => $item['seller'],
      'location' => $item['location'],
      'condition' => $item['condition'],
      'img' => $img
    ];
  }

  $categories = ['All', 'Books', 'Electronics', 'Supplies', 'Clothing', 'Food', 'Services', 'Other'];
  $conditions = ['Any Condition', 'New', 'Like New', 'Good', 'Fair', 'N/A'];

  // Active filters
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
      <a href="?category=<?= urlencode($cat) ?>&q=<?= urlencode($search_q) ?>&sort=<?= urlencode($active_sort) ?>"
         class="cat-tab <?= $active_cat === $cat ? 'active' : '' ?>">
        <?= htmlspecialchars($cat) ?>
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
          <div class="listing-price">&#8369;${item.price.toLocaleString()}</div>
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

  // Load more items from server
  async function loadMoreFromServer() {
    const nextPage = Math.floor(allListings.length / 40) + 1;

    const res = await fetch(`api/get_listings.php?page=${nextPage}`);
    const data = await res.json();

    if (data.length === 0) {
      allLoaded = true;
      return;
    }

    allListings.push(...data);
  }
 
  // Load the next batch of items
  async function loadNextBatch() {
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

    const endOfLoadedData = start + perBatchAmt >= allListings.length;

    if (endOfLoadedData && !allLoaded) {
      await loadMoreFromServer();
    }
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