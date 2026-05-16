<?php
  // PHP Logic (Authentication, Session, etc.)
  /*
    listing.php - Individual Item Listing Page
    Shows full item details, seller info, comments, and actions.
  */
  session_start();
  require_once "includes/db.php";

  $loggedInUser = $_SESSION['user'] ?? null;

  // Database Query
  $item_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
  if ($item_id <= 0) {
    header('Location: browse.php'); exit;
  }

  $stmt = $pdo->prepare("
    SELECT items.*, categories.name AS category,
      users.id AS seller_id, users.name AS seller_name,
      users.avatar AS seller_avatar, users.course AS seller_course,
      users.bio AS seller_bio
    FROM items
    JOIN categories ON items.category_id = categories.category_id
    JOIN users ON items.seller_id = users.id
    WHERE items.item_id = ? LIMIT 1
  ");
  $stmt->execute([$item_id]);
  $item = $stmt->fetch();

  if (!$item) {
    header('Location: browse.php'); exit;
  }

  if ($loggedInUser && isset($_POST['delete_item_id']) && (int)$loggedInUser['id'] === (int)$item['seller_id']) {
    $delete_id = (int)$_POST['delete_item_id'];
    require_once "includes/delete-item.php";
    deleteItemWithImage($pdo, $delete_id, (int)$loggedInUser['id']);
    header("Location: my-listings.php"); exit;
  }

  if (!isset($_SESSION['viewed_items'])) $_SESSION['viewed_items'] = [];
  if (
    (!$loggedInUser || (int)$loggedInUser['id'] !== (int)$item['seller_id']) &&
    !in_array($item_id, $_SESSION['viewed_items'])
  ) {
    $pdo->prepare("UPDATE items SET views = views + 1 WHERE item_id = ?")->execute([$item_id]);
    $_SESSION['viewed_items'][] = $item_id;
  }

  $stmt = $pdo->prepare("
    SELECT transactions.*, users.name AS buyer_name
    FROM transactions JOIN users ON transactions.buyer_id = users.id
    WHERE transactions.item_id = ?
    ORDER BY CASE status WHEN 'pending' THEN 0 WHEN 'completed' THEN 1 ELSE 2 END, transactions.created_at DESC
    LIMIT 1
  ");
  $stmt->execute([$item_id]);
  $transaction = $stmt->fetch();
  $txn_status = $transaction ? $transaction['status'] : null;
  $txn_buyer_id = $transaction ? (int)$transaction['buyer_id'] : null;
  $txn_id = $transaction ? (int)$transaction['transaction_id'] : null;
  $is_saved = false;

  if ($loggedInUser) {
    $stmt = $pdo->prepare("SELECT 1 FROM saved_items WHERE user_id = ? AND item_id = ? LIMIT 1");
    $stmt->execute([(int)$loggedInUser['id'], $item_id]);
    $is_saved = (bool)$stmt->fetch();
  }

  if ($loggedInUser && isset($_POST['toggle_save'])) {
    if ($is_saved) {
      $pdo->prepare("DELETE FROM saved_items WHERE user_id = ? AND item_id = ?")->execute([(int)$loggedInUser['id'], $item_id]);
      $is_saved = false;
    } else {
      $pdo->prepare("INSERT IGNORE INTO saved_items (user_id, item_id) VALUES (?, ?)")->execute([(int)$loggedInUser['id'], $item_id]);
      $is_saved = true;
    }
  }

  $comment_error   = '';
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

  $stmt = $pdo->prepare("
    SELECT comments.comment, comments.created_at, users.name AS commenter_name, users.avatar AS commenter_avatar
    FROM comments JOIN users ON comments.commenter_id = users.id
    WHERE comments.item_id = ? ORDER BY comments.created_at DESC
  ");
  $stmt->execute([$item_id]);
  $comments = $stmt->fetchAll();

  $stmt = $pdo->prepare("
    SELECT items.item_id, items.title, items.price, items.image_path, categories.name AS category
    FROM items JOIN categories ON items.category_id = categories.category_id
    WHERE items.seller_id = ? AND items.item_id != ? AND items.status = 'active'
    ORDER BY items.created_at DESC LIMIT 4
  ");
  $stmt->execute([(int)$item['seller_id'], $item_id]);
  $seller_listings = $stmt->fetchAll();

  require_once "includes/reactions.php";
  $reaction_counts = getReactionCounts($pdo, $item_id);
  $user_reaction = $loggedInUser ? getUserReaction($pdo, $item_id, (int)$loggedInUser['id']) : null;

  $is_own_listing = $loggedInUser && (int)$loggedInUser['id'] === (int)$item['seller_id'];
  $is_buyer_in_txn = $loggedInUser && $txn_buyer_id === (int)$loggedInUser['id'];

  // PHP UI/UX Logic
  $activePage = '';
  $pageTitle = "Item Details";
  include "includes/header.php";
?>

<div class="lp-page">

  <a href="browse.php" class="lp-back">
    <svg xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 -960 960 960" width="18px"><path d="M560-240 320-480l240-240 56 56-184 184 184 184-56 56Z"/></svg>
    Back to Browse
  </a>

  <div class="lp-layout">

    <!-- LEFT: Image -->
    <div class="lp-image-col">
      <div class="lp-image-wrap">
        <?php
          $imgFile = "uploads/" . $item['image_path'];
          if (!empty($item['image_path']) && file_exists($imgFile)):
        ?>
          <img src="<?= htmlspecialchars($imgFile) ?>" alt="<?= htmlspecialchars($item['title']) ?>" class="lp-main-img" />
        <?php else: ?>
          <div class="lp-no-image"><?= $imgNotAvailableIcon ?></div>
        <?php endif; ?>

        <span class="lp-condition-badge"><?= htmlspecialchars($item['condition_type']) ?></span>

        <?php if ($item['status'] === 'sold'): ?>
          <div class="lp-overlay lp-overlay--sold">SOLD</div>
        <?php elseif ($txn_status === 'pending'): ?>
          <div class="lp-overlay lp-overlay--reserved">RESERVED</div>
        <?php endif; ?>
      </div>
    </div>

    <!-- RIGHT: All details -->
    <div class="lp-detail-col">

      <!-- Title block -->
      <div class="lp-title-block">
        <span class="lp-category-tag"><?= htmlspecialchars($item['category']) ?></span>
        <h1 class="lp-title"><?= htmlspecialchars($item['title']) ?></h1>
        <div class="lp-price">&#8369;<?= number_format($item['price'], 2) ?></div>
        <div class="lp-meta">
          <span><?= $locationIcon ?><?= htmlspecialchars($item['meetup_location'] ?? 'Not specified') ?></span>
          <span><?= $visibilityOnIcon ?><?= (int)$item['views'] ?> views</span>
          <span>Posted <?= date('M j, Y', strtotime($item['created_at'])) ?></span>
        </div>
      </div>

      <!-- Action buttons row -->
      <div class="lp-actions">

        <?php if ($item['status'] === 'sold' && $txn_status !== 'pending'): ?>
          <div class="lp-status-badge lp-status--sold">Item Sold</div>

        <?php elseif ($is_own_listing): ?>

          <?php if ($txn_status === 'pending'): ?>
            <div class="lp-status-badge lp-status--pending">Transaction Pending</div>
            <div class="lp-txn-buyer">Buyer: <strong><?= htmlspecialchars($transaction['buyer_name']) ?></strong></div>
            <div class="lp-action-btns">
              <a href="messages.php?to=<?= $txn_buyer_id ?>&item=<?= $item_id ?>" class="lp-action-btn lp-action-btn--primary lp-action-btn--grow" title="Message Buyer">
                <?= $messageIcon ?>
                <span>Message Buyer</span>
              </a>
              <form method="POST" action="transactions.php" style="margin:0;flex:1 1 0">
                <input type="hidden" name="transaction_id" value="<?= $txn_id ?>">
                <input type="hidden" name="status" value="completed">
                <button type="submit" name="update_status" class="lp-action-btn lp-action-btn--success" style="width:100%" title="Mark as Sold" data-confirm="Mark this transaction as completed? The item will be marked as sold." data-confirm-green>
                  <?= $circleCheckIcon ?>
                  <span>Mark Sold</span>
                </button>
              </form>
              <form method="POST" action="transactions.php" style="margin:0;width:100%">
                <input type="hidden" name="transaction_id" value="<?= $txn_id ?>">
                <input type="hidden" name="status" value="cancelled">
                <button type="submit" name="update_status" class="lp-action-btn lp-action-btn--danger lp-action-btn--full" title="Cancel Transaction" data-confirm="Cancel this transaction? The item will be available again.">
                  <?= $circleCrossIcon ?>
                  <span>Cancel Transaction</span>
                </button>
              </form>
            </div>

          <?php else: ?>
            <div class="lp-action-btns">
              <a href="edit-listing.php?id=<?= $item_id ?>" class="lp-action-btn lp-action-btn--primary" title="Edit Listing">
                <?= $editIcon ?>
                <span>Edit Listing</span>
              </a>
              <form method="POST" style="margin:0">
                <input type="hidden" name="delete_item_id" value="<?= $item_id ?>">
                <button type="submit" class="lp-action-btn lp-action-btn--danger" title="Delete Listing" data-confirm="Delete this listing? This cannot be undone.">
                  <?= $deleteIcon ?>
                  <span>Delete</span>
                </button>
              </form>
            </div>
          <?php endif; ?>

        <?php elseif ($loggedInUser): ?>

          <?php if ($txn_status === 'pending' && $is_buyer_in_txn): ?>
            <div class="lp-status-badge lp-status--pending">Your Transaction is Pending</div>
            <div class="lp-action-btns">
              <a href="messages.php?to=<?= (int)$item['seller_id'] ?>&item=<?= $item_id ?>" class="lp-action-btn lp-action-btn--primary lp-action-btn--grow" title="Message Seller">
                <?= $messageIcon ?>
                <span>Message Seller</span>
              </a>
              <button type="button" class="lp-action-btn lp-action-btn--ghost" title="Report Listing" disabled>
                <?= $reportIcon ?>
                <span>Report</span>
              </button>
              <form method="POST" action="transactions.php" style="margin:0;width:100%">
                <input type="hidden" name="transaction_id" value="<?= $txn_id ?>">
                <input type="hidden" name="status" value="cancelled">
                <button type="submit" name="update_status" class="lp-action-btn lp-action-btn--danger lp-action-btn--full" title="Cancel" data-confirm="Cancel this transaction?">
                  <?= $circleCrossIcon ?>
                  <span>Cancel Transaction</span>
                </button>
              </form>
            </div>

          <?php elseif ($txn_status === 'pending' && !$is_buyer_in_txn): ?>
            <div class="lp-status-badge lp-status--reserved">Reserved by Another Buyer</div>
            <div class="lp-action-btns">
              <form method="POST" style="margin:0">
                <button type="submit" name="toggle_save" class="lp-action-btn <?= $is_saved ? 'lp-action-btn--saved' : 'lp-action-btn--secondary' ?>" title="<?= $is_saved ? 'Remove from Saved' : 'Save Item' ?>">
                  <?= $bookmarkIcon ?>
                  <span><?= $is_saved ? 'Saved' : 'Save' ?></span>
                </button>
              </form>
              <button type="button" class="lp-action-btn lp-action-btn--ghost" title="Report Listing" disabled>
                <?= $reportIcon ?>
                <span>Report</span>
              </button>
            </div>

          <?php elseif ($item['status'] === 'active'): ?>
            <div class="lp-action-btns">
              <a href="messages.php?to=<?= (int)$item['seller_id'] ?>&item=<?= $item_id ?>" class="lp-action-btn lp-action-btn--primary lp-action-btn--grow" title="Message Seller">
                <?= $messageIcon ?>
                <span>Message</span>
              </a>
              <form method="POST" style="margin:0">
                <button type="submit" name="toggle_save" class="lp-action-btn <?= $is_saved ? 'lp-action-btn--saved' : 'lp-action-btn--secondary' ?>" title="<?= $is_saved ? 'Remove from Saved' : 'Save Item' ?>">
                  <?= $bookmarkIcon ?>
                  <span><?= $is_saved ? 'Saved' : 'Save' ?></span>
                </button>
              </form>
              <button type="button" class="lp-action-btn lp-action-btn--ghost" title="Report Listing" disabled>
                <?= $reportIcon ?>
                <span>Report</span>
              </button>
            </div>

          <?php else: ?>
            <div class="lp-status-badge lp-status--sold">Item Sold</div>
          <?php endif; ?>

        <?php else: ?>
          <div class="lp-action-btns">
            <a href="login.php" class="lp-action-btn lp-action-btn--primary" title="Log in to buy">
              <?= $loginIcon ?>
              <span>Log In to Buy</span>
            </a>
          </div>
          <p class="lp-guest-note">You need an account to message sellers or save items.</p>
        <?php endif; ?>

      </div>

      <!-- Reaction bar -->
      <div class="lp-reaction-section">
        <div class="lp-reaction-bar" data-item-id="<?= $item_id ?>">
          <div class="lp-reaction-trigger">
            <button class="lp-react-btn <?= $user_reaction ? 'lp-react-btn--active lp-react-btn--' . $user_reaction : '' ?>" id="reactionMainBtn" <?= !$loggedInUser ? 'data-guest="true"' : '' ?> aria-label="React">
              <?php if ($user_reaction): ?>
                <div class="lp-react-btn-emoji-wrap">
                  <img src="assets/img/<?= $user_reaction ?>-junimo-emoji.png" alt="<?= ucfirst($user_reaction) ?>" class="lp-react-btn-emoji" />
                </div>
              <?php else: ?>
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#434343"><path d="m305-704 112-145q12-16 28.5-23.5T480-880q18 0 34.5 7.5T543-849l112 145 170 57q26 8 41 29.5t15 47.5q0 12-3.5 24T866-523L756-367l4 164q1 35-23 59t-56 24q-2 0-22-3l-179-50-179 50q-5 2-11 2.5t-11 .5q-32 0-56-24t-23-59l4-165L95-523q-8-11-11.5-23T80-570q0-25 14.5-46.5T135-647l170-57Zm49 69-194 64 124 179-4 191 200-55 200 56-4-192 124-177-194-66-126-165-126 165Zm126 135Z"/></svg>
              <?php endif; ?>
            </button>

            <div class="lp-reaction-picker" id="reactionPicker" role="menu">
              <?php
                $emojis = ['like'=>'Like','heart'=>'Heart','laugh'=>'Laugh','wow'=>'Wow','cry'=>'Cry'];
                foreach ($emojis as $type => $label):
              ?>
                <button class="lp-reaction-opt <?= $user_reaction === $type ? 'lp-reaction-opt--active' : '' ?>" data-type="<?= $type ?>" aria-label="<?= $label ?>" role="menuitem">
                  <div class="lp-reaction-img-wrap">
                    <img src="assets/img/<?= $type ?>-junimo-emoji.png" alt="<?= $label ?>" class="lp-reaction-img" />
                  </div>
                  <span class="lp-reaction-opt-label"><?= $label ?></span>
                </button>
              <?php endforeach; ?>
            </div>
          </div>

          <?php $total_reactions = array_sum($reaction_counts); ?>
          <div class="lp-reaction-counts" id="reactionCounts" <?= $total_reactions === 0 ? 'style="display:none"' : '' ?>>
            <?php foreach ($reaction_counts as $type => $count): ?>
              <?php if ($count > 0): ?>
                <span class="lp-count-item" data-type="<?= $type ?>">
                  <div class="lp-count-emoji-wrap">
                    <img src="assets/img/<?= $type ?>-junimo-emoji.png" alt="<?= ucfirst($type) ?>" class="lp-count-emoji" />
                  </div>
                  <span class="lp-count-num"><?= $count ?></span>
                </span>
              <?php endif; ?>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <!-- Description -->
      <?php if (!empty($item['description'])): ?>
        <div class="lp-section">
          <div class="lp-section-label">Description</div>
          <p class="lp-description-text"><?= nl2br(htmlspecialchars($item['description'])) ?></p>
        </div>
      <?php endif; ?>

      <?php if (!empty($item['contact_info'])): ?>
        <div class="lp-section">
          <div class="lp-section-label">Contact</div>
          <p class="lp-description-text"><?= htmlspecialchars($item['contact_info']) ?></p>
        </div>
      <?php endif; ?>

      <!-- Seller card -->
      <div class="lp-section">
        <div class="lp-section-label">Seller</div>
        <div class="lp-seller-card">
          <div class="lp-seller-avatar">
            <?php if (!empty($item['seller_avatar'])): ?>
              <img src="assets/img/<?= htmlspecialchars($item['seller_avatar']) ?>.png" alt="<?= htmlspecialchars($item['seller_name']) ?>" class="avatar-pixel-img"/>
            <?php else: ?>
              <?= strtoupper($item['seller_name'][0] ?? '?') ?>
            <?php endif; ?>
          </div>
          <div>
            <div class="lp-seller-name"><?= htmlspecialchars($item['seller_name']) ?></div>
            <?php if (!empty($item['seller_course'])): ?>
              <div class="lp-seller-course"><?= htmlspecialchars($item['seller_course']) ?></div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- More from seller -->
      <?php if (!empty($seller_listings)): ?>
        <div class="lp-section">
          <div class="lp-section-label">More from this Seller</div>
          <div class="lp-more-listings">
            <?php foreach ($seller_listings as $other): ?>
              <a href="listing.php?id=<?= (int)$other['item_id'] ?>" class="lp-more-item">
                <div class="lp-more-img">
                  <?php
                    $otherImg = "uploads/" . $other['image_path'];
                    if (!empty($other['image_path']) && file_exists($otherImg)):
                  ?>
                    <img src="<?= htmlspecialchars($otherImg) ?>" alt="<?= htmlspecialchars($other['title']) ?>" />
                  <?php else: ?>
                    <?= $imgNotAvailableIcon ?>
                  <?php endif; ?>
                </div>
                <div class="lp-more-info">
                  <div class="lp-more-title"><?= htmlspecialchars($other['title']) ?></div>
                  <div class="lp-more-price">&#8369;<?= number_format($other['price'], 2) ?></div>
                </div>
              </a>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

      <!-- Comments -->
      <div class="lp-section lp-comments-section">
        <div class="lp-section-label">Comments (<?= count($comments) ?>)</div>

        <?php if ($comment_error): ?>
          <div class="lp-alert lp-alert--error"><?= htmlspecialchars($comment_error) ?></div>
        <?php endif; ?>
        <?php if ($comment_success): ?>
          <div class="lp-alert lp-alert--success">Comment posted!</div>
        <?php endif; ?>

        <?php if ($loggedInUser && !$is_own_listing): ?>
          <form method="POST" class="lp-comment-form">
            <textarea name="comment" placeholder="Write a comment" maxlength="1000" rows="3" required></textarea>
            <div class="lp-comment-form-footer">
              <span class="lp-form-hint">Max 1000 characters</span>
              <button type="submit" name="submit_comment" class="lp-btn-comment">Post Comment</button>
            </div>
          </form>
        <?php elseif (!$loggedInUser): ?>
          <p class="lp-comment-login"><a href="login.php">Log in</a> to leave a comment.</p>
        <?php endif; ?>

        <?php if (empty($comments)): ?>
          <p class="lp-no-comments">No comments yet. Be the first to ask!</p>
        <?php else: ?>
          <div class="lp-comments-list">
            <?php foreach ($comments as $c): ?>
              <div class="lp-comment-item">
                <div class="lp-comment-avatar"><?= strtoupper($c['commenter_name'][0] ?? '?') ?></div>
                <div class="lp-comment-body">
                  <div class="lp-comment-header">
                    <span class="lp-comment-author"><?= htmlspecialchars($c['commenter_name']) ?></span>
                    <span class="lp-comment-time"><?= date('M j, Y', strtotime($c['created_at'])) ?></span>
                  </div>
                  <p class="lp-comment-text"><?= nl2br(htmlspecialchars($c['comment'])) ?></p>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

    </div>
  </div>
</div>

<script>
  (function () {
    const bar = document.querySelector('.lp-reaction-bar');
    if (!bar) return;
    const itemId = bar.dataset.itemId;
    const mainBtn = document.getElementById('reactionMainBtn');
    const picker = document.getElementById('reactionPicker');
    const countsEl = document.getElementById('reactionCounts');
    if (!mainBtn || !picker) return;
    let pickerOpen = false;
    let hoverIntent = null;

    if (mainBtn.dataset.guest === 'true') {
      mainBtn.addEventListener('click', function () { window.location.href = 'login.php'; });
      return;
    }

    function openPicker() {
      picker.classList.add('lp-picker--visible');
      pickerOpen = true;

      // Reset first
      picker.style.left = '';
      picker.style.right = '';
      picker.style.transform = '';

      const rect = picker.getBoundingClientRect();
      const viewportWidth = window.innerWidth;

      if (rect.left < 8) {
        // Clipping on the left
        picker.style.left = '0';
        picker.style.right = 'auto';
        picker.style.transform = 'translateY(0) scale(1)';
      } else if (rect.right > viewportWidth - 8) {
        // Clipping on the right
        picker.style.left = 'auto';
        picker.style.right = '0';
        picker.style.transform = 'translateY(0) scale(1)';
      } else {
        picker.style.transform = 'translateY(0) scale(1)';
      }
    }

    function closePicker() {
      picker.classList.remove('lp-picker--visible');
      pickerOpen = false;
      picker.style.left = '';
      picker.style.right = '';
      picker.style.transform = '';
    }

    mainBtn.addEventListener('mouseenter', function () { hoverIntent = setTimeout(openPicker, 180); });
    mainBtn.addEventListener('mouseleave', function () { clearTimeout(hoverIntent); hoverIntent = setTimeout(closePicker, 220); });
    picker.addEventListener('mouseenter', function () { clearTimeout(hoverIntent); });
    picker.addEventListener('mouseleave', function () { hoverIntent = setTimeout(closePicker, 200); });

    mainBtn.addEventListener('click', function () {
      const cur = mainBtn.dataset.currentReaction;
      if (cur && !pickerOpen) { submitReaction(cur); return; }
      pickerOpen ? closePicker() : openPicker();
    });

    document.addEventListener('click', function (e) { if (!bar.contains(e.target)) closePicker(); });

    picker.querySelectorAll('.lp-reaction-opt').forEach(function (btn) {
      btn.addEventListener('click', function () { submitReaction(btn.dataset.type); closePicker(); });
    });

    function submitReaction(type) {
      const fd = new FormData();
      fd.append('item_id', itemId);
      fd.append('reaction_type', type);
      fetch('api/react.php', { method: 'POST', body: fd })
        .then(function (r) { return r.json(); })
        .then(function (d) { if (!d.error) { updateBtn(d.reaction_type); updateCounts(d.counts); } })
        .catch(function () {});
    }

    function updateBtn(activeType) {
      ['like','heart','laugh','wow','cry'].forEach(function (t) { mainBtn.classList.remove('lp-react-btn--' + t); });
      if (activeType) {
        mainBtn.classList.add('lp-react-btn--active', 'lp-react-btn--' + activeType);
        mainBtn.dataset.currentReaction = activeType;
        mainBtn.innerHTML = '<div class="lp-react-btn-emoji-wrap"><img src="assets/img/' + activeType + '-junimo-emoji.png" alt="' + cap(activeType) + '" class="lp-react-btn-emoji" /></div>';
      } else {
        mainBtn.classList.remove('lp-react-btn--active');
        delete mainBtn.dataset.currentReaction;
        mainBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#434343"><path d="m305-704 112-145q12-16 28.5-23.5T480-880q18 0 34.5 7.5T543-849l112 145 170 57q26 8 41 29.5t15 47.5q0 12-3.5 24T866-523L756-367l4 164q1 35-23 59t-56 24q-2 0-22-3l-179-50-179 50q-5 2-11 2.5t-11 .5q-32 0-56-24t-23-59l4-165L95-523q-8-11-11.5-23T80-570q0-25 14.5-46.5T135-647l170-57Zm49 69-194 64 124 179-4 191 200-55 200 56-4-192 124-177-194-66-126-165-126 165Zm126 135Z"/></svg>';
      }
      picker.querySelectorAll('.lp-reaction-opt').forEach(function (o) {
        o.classList.toggle('lp-reaction-opt--active', o.dataset.type === activeType);
      });
    }

    function updateCounts(counts) {
      const types = ['like','heart','laugh','wow','cry'];
      let total = 0;
      types.forEach(function (type) {
        const count = counts[type] || 0;
        total += count;
        let item = countsEl.querySelector('[data-type="' + type + '"]');
        if (count > 0) {
          if (!item) {
            item = document.createElement('span');
            item.className = 'lp-count-item';
            item.dataset.type = type;
            item.innerHTML = '<div class="lp-count-emoji-wrap"><img src="assets/img/' + type + '-junimo-emoji.png" alt="' + cap(type) + '" class="lp-count-emoji" /></div><span class="lp-count-num">' + count + '</span>';
            countsEl.appendChild(item);
          } else {
            item.querySelector('.lp-count-num').textContent = count;
          }
        } else if (item) {
          item.remove();
        }
      });
      countsEl.style.display = total > 0 ? 'flex' : 'none';
    }

    function cap(s) { return s.charAt(0).toUpperCase() + s.slice(1); }

    const active = picker.querySelector('.lp-reaction-opt--active');
    if (active) mainBtn.dataset.currentReaction = active.dataset.type;
  })();
</script>

<?php include 'includes/footer.php'; ?>