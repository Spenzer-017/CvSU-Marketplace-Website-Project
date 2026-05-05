<!-- PHP Logic (Authentication, Session, etc.) -->
<?php
  /*
    listing.php - Individual Item Listing Page
    Shows full item details, seller info, comments, and actions.
  */

  session_start();

  require_once "includes/db.php";

  /* $loggedInUser is the session array - we avoid naming it $user to prevent
    accidental collision with the $user DB row fetched below */
  $loggedInUser = $_SESSION['user'] ?? null;
?>

<!-- Database Query -->
<?php
  // Get and validate item id from URL
  $item_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

  if ($item_id <= 0) {
    header('Location: browse.php');
    exit;
  }

  // Fetch the item joined with seller info and category name
  $stmt = $pdo->prepare("
    SELECT
      items.*,
      categories.name AS category,
      users.id AS seller_id,
      users.name AS seller_name,
      users.avatar AS seller_avatar,
      users.course AS seller_course,
      users.bio AS seller_bio
    FROM items
    JOIN categories ON items.category_id = categories.category_id
    JOIN users ON items.seller_id = users.id
    WHERE items.item_id = ?
    LIMIT 1
  ");
  $stmt->execute([$item_id]);
  $item = $stmt->fetch();

  // Redirect to browse if item not found
  if (!$item) {
    header('Location: browse.php');
    exit;
  }

  // Handle delete listing
  if ( $loggedInUser && isset($_POST['delete_item_id']) && (int)$loggedInUser['id'] === (int)$item['seller_id']) {
      $delete_id = (int)$_POST['delete_item_id'];

      require_once "includes/delete-item.php";
      deleteItemWithImage($pdo, $delete_id, (int)$loggedInUser['id']);

      header("Location: my-listings.php");
      exit;
  }

  // Increment view count - skip if the viewer is the seller or its the same viewer
  if (!isset($_SESSION['viewed_items'])) {
    $_SESSION['viewed_items'] = [];
  }

  if (
    (!$loggedInUser || (int)$loggedInUser['id'] !== (int)$item['seller_id']) &&
    !in_array($item_id, $_SESSION['viewed_items'])
  ) {
    $pdo->prepare("UPDATE items SET views = views + 1 WHERE item_id = ?")->execute([$item_id]);

    $_SESSION['viewed_items'][] = $item_id;
  }

  // Fetch the current transaction for this item (only one pending allowed)
  $stmt = $pdo->prepare("
    SELECT transactions.*, users.name AS buyer_name
    FROM transactions
    JOIN users ON transactions.buyer_id = users.id
    WHERE transactions.item_id = ?
    ORDER BY
      CASE status WHEN 'pending' THEN 0 WHEN 'completed' THEN 1 ELSE 2 END,
      transactions.created_at DESC
    LIMIT 1
  ");
  $stmt->execute([$item_id]);
  $transaction = $stmt->fetch();

  $txn_status = $transaction ? $transaction['status'] : null;
  $txn_buyer_id = $transaction ? (int)$transaction['buyer_id'] : null;
  $txn_id = $transaction ? (int)$transaction['transaction_id'] : null;

  // Check if item is saved to wishlist
  $is_saved = false;
  if ($loggedInUser) {
    $stmt = $pdo->prepare("SELECT 1 FROM saved_items WHERE user_id = ? AND item_id = ? LIMIT 1");
    $stmt->execute([(int)$loggedInUser['id'], $item_id]);
    $is_saved = (bool)$stmt->fetch();
  }

  // Handle save / unsave POST
  if ($loggedInUser && isset($_POST['toggle_save'])) {
    if ($is_saved) {
      $pdo->prepare("DELETE FROM saved_items WHERE user_id = ? AND item_id = ?")->execute([(int)$loggedInUser['id'], $item_id]);
      $is_saved = false;
    } else {
      $pdo->prepare("INSERT IGNORE INTO saved_items (user_id, item_id) VALUES (?, ?)")->execute([(int)$loggedInUser['id'], $item_id]);
      $is_saved = true;
    }
  }

  // Handle comment submission
  $comment_error = '';
  $comment_success = false;
  if ($loggedInUser && isset($_POST['submit_comment'])) {
    $comment_text = trim($_POST['comment'] ?? '');
    if ($comment_text === '') {
      $comment_error = 'Comment cannot be empty.';
    } elseif (strlen($comment_text) > 1000) {
      $comment_error = 'Comment must be 1000 characters or less.';
    } else {
      $pdo->prepare("INSERT INTO comments (commenter_id, item_id, comment) VALUES (?, ?, ?)")->execute([(int)$loggedInUser['id'], $item_id, $comment_text]);
      $comment_success = true;
    }
  }

  // Fetch comments with commenter info, newest first
  $stmt = $pdo->prepare("
    SELECT comments.comment, comments.created_at, users.name AS commenter_name, users.avatar AS commenter_avatar
    FROM comments
    JOIN users ON comments.commenter_id = users.id
    WHERE comments.item_id = ?
    ORDER BY comments.created_at DESC
  ");
  $stmt->execute([$item_id]);
  $comments = $stmt->fetchAll();

  // Fetch up to 4 other active listings from the same seller
  $stmt = $pdo->prepare("
    SELECT items.item_id, items.title, items.price, items.image_path, categories.name AS category
    FROM items
    JOIN categories ON items.category_id = categories.category_id
    WHERE items.seller_id = ? AND items.item_id != ? AND items.status = 'active'
    ORDER BY items.created_at DESC
    LIMIT 4
  ");
  $stmt->execute([(int)$item['seller_id'], $item_id]);
  $seller_listings = $stmt->fetchAll();

  $is_own_listing = $loggedInUser && (int)$loggedInUser['id'] === (int)$item['seller_id'];
  $is_buyer_in_txn = $loggedInUser && $txn_buyer_id === (int)$loggedInUser['id'];
?>

<!-- PHP UI/UX Logic -->
<?php
  $activePage = '';
  include "includes/header.php";
?>

<div class="listing-page">

  <a href="browse.php" class="btn-back">Back to Browse</a>

  <div class="listing-layout">

    <!-- Left: Item details -->
    <div class="listing-main">

      <!-- Item image -->
      <div class="listing-image-box">
        <?php
          $imgFile = "uploads/" . $item['image_path'];
          if (!empty($item['image_path']) && file_exists($imgFile)):
        ?>
          <img src="<?= htmlspecialchars($imgFile) ?>" alt="<?= htmlspecialchars($item['title']) ?>" />
        <?php else: ?>
          <div class="listing-no-image"><?= $imgNotAvailableIcon ?></div>
        <?php endif; ?>

        <span class="listing-condition-badge"><?= htmlspecialchars($item['condition_type']) ?></span>

        <?php if ($item['status'] === 'sold'): ?>
          <div class="listing-sold-overlay">SOLD</div>
        <?php elseif ($txn_status === 'pending'): ?>
          <div class="listing-reserved-overlay">RESERVED</div>
        <?php endif; ?>
      </div>

      <!-- Item info card -->
      <div class="listing-info-card">

        <div class="listing-category-tag"><?= htmlspecialchars($item['category']) ?></div>
        <h1 class="listing-title"><?= htmlspecialchars($item['title']) ?></h1>
        <div class="listing-price">&#8369;<?= number_format($item['price'], 2) ?></div>

        <div class="listing-meta-row">
          <span><?= $locationIcon ?> &nbsp;<?= htmlspecialchars($item['meetup_location'] ?? 'Not specified') ?></span>
          <span><?= $visibilityOnIcon ?> &nbsp;<?= (int)$item['views']?> views</span>
          <span>Posted <?= date('M j, Y', strtotime($item['created_at'])) ?></span>
        </div>

        <?php if (!empty($item['description'])): ?>
          <div class="listing-description">
            <h3>Description</h3>
            <p><?= nl2br(htmlspecialchars($item['description'])) ?></p>
          </div>
        <?php endif; ?>

        <?php if (!empty($item['contact_info'])): ?>
          <div class="listing-contact">
            <h3>Contact</h3>
            <p><?= htmlspecialchars($item['contact_info']) ?></p>
          </div>
        <?php endif; ?>

      </div>

      <!-- Comments Section -->
      <div class="listing-comments">
        <h3>Comments (<?= count($comments) ?>)</h3>

        <?php if ($comment_error): ?>
          <div class="alert alert-error" style="margin-bottom: 16px;"><?= htmlspecialchars($comment_error) ?></div>
        <?php endif; ?>

        <?php if ($comment_success): ?>
          <div class="alert alert-success" style="margin-bottom: 16px;">Comment posted!</div>
        <?php endif; ?>

        <?php if ($loggedInUser && !$is_own_listing): ?>
          <form method="POST" class="comment-form">
            <textarea name="comment" placeholder="Enter your comments" maxlength="1000" rows="3" required></textarea>
            <span class="form-hint">Max 1000 characters</span>
            <button type="submit" name="submit_comment" class="btn-comment">Post Comment</button>
          </form>
        <?php elseif (!$loggedInUser): ?>
          <p class="comment-login-note">
            <a href="login.php">Log in</a> to ask a question about this listing.
          </p>
        <?php endif; ?>

        <?php if (empty($comments)): ?>
          <p class="no-comments">No comments yet.</p>
        <?php else: ?>
          <div class="comments-list">
            <?php foreach ($comments as $c): ?>
              <div class="comment-item">
                <div class="comment-avatar">
                  <?= strtoupper($c['commenter_name'][0] ?? '?') ?>
                </div>
                <div class="comment-body">
                  <div class="comment-top">
                    <span class="comment-author"><?= htmlspecialchars($c['commenter_name']) ?></span>
                    <span class="comment-time"><?= date('M j, Y', strtotime($c['created_at'])) ?></span>
                  </div>
                  <p class="comment-text"><?= nl2br(htmlspecialchars($c['comment'])) ?></p>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

    </div>

    <!-- Right: Sidebar -->
    <div class="listing-sidebar">

      <!-- Action card -->
      <div class="listing-action-card">

        <?php if ($item['status'] === 'sold' && $txn_status !== 'pending'): ?>
          <!-- Item is fully sold, no active transaction -->
          <div class="txn-status-notice txn-notice-sold">
            Item Sold
          </div>

        <?php elseif ($is_own_listing): ?>
          <!-- Seller View -->

          <?php if ($txn_status === 'pending'): ?>
            <!-- Seller: transaction is pending with a buyer -->
            <div class="txn-status-notice txn-notice-pending">
              Transaction Pending
            </div>
            <div class="txn-buyer-info">
              Buyer: <strong><?= htmlspecialchars($transaction['buyer_name']) ?></strong>
            </div>
            <a href="messages.php?to=<?= $txn_buyer_id ?>&item=<?= $item_id ?>" class="btn-message-seller">
              Message Buyer
            </a>
            <!-- Mark as sold - goes to transactions page -->
            <form method="POST" action="transactions.php">
              <input type="hidden" name="transaction_id" value="<?= $txn_id ?>">
              <input type="hidden" name="status" value="completed">
              <button type="submit" name="update_status" class="btn-submit" data-confirm="Mark this transaction as completed? The item will be marked as sold." data-confirm-green>
                Mark as Sold
              </button>
            </form>
            <!-- Seller can cancel -->
            <form method="POST" action="transactions.php">
              <input type="hidden" name="transaction_id" value="<?= $txn_id ?>">
              <input type="hidden" name="status" value="cancelled">
              <button type="submit" name="update_status" class="btn-delete-listing" data-confirm="Cancel this transaction? The item will be available again.">
                Cancel Transaction
              </button>
            </form>

          <?php else: ?>
            <!-- Seller: no pending transaction, normal edit/delete -->
            <a href="edit-listing.php?id=<?= $item_id ?>" class="btn-submit">Edit Listing</a>
            <form method="POST">
              <input type="hidden" name="delete_item_id" value="<?= $item_id ?>">
              <button type="submit" class="btn-delete-listing" data-confirm="Delete this listing? This cannot be undone.">
                Delete Listing
              </button>
            </form>
          <?php endif; ?>

        <?php elseif ($loggedInUser): ?>
          <!-- Buyer View -->

          <?php if ($txn_status === 'pending' && $is_buyer_in_txn): ?>
            <!-- This buyer has a pending transaction for this item -->
            <div class="txn-status-notice txn-notice-pending">
              Your Transaction is Pending
            </div>
            <p class="txn-helper-text">
              You have an active transaction on this item. Go to your transactions to manage it.
            </p>
            <a href="messages.php?to=<?= (int)$item['seller_id'] ?>&item=<?= $item_id ?>" class="btn-message-seller">
              Message Seller
            </a>
            <a href="transactions.php?view=buying&status=pending" class="btn-save">
              View My Transactions
            </a>
            <!-- Buyer can also cancel -->
            <form method="POST" action="transactions.php">
              <input type="hidden" name="transaction_id" value="<?= $txn_id ?>">
              <input type="hidden" name="status" value="cancelled">
              <button type="submit" name="update_status" class="btn-delete-listing" data-confirm="Cancel this transaction?">
                Cancel Transaction
              </button>
            </form>

          <?php elseif ($txn_status === 'pending' && !$is_buyer_in_txn): ?>
            <!-- Another buyer already has a pending transaction -->
            <div class="txn-status-notice txn-notice-reserved">
              Reserved by Another Buyer
            </div>
            <p class="txn-helper-text">
              This item currently has a pending transaction. It may become available again if the transaction falls through.
            </p>
            <!-- Still allow saving -->
            <form method="POST">
              <button type="submit" name="toggle_save" class="btn-save">
                <?= $is_saved ? 'Remove from Saved' : 'Save Item' ?>
              </button>
            </form>

          <?php elseif ($item['status'] === 'active'): ?>
            <!-- Item is free, buyer can message -->
            <a href="messages.php?to=<?= (int)$item['seller_id'] ?>&item=<?= $item_id ?>" class="btn-message-seller">
              Message Seller
            </a>
            <form method="POST">
              <button type="submit" name="toggle_save" class="btn-save">
                <?= $is_saved ? 'Remove from Saved' : 'Save Item' ?>
              </button>
            </form>

          <?php else: ?>
            <!-- Item sold, no active transaction for this buyer -->
            <div class="txn-status-notice txn-notice-sold">
              Item Sold
            </div>
          <?php endif; ?>

        <?php else: ?>
          <!-- Guest View -->
          <a href="login.php" class="btn-submit">Log In to Buy</a>
          <p class="action-login-note">You need an account to message sellers or save items.</p>
        <?php endif; ?>

      </div>

      <!-- Seller info card -->
      <div class="seller-card">
        <h3 class="card-title">Seller</h3>
        <div class="seller-info">
          <div class="seller-avatar">
            <?php if (!empty($item['seller_avatar'])): ?>
              <img src="assets/img/<?= htmlspecialchars($item['seller_avatar']) ?>.png" alt="<?= htmlspecialchars($item['seller_name']) ?>" class="avatar-pixel-img"/>
            <?php else: ?>
              <?= strtoupper($item['seller_name'][0] ?? '?') ?>
            <?php endif; ?>
          </div>
          <div>
            <div class="seller-name"><?= htmlspecialchars($item['seller_name']) ?></div>
            <?php if (!empty($item['seller_course'])): ?>
              <div class="seller-course"><?= htmlspecialchars($item['seller_course']) ?></div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Other listings by same seller -->
      <?php if (!empty($seller_listings)): ?>
        <div class="seller-other-listings">
          <h3 class="card-title">More from this Seller</h3>
          <?php foreach ($seller_listings as $other): ?>
            <a href="listing.php?id=<?= (int)$other['item_id'] ?>" class="other-listing-item">
              <div class="other-listing-img">
                <?php
                  $otherImg = "uploads/" . $other['image_path'];
                  if (!empty($other['image_path']) && file_exists($otherImg)):
                ?>
                  <img src="<?= htmlspecialchars($otherImg) ?>" alt="<?= htmlspecialchars($other['title']) ?>" />
                <?php else: ?>
                  <?= $imgNotAvailableIcon ?>
                <?php endif; ?>
              </div>
              <div class="other-listing-info">
                <div class="other-listing-title"><?= htmlspecialchars($other['title']) ?></div>
                <div class="other-listing-price">&#8369;<?= number_format($other['price'], 2) ?></div>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

    </div>

  </div>

</div>

<?php include 'includes/footer.php'; ?>