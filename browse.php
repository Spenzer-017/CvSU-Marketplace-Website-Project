<?php
/*
  browse.php — CvSU Marketplace Browse Page
  Shows all listings with filters and infinite scroll.
*/
 
// --- Mock listings data (replace with DB query later) ---
// In real use: SELECT * FROM listings WHERE ... LIMIT 12 OFFSET $offset
$all_listings = [
  ['id'=>1,  'title'=>'Calculus Textbook 10th Ed.',   'price'=>280,  'category'=>'Books',       'seller'=>'Maria S.',  'location'=>'Dorm A',     'img'=>'📚', 'condition'=>'Good'],
  ['id'=>2,  'title'=>'Scientific Calculator fx-991', 'price'=>450,  'category'=>'Electronics', 'seller'=>'Carlo R.',  'location'=>'Main Bldg',  'img'=>'🧮', 'condition'=>'Like New'],
  ['id'=>3,  'title'=>'Engineering Drawing Kit',      'price'=>350,  'category'=>'Supplies',    'seller'=>'Anna F.',   'location'=>'Eng Bldg',   'img'=>'📐', 'condition'=>'Good'],
  ['id'=>4,  'title'=>'Arduino Uno Starter Kit',      'price'=>900,  'category'=>'Electronics', 'seller'=>'Ryan C.',   'location'=>'IT Bldg',    'img'=>'🔌', 'condition'=>'Like New'],
  ['id'=>5,  'title'=>'Nursing Uniform (Med, L)',      'price'=>500,  'category'=>'Clothing',    'seller'=>'Lea M.',    'location'=>'Health Sci', 'img'=>'🩺', 'condition'=>'Good'],
  ['id'=>6,  'title'=>'Physics Lab Manual',           'price'=>120,  'category'=>'Books',       'seller'=>'Josh T.',   'location'=>'Sci Bldg',   'img'=>'🔬', 'condition'=>'Fair'],
  ['id'=>7,  'title'=>'USB-C Hub 7-in-1',            'price'=>550,  'category'=>'Electronics', 'seller'=>'Leo M.',    'location'=>'Dorm B',     'img'=>'💻', 'condition'=>'Like New'],
  ['id'=>8,  'title'=>'Notebook Bundle (5 pcs)',      'price'=>150,  'category'=>'Supplies',    'seller'=>'Claire T.', 'location'=>'Main Bldg',  'img'=>'📓', 'condition'=>'New'],
  ['id'=>9,  'title'=>'Organic Chemistry Book',       'price'=>320,  'category'=>'Books',       'seller'=>'Ken P.',    'location'=>'Sci Bldg',   'img'=>'📗', 'condition'=>'Good'],
  ['id'=>10, 'title'=>'Mechanical Keyboard',          'price'=>1200, 'category'=>'Electronics', 'seller'=>'Dan V.',    'location'=>'IT Bldg',    'img'=>'⌨️', 'condition'=>'Like New'],
  ['id'=>11, 'title'=>'Drafting Table Lamp',          'price'=>400,  'category'=>'Supplies',    'seller'=>'Rose A.',   'location'=>'Dorm A',     'img'=>'💡', 'condition'=>'Good'],
  ['id'=>12, 'title'=>'CvSU PE Uniform Set',          'price'=>380,  'category'=>'Clothing',    'seller'=>'Sam L.',    'location'=>'Gym',        'img'=>'👟', 'condition'=>'Good'],
];
 
$categories = ['All', 'Books', 'Electronics', 'Supplies', 'Clothing', 'Food', 'Services'];
$conditions = ['Any Condition', 'New', 'Like New', 'Good', 'Fair'];
 
// Active filters from URL
$active_cat  = $_GET['category']  ?? 'All';
$active_sort = $_GET['sort']      ?? 'newest';
$search_q    = $_GET['q']         ?? '';
?>

<?php 
  session_start();
  $activePage = "browse"; 
  include "includes/header.php";
?>

<!-- ================================
     BROWSE BODY
     ================================ -->
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
      <svg width="14" height="14" fill="none" stroke="#7A8C7A" stroke-width="2"
           viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input type="text" id="searchInput" placeholder="Search listings…"
             value="<?= htmlspecialchars($search_q) ?>" />
    </div>
 
    <!-- Condition filter -->
    <select class="filter-select" id="conditionFilter">
      <?php foreach ($conditions as $c): ?>
        <option value="<?= $c ?>"><?= $c ?></option>
      <?php endforeach; ?>
    </select>
 
    <!-- Sort -->
    <select class="filter-select" id="sortFilter">
      <option value="newest"    <?= $active_sort === 'newest'    ? 'selected' : '' ?>>Newest First</option>
      <option value="price_asc" <?= $active_sort === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
      <option value="price_desc"<?= $active_sort === 'price_desc'? 'selected' : '' ?>>Price: High to Low</option>
    </select>
  </div>
 
  <!-- Category tabs -->
  <div class="category-tabs">
    <?php foreach ($categories as $cat): ?>
      <a href="?category=<?= urlencode($cat) ?>"
         class="cat-tab <?= $active_cat === $cat ? 'active' : '' ?>">
        <?= $cat ?>
      </a>
    <?php endforeach; ?>
  </div>
 
  <!-- Result count (updated by JS) -->
  <p class="result-count" id="resultCount">
    Showing <span id="shownCount">0</span> listings
  </p>
 
  <!-- Listings grid — cards are injected here by JS -->
  <div class="listings-grid" id="listingsGrid"></div>
 
  <!-- Empty state — shown when no results match -->
  <div class="empty-state" id="emptyState" style="display:none;">
    <div class="empty-icon">🔍</div>
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
  <div id="end-message">✅ You've seen all listings!</div>
 
</div><!-- /.browse-page -->
 
 
<script>
  // ================================================
  //  ALL LISTINGS DATA
  //  In a real app, you'd fetch this from the server
  //  via an API endpoint like /api/listings.php
  // ================================================
  const ALL_LISTINGS = <?= json_encode($all_listings) ?>;
 
  // How many cards to show per "page" (each scroll load)
  const PER_PAGE = 8;
 
  let currentPage   = 0;   // which batch we're on
  let isLoading     = false;
  let allLoaded     = false;
  let filteredItems = [];   // holds the current filtered+sorted list
 
  // ── DOM refs ──────────────────────────────────
  const grid          = document.getElementById('listingsGrid');
  const loader        = document.getElementById('loader');
  const endMsg        = document.getElementById('end-message');
  const emptyState    = document.getElementById('emptyState');
  const shownCount    = document.getElementById('shownCount');
  const searchInput   = document.getElementById('searchInput');
  const conditionSel  = document.getElementById('conditionFilter');
  const sortSel       = document.getElementById('sortFilter');
 
  // ── Build one card HTML string ─────────────────
  function buildCard(item) {
    return `
      <a href="/listing.php?id=${item.id}" class="listing-card">
        <div class="listing-thumb">
          <span class="condition-badge">${item.condition}</span>
          ${item.img}
        </div>
        <div class="listing-body">
          <div class="listing-category">${item.category}</div>
          <div class="listing-title">${item.title}</div>
          <div class="listing-price">₱${item.price.toLocaleString()}</div>
          <div class="listing-meta">
            <span>👤 ${item.seller}</span>
            <span>📍 ${item.location}</span>
          </div>
        </div>
      </a>`;
  }
 
  // ── Apply search + condition + sort filters ─────
  function applyFilters() {
    const q         = searchInput.value.trim().toLowerCase();
    const condition = conditionSel.value;
    const sort      = sortSel.value;
 
    // Get active category from URL
    const params    = new URLSearchParams(window.location.search);
    const category  = params.get('category') || 'All';
 
    let items = [...ALL_LISTINGS];
 
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
    if (sort === 'price_asc')  items.sort((a, b) => a.price - b.price);
    if (sort === 'price_desc') items.sort((a, b) => b.price - a.price);
    // 'newest' keeps original order (already sorted by id desc from DB)
 
    filteredItems = items;
  }
 
  // ── Reset and reload from page 0 ───────────────
  function resetGrid() {
    grid.innerHTML = '';
    currentPage    = 0;
    allLoaded      = false;
    endMsg.style.display   = 'none';
    emptyState.style.display = 'none';
    applyFilters();
    loadNextBatch();
  }
 
  // ── Load the next batch of PER_PAGE items ───────
  function loadNextBatch() {
    if (isLoading || allLoaded) return;
 
    isLoading = true;
    loader.style.display = 'block';
 
    // Simulate a small network delay (remove this in production,
    // and replace with a real fetch() call to your PHP API)
    setTimeout(() => {
      const start = currentPage * PER_PAGE;
      const batch = filteredItems.slice(start, start + PER_PAGE);
 
      if (batch.length === 0 && currentPage === 0) {
        // No results at all
        emptyState.style.display = 'block';
      } else {
        batch.forEach(item => {
          grid.insertAdjacentHTML('beforeend', buildCard(item));
        });
        currentPage++;
      }
 
      // Update count
      const shown = Math.min(currentPage * PER_PAGE, filteredItems.length);
      shownCount.textContent = shown;
 
      // Check if we've loaded everything
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
 
  // ── Infinite Scroll via IntersectionObserver ────
  // This fires loadNextBatch() whenever the sentinel
  // element scrolls into view at the bottom of the page.
  const observer = new IntersectionObserver((entries) => {
    if (entries[0].isIntersecting) {
      loadNextBatch();
    }
  }, {
    rootMargin: '200px' // start loading 200px before reaching the bottom
  });
 
  observer.observe(document.getElementById('scroll-sentinel'));
 
  // ── Filter event listeners ───────────────────
  // Debounce the search so it doesn't fire on every keypress
  let searchTimer;
  searchInput.addEventListener('input', () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(resetGrid, 300);
  });
 
  conditionSel.addEventListener('change', resetGrid);
  sortSel.addEventListener('change', resetGrid);
 
  // ── Initial load ─────────────────────────────
  resetGrid();
</script>
 
<?php include 'includes/footer.php'; ?>