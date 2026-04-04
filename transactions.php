<!-- PHP Logic (Authentication, Session, etc.) -->
<?php
  /*
    transactions.php - Transaction Management
    Buyers see their purchases, sellers see their sales.
    Both can update transaction status.
  */

  session_start();

  require_once "includes/db.php";

  if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
  }

  $loggedInUser = $_SESSION['user'];
  $uid = (int)$loggedInUser['id'];
?>

<!-- PHP UI/UX Logic -->
<?php
  $activePage = 'transactions';
  include "includes/header.php";
?>

<!-- Database Query -->
<?php 
  // Handle status update POST
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $txn_id = (int)($_POST['transaction_id'] ?? 0);
    $newStatus = trim($_POST['status'] ?? '');
    $allowed = ['completed', 'cancelled'];

    if ($txn_id > 0 && in_array($newStatus, $allowed)) {
      // Only allow the buyer or seller of this transaction to update it
      $stmt = $pdo->prepare("
        SELECT * FROM transactions WHERE transaction_id = ? AND (buyer_id = ? OR seller_id = ?)
      ");
      $stmt->execute([$txn_id, $uid, $uid]);
      $txn = $stmt->fetch();

      if ($txn) {
        if ($newStatus === 'completed') {
          // Mark transaction completed and set completed_at timestamp
          $pdo->prepare("
            UPDATE transactions SET status = 'completed', completed_at = NOW()
            WHERE transaction_id = ?
          ")->execute([$txn_id]);

          // Mark the item as sold
          $pdo->prepare("
            UPDATE items SET status = 'sold' WHERE item_id = ?
          ")->execute([$txn['item_id']]);

        } elseif ($newStatus === 'cancelled') {
          $pdo->prepare("
            UPDATE transactions SET status = 'cancelled' WHERE transaction_id = ?
          ")->execute([$txn_id]);
        }
      }
    }

    header("Location: transactions.php");
    exit;
  }

  // Handle create transaction POST (seller creates when deal is agreed)
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_transaction'])) {
    $item_id  = (int)($_POST['item_id']  ?? 0);
    $buyer_id = (int)($_POST['buyer_id'] ?? 0);

    if ($item_id > 0 && $buyer_id > 0) {
      // Verify this user is the seller of that item
      $stmt = $pdo->prepare("SELECT * FROM items WHERE item_id = ? AND seller_id = ? AND status = 'active'");
      $stmt->execute([$item_id, $uid]);
      $item = $stmt->fetch();

      if ($item) {
        // Check no pending transaction already exists for this item + buyer
        $stmt = $pdo->prepare("
          SELECT 1 FROM transactions
          WHERE item_id = ? AND buyer_id = ? AND status = 'pending'
          LIMIT 1
        ");
        $stmt->execute([$item_id, $buyer_id]);

        if (!$stmt->fetch()) {
          $pdo->prepare("
            INSERT INTO transactions (buyer_id, seller_id, item_id, amount, status)
            VALUES (?, ?, ?, ?, 'pending')
          ")->execute([$buyer_id, $uid, $item_id, $item['price']]);
        }
      }
    }

    header("Location: transactions.php");
    exit;
  }

  // View toggle: am I looking at my purchases or my sales?
  $view = $_GET['view'] ?? 'buying';
  if (!in_array($view, ['buying', 'selling'])) $view = 'buying';

  // Filter by status
  $status_filter = $_GET['status'] ?? 'all';
  $allowed_statuses = ['all', 'pending', 'completed', 'cancelled'];
  if (!in_array($status_filter, $allowed_statuses)) $status_filter = 'all';

  // Build query
  if ($view === 'buying') {
    $where = $status_filter === 'all' ? "t.buyer_id = ?" : "t.buyer_id = ? AND t.status = ?";
    $params = $status_filter === 'all' ? [$uid] : [$uid, $status_filter];
  } else {
    $where = $status_filter === 'all' ? "t.seller_id = ?" : "t.seller_id = ? AND t.status = ?";
    $params = $status_filter === 'all' ? [$uid] : [$uid, $status_filter];
  }

  $stmt = $pdo->prepare("
    SELECT
      t.*,
      i.title AS item_title, i.image_path AS item_image, i.meetup_location,
      buyer.name  AS buyer_name,
      seller.name AS seller_name
    FROM transactions t
    JOIN items i ON t.item_id = i.item_id
    JOIN users buyer  ON t.buyer_id  = buyer.id
    JOIN users seller ON t.seller_id = seller.id
    WHERE $where
    ORDER BY t.created_at DESC
  ");
  $stmt->execute($params);
  $transactions = $stmt->fetchAll();

  // Count per status for the tabs
  if ($view === 'buying') {
    $countStmt = $pdo->prepare("SELECT status, COUNT(*) AS cnt FROM transactions WHERE buyer_id = ? GROUP BY status");
  } else {
    $countStmt = $pdo->prepare("SELECT status, COUNT(*) AS cnt FROM transactions WHERE seller_id = ? GROUP BY status");
  }
  $countStmt->execute([$uid]);
  $counts = ['all' => 0, 'pending' => 0, 'completed' => 0, 'cancelled' => 0];
  foreach ($countStmt->fetchAll() as $row) {
    $counts[$row['status']] = (int)$row['cnt'];
    $counts['all'] += (int)$row['cnt'];
  }
?>

<div class="transactions-page">

  <div class="transactions-header">
    <div>
      <h1>Transactions</h1>
      <p>Track your buying and selling activity.</p>
    </div>
  </div>

  <!-- Buying / Selling toggle -->
  <div class="txn-view-toggle">
    <a href="?view=buying&status=<?= $status_filter ?>" class="txn-view-btn <?= $view === 'buying' ? 'active' : '' ?>">My Purchases</a>
    <a href="?view=selling&status=<?= $status_filter ?>" class="txn-view-btn <?= $view === 'selling' ? 'active' : '' ?>">My Sales</a>
  </div>

  <!-- Status filter tabs -->
  <div class="listings-filter-tabs">
    <?php foreach (['all' => 'All', 'pending' => 'Pending', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $key => $label): ?>
      <a href="?view=<?= $view ?>&status=<?= $key ?>" class="filter-tab <?= $status_filter === $key ? 'active' : '' ?>">
        <?= $label ?> (<?= $counts[$key] ?>)
      </a>
    <?php endforeach; ?>
  </div>

  <?php if (empty($transactions)): ?>
    <div class="empty-state" style="padding: 64px 24px;">
      <h3>No transactions found</h3>
      <p><?= $view === 'buying' ? "You haven't made any purchases yet." : "You don't have any sales yet." ?></p>
    </div>

  <?php else: ?>
    <div class="transactions-list">
      <?php foreach ($transactions as $txn): ?>
        <div class="txn-card">

          <!-- Item info -->
          <div class="txn-item-info">
            <div class="txn-item-img">
              <?php
                $txnImg = "uploads/" . $txn['item_image'];
                if (!empty($txn['item_image']) && file_exists($txnImg)):
              ?>
                <img src="<?= htmlspecialchars($txnImg) ?>" alt="" />
              <?php else: ?>
                <?= $imgNotAvailableIcon ?>
              <?php endif; ?>
            </div>
            <div>
              <a href="listing.php?id=<?= (int)$txn['item_id'] ?>" class="txn-item-title">
                <?= htmlspecialchars($txn['item_title']) ?>
              </a>
              <div class="txn-parties">
                <?php if ($view === 'buying'): ?>
                  Seller: <strong><?= htmlspecialchars($txn['seller_name']) ?></strong>
                <?php else: ?>
                  Buyer: <strong><?= htmlspecialchars($txn['buyer_name']) ?></strong>
                <?php endif; ?>
              </div>
              <?php if (!empty($txn['meetup_location'])): ?>
                <div class="txn-meetup"><?= $locationIcon ?> <?= htmlspecialchars($txn['meetup_location']) ?></div>
              <?php endif; ?>
            </div>
          </div>

          <!-- Amount & status -->
          <div class="txn-status-col">
            <div class="txn-amount">&#8369;<?= number_format($txn['amount'], 2) ?></div>
            <span class="txn-badge txn-badge-<?= $txn['status'] ?>">
              <?= ucfirst($txn['status']) ?>
            </span>
            <div class="txn-date"><?= date('M j, Y', strtotime($txn['created_at'])) ?></div>
            <?php if ($txn['completed_at']): ?>
              <div class="txn-date">Completed: <?= date('M j, Y', strtotime($txn['completed_at'])) ?></div>
            <?php endif; ?>
          </div>

          <!-- Action buttons (only on pending transactions) -->
          <?php if ($txn['status'] === 'pending'): ?>
            <div class="txn-actions">

              <?php if ($view === 'selling'): ?>
                <!-- Seller can mark as completed -->
                <form method="POST">
                  <input type="hidden" name="transaction_id" value="<?= (int)$txn['transaction_id'] ?>">
                  <input type="hidden" name="status" value="completed">
                  <button type="submit" name="update_status" class="btn-txn-complete" onclick="return confirm('Mark this deal as completed? The item will be marked as sold.')">
                    Mark as Sold
                  </button>
                </form>
              <?php endif; ?>

              <!-- Both buyer and seller can cancel -->
              <form method="POST">
                <input type="hidden" name="transaction_id" value="<?= (int)$txn['transaction_id'] ?>">
                <input type="hidden" name="status" value="cancelled">
                <button type="submit" name="update_status" class="btn-txn-cancel" onclick="return confirm('Cancel this transaction?')">
                  Cancel
                </button>
              </form>

              <!-- Message link -->
              <?php
                $other_id = $view === 'buying' ? (int)$txn['seller_id'] : (int)$txn['buyer_id'];
              ?>
              <a href="messages.php?to=<?= $other_id ?>&item=<?= (int)$txn['item_id'] ?>" class="btn-txn-msg">
                Message
              </a>

            </div>
          <?php endif; ?>

        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>