<?php
/*
  cart.php — Shopping Cart
  Shows items the user has saved/reserved to buy.
*/

// Uncomment when you have real auth:
// if (!isset($_SESSION['user'])) {
//   header('Location: /login.php');
//   exit;
// }

// --- Mock cart items (replace with DB query later) ---
// Real query: SELECT cart.*, listings.* FROM cart
//             JOIN listings ON cart.listing_id = listings.id
//             WHERE cart.user_id = $_SESSION['user']['id']
$cart_items = [
  [
    'id'        => 1,
    'title'     => 'Calculus Textbook 10th Edition',
    'category'  => 'Books',
    'condition' => 'Good',
    'price'     => 280,
    'seller'    => 'Maria Santos',
    'location'  => 'Main Building',
    'img'       => '📚',
  ],
  [
    'id'        => 2,
    'title'     => 'Scientific Calculator fx-991',
    'category'  => 'Electronics',
    'condition' => 'Like New',
    'price'     => 450,
    'seller'    => 'Carlo Reyes',
    'location'  => 'IT Building',
    'img'       => '🧮',
  ],
  [
    'id'        => 3,
    'title'     => 'Engineering Drawing Kit',
    'category'  => 'Supplies',
    'condition' => 'Good',
    'price'     => 350,
    'seller'    => 'Anna Flores',
    'location'  => 'Engineering Bldg',
    'img'       => '📐',
  ],
];

$subtotal = array_sum(array_column($cart_items, 'price'));
?>

<?php 
    session_start();
    $activePage = "cart"; 
    include "includes/header.php";
?>

<div class="cart-page">

  <!-- Page header -->
  <div class="cart-header">
    <div>
      <h1>My Cart</h1>
      <p><?= count($cart_items) ?> item<?= count($cart_items) !== 1 ? 's' : '' ?> saved</p>
    </div>
    <a href="/cvsu-marketplace/browse.php" class="btn-back">← Continue Browsing</a>
  </div>

  <?php if (empty($cart_items)): ?>

    <!-- Empty cart state -->
    <div class="cart-empty">
      <div class="empty-icon">🛒</div>
      <h2>Your cart is empty</h2>
      <p>Browse listings and save items you want to buy.</p>
      <a href="/browse.php" class="btn-browse">Browse Listings</a>
    </div>

  <?php else: ?>

    <div class="cart-layout">

      <!-- LEFT: Cart items list -->
      <div class="cart-items">

        <?php foreach ($cart_items as $item): ?>
          <div class="cart-card" id="cart-item-<?= $item['id'] ?>">

            <!-- Thumbnail -->
            <div class="cart-thumb"><?= $item['img'] ?></div>

            <!-- Item details -->
            <div class="cart-info">
              <div class="cart-category"><?= htmlspecialchars($item['category']) ?></div>
              <a href="/listing.php?id=<?= $item['id'] ?>" class="cart-title">
                <?= htmlspecialchars($item['title']) ?>
              </a>
              <div class="cart-meta">
                <span class="badge badge-condition"><?= htmlspecialchars($item['condition']) ?></span>
                <span class="cart-seller">👤 <?= htmlspecialchars($item['seller']) ?></span>
                <span class="cart-location">📍 <?= htmlspecialchars($item['location']) ?></span>
              </div>
            </div>

            <!-- Price + actions -->
            <div class="cart-actions">
              <div class="cart-price">₱<?= number_format($item['price']) ?></div>
              <div class="cart-buttons">
                <a href="/messages.php?seller=<?= urlencode($item['seller']) ?>&item=<?= $item['id'] ?>"
                   class="btn-message">
                  💬 Message Seller
                </a>
                <button
                  class="btn-remove"
                  onclick="removeItem(<?= $item['id'] ?>, <?= $item['price'] ?>)"
                  aria-label="Remove <?= htmlspecialchars($item['title']) ?> from cart"
                >
                  Remove
                </button>
              </div>
            </div>

          </div><!-- /.cart-card -->
        <?php endforeach; ?>

        <!-- Clear all link -->
        <div class="cart-clear-row">
          <button class="btn-clear-all" onclick="clearAll()">🗑 Clear Cart</button>
        </div>

      </div><!-- /.cart-items -->

      <!-- RIGHT: Order summary -->
      <div class="cart-summary">

        <div class="summary-card">
          <h2 class="summary-title">Order Summary</h2>

          <div class="summary-lines">
            <?php foreach ($cart_items as $item): ?>
              <div class="summary-line" id="summary-line-<?= $item['id'] ?>">
                <span class="summary-item-name"><?= htmlspecialchars($item['title']) ?></span>
                <span class="summary-item-price">₱<?= number_format($item['price']) ?></span>
              </div>
            <?php endforeach; ?>
          </div>

          <div class="summary-divider"></div>

          <div class="summary-total">
            <span>Total</span>
            <span id="totalPrice">₱<?= number_format($subtotal) ?></span>
          </div>

          <p class="summary-note">
            💡 Prices are set by individual sellers.
            Agree on final payment when you meet up.
          </p>

          <!-- Message all sellers -->
          <a href="/messages.php" class="btn-checkout">
            Message All Sellers
          </a>

          <a href="/browse.php" class="btn-browse-more">
            + Add More Items
          </a>
        </div>

        <!-- Safety tip card -->
        <div class="safety-card">
          <h4>🔒 Stay Safe</h4>
          <ul>
            <li>Always meet in a public campus spot</li>
            <li>Inspect the item before paying</li>
            <li>Never send money in advance</li>
            <li>Report suspicious listings</li>
          </ul>
        </div>

      </div><!-- /.cart-summary -->

    </div><!-- /.cart-layout -->

  <?php endif; ?>

</div><!-- /.cart-page -->

<script>
  // Keep a running total so we can update it when items are removed
  let cartTotal = <?= $subtotal ?>;

  // ── Remove a single item ──────────────────────
  function removeItem(id, price) {
    const card    = document.getElementById('cart-item-' + id);
    const sumLine = document.getElementById('summary-line-' + id);

    if (!card) return;

    // Fade out and remove the card
    card.style.transition = 'opacity 0.3s, transform 0.3s';
    card.style.opacity    = '0';
    card.style.transform  = 'translateX(20px)';

    setTimeout(() => {
      card.remove();
      if (sumLine) sumLine.remove();

      // Update total
      cartTotal -= price;
      document.getElementById('totalPrice').textContent =
        '₱' + cartTotal.toLocaleString();

      // Count remaining items
      const remaining = document.querySelectorAll('.cart-card').length;
      updateItemCount(remaining);

      // Show empty state if no items left
      if (remaining === 0) showEmptyState();
    }, 300);
  }

  // ── Clear all items ───────────────────────────
  function clearAll() {
    const cards = document.querySelectorAll('.cart-card');
    cards.forEach((card, i) => {
      setTimeout(() => {
        card.style.transition = 'opacity 0.3s, transform 0.3s';
        card.style.opacity    = '0';
        card.style.transform  = 'translateX(20px)';
        setTimeout(() => card.remove(), 300);
      }, i * 80); // stagger the removals
    });

    setTimeout(() => showEmptyState(), cards.length * 80 + 350);
  }

  // ── Update the "X items saved" counter ───────
  function updateItemCount(count) {
    const header = document.querySelector('.cart-header p');
    if (header) {
      header.textContent = count + ' item' + (count !== 1 ? 's' : '') + ' saved';
    }
  }

  // ── Replace layout with the empty state ──────
  function showEmptyState() {
    const layout = document.querySelector('.cart-layout');
    if (!layout) return;

    layout.innerHTML = `
      <div class="cart-empty">
        <div class="empty-icon">🛒</div>
        <h2>Your cart is empty</h2>
        <p>Browse listings and save items you want to buy.</p>
        <a href="/browse.php" class="btn-browse">Browse Listings</a>
      </div>`;

    updateItemCount(0);
  }
</script>

<?php include 'includes/footer.php'; ?>