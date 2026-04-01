<!-- PHP Logic (Authentication, Session, etc.) -->
<?php
  /*
    cart.php — Shopping Cart
    Shows items the user has saved/reserved to buy.
  */

  session_start();

  // Forced user login (Remove/change later on for authentication)
  $_SESSION['user'] = ["name" => "Spenzer", 'course' => 'BS Computer Science', "id" => 17];
  $user = $_SESSION['user'] ?? null;
?>

<!-- PHP UI/UX Logic -->
<?php 
  $activePage = "cart"; 
  include "includes/header.php";
?>

<!-- PHP Database Query -->
<?php 
  // Mock cart items (replace with DB query later)
  $cart_items = [
    [
      'id' => 1654,
      'title' => 'CvSU ID Lace',
      'category' => 'Supplies',
      'condition' => 'Good',
      'price' => 80,
      'seller' => 'Charlie',
      'location' => 'CEIT',
      'img' => $imgNotAvailableIcon,
    ],
    [
      'id' => 2123,
      'title' => 'Iphone 67 Pro Max Fully Paid',
      'category' => 'Electronics',
      'condition' => 'New',
      'price' => 6700,
      'seller' => 'Jedhorse',
      'location' => 'DIT',
      'img' => $imgNotAvailableIcon,
    ],
    [
      'id' => 3980,
      'title' => 'CvSU Uniform M (Large)',
      'category' => 'Clothing',
      'condition' => 'Good',
      'price' => 300,
      'seller' => 'Blessie',
      'location' => 'CAS',
      'img' => $imgNotAvailableIcon,
    ],
  ];

  $subtotal = array_sum(array_column($cart_items, 'price'));
?>

<div class="cart-page">

  <!-- Page header -->
  <div class="cart-header">
    <div>
      <h1>My Cart</h1>
      <p><?= count($cart_items) ?> item<?= count($cart_items) !== 1 ? 's' : '' ?> saved</p>
    </div>
    <a href="browse.php" class="btn-back">< Continue Browsing</a>
  </div>

  <?php if (empty($cart_items)): ?>
    <!-- Empty cart state -->
    <div class="cart-empty">
      <div class="empty-icon"><?= $cartIcon ?></div>
      <h2>Your cart is empty</h2>
      <p>Browse listings and save items you want to buy.</p>
      <a href="browse.php" class="btn-browse">Browse Listings</a>
    </div>

  <?php else: ?>
    <div class="cart-layout">

      <!-- Left: Cart items list -->
      <div class="cart-items">

        <?php foreach ($cart_items as $item): ?>
          <div class="cart-card" id="cart-item-<?= $item['id'] ?>">

            <!-- Cart Item Image -->
            <div class="cart-img"><?= $item['img'] ?></div>

            <!-- Item details -->
            <div class="cart-info">
              <div class="cart-category"><?= htmlspecialchars($item['category']) ?></div>
              <a href="listing.php?id=<?= $item['id'] ?>" class="cart-title">
                <?= htmlspecialchars($item['title']) ?>
              </a>
              <div class="cart-meta">
                <span class="badge badge-condition"><?= htmlspecialchars($item['condition']) ?></span>
                <span class="cart-seller"><?= $userIcon ?>&nbsp;<?= htmlspecialchars($item['seller']) ?></span>
                <span class="cart-location"><?= $locationIcon ?>&nbsp;<?= htmlspecialchars($item['location']) ?></span>
              </div>
            </div>

            <!-- Price & actions -->
            <div class="cart-actions">
              <div class="cart-price">₱<?= number_format($item['price']) ?></div>
              <div class="cart-buttons">
                <a href="messages.php?seller=<?= urlencode($item['seller']) ?>&item=<?= $item['id'] ?>" class="btn-message">
                  Message Seller
                </a>
                <button class="btn-remove" onclick="removeItem(<?= $item['id'] ?>, <?= $item['price'] ?>)" aria-label="Remove <?= htmlspecialchars($item['title']) ?> from cart">
                  Remove
                </button>
              </div>
            </div>

          </div>
        <?php endforeach; ?>
        
        <!-- Clear all link -->
        <div class="cart-clear-row">
          <button class="btn-clear-all" onclick="clearAll()">Clear Cart</button>
        </div>

      </div>

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

          <!-- Message all sellers -->
          <a href="messages.php" class="btn-checkout">
            Message All Sellers
          </a>

          <a href="browse.php" class="btn-browse-more">
            + Add More Items
          </a>
        </div>

      </div>

    </div>

  <?php endif; ?>

</div>

<script>
  // Keep a running total so we can update it when items are removed
  let cartTotal = <?= $subtotal ?>;

  // Remove a single item
  function removeItem(id, price) {
    const card = document.getElementById('cart-item-' + id);
    const sumLine = document.getElementById('summary-line-' + id);

    if (!card) return;

    // Fade out animation and remove the card
    card.style.transition = 'opacity 0.3s, transform 0.3s';
    card.style.opacity = '0';
    card.style.transform = 'translateX(20px)';

    setTimeout(() => {
      card.remove();
      if (sumLine) sumLine.remove();

      // Update total
      cartTotal -= price;
      document.getElementById('totalPrice').textContent = '₱' + cartTotal.toLocaleString();

      // Count remaining items
      const remaining = document.querySelectorAll('.cart-card').length;
      updateItemCount(remaining);

      // Show empty state if no items left
      if (remaining === 0) showEmptyState();
    }, 300);
  }

  // Clear all items
  function clearAll() {
    const cards = document.querySelectorAll('.cart-card');
    cards.forEach((card, i) => {
      setTimeout(() => {
        card.style.transition = 'opacity 0.3s, transform 0.3s';
        card.style.opacity = '0';
        card.style.transform  = 'translateX(20px)';
        setTimeout(() => card.remove(), 300);
      }, i * 80); // stagger the removals
    });

    setTimeout(() => showEmptyState(), cards.length * 80 + 350);
  }

  // Update the "X items saved" counter
  function updateItemCount(count) {
    const header = document.querySelector('.cart-header p');
    if (header) {
      header.textContent = count + ' item' + (count !== 1 ? 's' : '') + ' saved';
    }
  }

  // Replace layout with the empty state
  const cartIcon = <?= json_encode($cartIcon) ?>;

  function showEmptyState() {
    const page = document.querySelector('.cart-page');
    if (!page) return;

    page.innerHTML = `
      <div class="cart-header">
        <div>
          <h1>My Cart</h1>
          <p>0 items saved</p>
        </div>
        <a href="browse.php" class="btn-back">< Continue Browsing</a>
      </div>

      <div class="cart-empty">
        <div class="empty-icon">${cartIcon}</div>
        <h2>Your cart is empty</h2>
        <p>Browse listings and save items you want to buy.</p>
        <a href="browse.php" class="btn-browse">Browse Listings</a>
      </div>
    `;

    updateItemCount(0);
  }
</script>

<?php include 'includes/footer.php'; ?>