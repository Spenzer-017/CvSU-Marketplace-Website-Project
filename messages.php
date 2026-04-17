<!-- PHP Logic (Authentication, Session, etc.) -->
<?php
  /*
    messages.php - Direct Messaging
    Shows conversation threads between buyers and sellers about items.
    URL parameters: ?to=USER_ID&item=ITEM_ID  (opens or starts a conversation)
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
  $activePage = '';
  include "includes/header.php";
?>

<!-- Database Query -->
<?php
  // Handle sending a message
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $receiver_id = (int)($_POST['receiver_id'] ?? 0);
    $item_id = (int)($_POST['item_id'] ?? 0);
    $message = trim($_POST['message'] ?? '');

    if ($receiver_id > 0 && $item_id > 0 && $message !== '' && strlen($message) <= 1000) {
      $stmt = $pdo->prepare("SELECT seller_id FROM items WHERE item_id = ? LIMIT 1");
      $stmt->execute([$item_id]);
      $item = $stmt->fetch();

      if ($item) {
        $seller_id = (int)$item['seller_id'];
        if (
          ($uid !== $seller_id && $receiver_id === $seller_id) ||
          ($uid === $seller_id && $receiver_id !== $seller_id)
        ) {
          $pdo->prepare("
            INSERT INTO messages (sender_id, receiver_id, item_id, message)
            VALUES (?, ?, ?, ?)
          ")->execute([$uid, $receiver_id, $item_id, $message]);
        }
      }
    }

    header("Location: messages.php?to=$receiver_id&item=$item_id");
    exit;
  }

  // Handle create transaction (seller only, from chat panel)
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_transaction'])) {
    $item_id = (int)($_POST['item_id'] ?? 0);
    $buyer_id = (int)($_POST['buyer_id'] ?? 0);

    if ($item_id > 0 && $buyer_id > 0 && $buyer_id !== $uid) {
      // Use a DB transaction to prevent race conditions / double-submit
      $pdo->beginTransaction();

      try {
        // Verify the logged-in user is the seller and the item is still active
        $stmt = $pdo->prepare("
          SELECT item_id, price FROM items
          WHERE item_id = ? AND seller_id = ? AND status = 'active'
          LIMIT 1
        ");
        $stmt->execute([$item_id, $uid]);
        $item = $stmt->fetch();

        if ($item) {
          // Check no pending transaction exists for this item with any buyer
          $stmt = $pdo->prepare("
            SELECT 1 FROM transactions
            WHERE item_id = ? AND status = 'pending'
            LIMIT 1
          ");
          $stmt->execute([$item_id]);
          $already_pending = $stmt->fetch();

          if (!$already_pending) {
            // Verify a real conversation exists between this seller and this buyer
            $stmt = $pdo->prepare("
              SELECT 1 FROM messages
              WHERE item_id = ?
              AND ((sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?))
              LIMIT 1
            ");
            $stmt->execute([$item_id, $buyer_id, $uid, $uid, $buyer_id]);
            $has_conversation = $stmt->fetch();

            if ($has_conversation) {
              $pdo->prepare("
                INSERT INTO transactions (buyer_id, seller_id, item_id, amount, status)
                VALUES (?, ?, ?, ?, 'pending')
              ")->execute([$buyer_id, $uid, $item_id, $item['price']]);

              // Reserve the item
              $pdo->prepare("
                UPDATE items SET status = 'reserved' WHERE item_id = ? AND status = 'active'
              ")->execute([$item_id]);
            }
          }
        }

        $pdo->commit();

      } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Transaction creation failed: " . $e->getMessage());
      }
    }

    header("Location: messages.php?to=$buyer_id&item=$item_id");
    exit;
  }

  // Get all unique conversations for this user
  $stmt = $pdo->prepare("
    SELECT
      m.item_id,
      CASE WHEN m.sender_id = ? THEN m.receiver_id ELSE m.sender_id END AS other_user_id,
      u.name AS other_user_name,
      u.avatar AS other_user_avatar,
      i.title AS item_title,
      i.image_path AS item_image,
      MAX(m.created_at) AS last_message_time,
      SUM(CASE WHEN m.receiver_id = ? AND m.is_read = 0 THEN 1 ELSE 0 END) AS unread_count,
      (SELECT message FROM messages m2
       WHERE ((m2.sender_id = ? AND m2.receiver_id = other_user_id)
           OR (m2.sender_id = other_user_id AND m2.receiver_id = ?))
       AND m2.item_id = m.item_id
       ORDER BY m2.created_at DESC LIMIT 1) AS last_message
    FROM messages m
    JOIN users u ON u.id = CASE WHEN m.sender_id = ? THEN m.receiver_id ELSE m.sender_id END
    JOIN items i ON i.item_id = m.item_id
    WHERE m.sender_id = ? OR m.receiver_id = ?
    GROUP BY m.item_id, other_user_id
    ORDER BY last_message_time DESC
  ");
  $stmt->execute([$uid, $uid, $uid, $uid, $uid, $uid, $uid]);
  $conversations = $stmt->fetchAll();

  // Active conversation from URL params
  $active_to = isset($_GET['to']) ? (int)$_GET['to'] : 0;
  $active_item = isset($_GET['item']) ? (int)$_GET['item'] : 0;

  // Default to first conversation if none selected
  if ($active_to === 0 && !empty($conversations)) {
    $active_to = (int)$conversations[0]['other_user_id'];
    $active_item = (int)$conversations[0]['item_id'];
  }

  $active_messages = [];
  $active_item_data = null;
  $other_user = null;
  $chat_transaction = null;
  $any_pending_on_item = false;
  $is_seller_in_chat = false;

  if ($active_to > 0 && $active_item > 0) {
    // Mark messages as read
    $pdo->prepare("
      UPDATE messages SET is_read = 1
      WHERE receiver_id = ? AND sender_id = ? AND item_id = ?
    ")->execute([$uid, $active_to, $active_item]);

    // Fetch messages in this thread
    $stmt = $pdo->prepare("
      SELECT m.*, u.name AS sender_name, u.avatar AS sender_avatar
      FROM messages m
      JOIN users u ON u.id = m.sender_id
      WHERE ((m.sender_id = ? AND m.receiver_id = ?)
          OR (m.sender_id = ? AND m.receiver_id = ?))
      AND m.item_id = ?
      ORDER BY m.created_at ASC
    ");
    $stmt->execute([$uid, $active_to, $active_to, $uid, $active_item]);
    $active_messages = $stmt->fetchAll();

    // Fetch item data
    $stmt = $pdo->prepare("
      SELECT item_id, title, price, image_path, status, seller_id
      FROM items WHERE item_id = ?
    ");
    $stmt->execute([$active_item]);
    $active_item_data = $stmt->fetch();

    // Fetch other user info
    $stmt = $pdo->prepare("SELECT id, name, avatar, course FROM users WHERE id = ?");
    $stmt->execute([$active_to]);
    $other_user = $stmt->fetch();

    $is_seller_in_chat = $active_item_data && (int)$active_item_data['seller_id'] === $uid;

    // Determine buyer_id and seller_id for this specific chat
    $chat_seller_id = $active_item_data ? (int)$active_item_data['seller_id'] : 0;
    $chat_buyer_id = ($uid === $chat_seller_id) ? $active_to : $uid;

    // Each chat shows only its own transaction, not someone else's
    $stmt = $pdo->prepare("
      SELECT * FROM transactions
      WHERE item_id = ?
      AND buyer_id = ?
      AND seller_id = ?
      ORDER BY CASE status WHEN 'pending' THEN 0 WHEN 'completed' THEN 1 ELSE 2 END, created_at DESC
      LIMIT 1
    ");
    $stmt->execute([$active_item, $chat_buyer_id, $chat_seller_id]);
    $chat_transaction = $stmt->fetch();

    // The seller should not be able to create a new transaction while one is active elsewhere
    $stmt = $pdo->prepare("
      SELECT 1 FROM transactions
      WHERE item_id = ? AND status = 'pending'
      LIMIT 1
    ");
    $stmt->execute([$active_item]);
    $any_pending_on_item = (bool)$stmt->fetch();
  }
?>

<div class="messages-page">

  <div class="messages-layout">

    <!-- Left: Conversation list -->
    <div class="conversations-panel">
      <div class="conversations-header">
        <h2>Messages</h2>
      </div>

      <?php if (empty($conversations)): ?>
        <p class="no-conversations">No conversations yet.</p>
      <?php else: ?>
        <?php foreach ($conversations as $conv): ?>
          <?php $isActive = (int)$conv['other_user_id'] === $active_to && (int)$conv['item_id'] === $active_item; ?>
          <a href="messages.php?to=<?= (int)$conv['other_user_id'] ?>&item=<?= (int)$conv['item_id'] ?>" class="conversation-item <?= $isActive ? 'active' : '' ?>">

            <div class="conv-avatar">
              <?php if (!empty($conv['other_user_avatar'])): ?>
                <img src="assets/img/<?= htmlspecialchars($conv['other_user_avatar']) ?>.png" alt="" class="avatar-pixel-img" />
              <?php else: ?>
                <?= strtoupper($conv['other_user_name'][0] ?? '?') ?>
              <?php endif; ?>
            </div>

            <div class="conv-info">
              <div class="conv-top">
                <span class="conv-name"><?= htmlspecialchars($conv['other_user_name']) ?></span>
                <span class="conv-time"><?= date('M j', strtotime($conv['last_message_time'])) ?></span>
              </div>
              <div class="conv-item-title"><?= htmlspecialchars($conv['item_title']) ?></div>
              <div class="conv-preview"><?= htmlspecialchars($conv['last_message'] ?? '') ?></div>
            </div>

            <?php if ((int)$conv['unread_count'] > 0): ?>
              <div class="conv-unread-badge"><?= (int)$conv['unread_count'] ?></div>
            <?php endif; ?>

          </a>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <!-- Right: Active conversation -->
    <div class="chat-panel">

      <?php if (!$other_user || !$active_item_data): ?>
        <div class="chat-empty">
          <p>Select a conversation to start messaging.</p>
        </div>

      <?php else: ?>

        <!-- Chat header -->
        <div class="chat-header">
          <div class="chat-header-user">
            <div class="conv-avatar">
              <?php if (!empty($other_user['avatar'])): ?>
                <img src="assets/img/<?= htmlspecialchars($other_user['avatar']) ?>.png" alt="" class="avatar-pixel-img" />
              <?php else: ?>
                <?= strtoupper($other_user['name'][0] ?? '?') ?>
              <?php endif; ?>
            </div>
            <div>
              <div class="chat-header-name"><?= htmlspecialchars($other_user['name']) ?></div>
              <?php if (!empty($other_user['course'])): ?>
                <div class="chat-header-course"><?= htmlspecialchars($other_user['course']) ?></div>
              <?php endif; ?>
            </div>
          </div>

          <!-- Item reference -->
          <a href="listing.php?id=<?= (int)$active_item_data['item_id'] ?>" class="chat-item-ref">
            <div class="chat-item-img">
              <?php
                $chatImg = "uploads/" . $active_item_data['image_path'];
                if (!empty($active_item_data['image_path']) && file_exists($chatImg)):
              ?>
                <img src="<?= htmlspecialchars($chatImg) ?>" alt="" />
              <?php else: ?>
                <?= $imgNotAvailableIcon ?>
              <?php endif; ?>
            </div>
            <div>
              <div class="chat-item-title"><?= htmlspecialchars($active_item_data['title']) ?></div>
              <div class="chat-item-price">&#8369;<?= number_format($active_item_data['price'], 2) ?></div>
            </div>
          </a>
        </div>

        <!-- Transaction bar -->
        <?php
          $chat_txn_status = $chat_transaction ? $chat_transaction['status'] : null;
          $can_create_txn = $is_seller_in_chat
            && $active_item_data['status'] === 'active'
            && !$any_pending_on_item
            && $chat_txn_status !== 'pending'
            && $chat_txn_status !== 'completed';

          $show_txn_bar = $chat_txn_status || $can_create_txn
            || ($is_seller_in_chat && $any_pending_on_item && $chat_txn_status === null);
        ?>

        <?php if ($show_txn_bar): ?>
          <div class="chat-txn-bar">

            <?php if ($chat_txn_status === 'pending'): ?>
              <!-- This chat has an active pending transaction -->
              <div class="chat-txn-status chat-txn-pending">
                Transaction Pending
              </div>
              <?php if ($is_seller_in_chat): ?>
                <div class="chat-txn-actions">
                  <form method="POST" action="transactions.php">
                    <input type="hidden" name="transaction_id" value="<?= (int)$chat_transaction['transaction_id'] ?>">
                    <input type="hidden" name="status" value="completed">
                    <button type="submit" name="update_status" class="btn-chat-txn-complete" data-confirm="Mark as sold?" data-confirm-green>
                      Mark as Sold
                    </button>
                  </form>
                  <form method="POST" action="transactions.php">
                    <input type="hidden" name="transaction_id" value="<?= (int)$chat_transaction['transaction_id'] ?>">
                    <input type="hidden" name="status" value="cancelled">
                    <button type="submit" name="update_status" class="btn-chat-txn-cancel" data-confirm="Cancel this transaction?">
                      Cancel
                    </button>
                  </form>
                </div>
              <?php else: ?>
                <!-- Buyer can only cancel their own pending transaction -->
                <div class="chat-txn-actions">
                  <form method="POST" action="transactions.php">
                    <input type="hidden" name="transaction_id" value="<?= (int)$chat_transaction['transaction_id'] ?>">
                    <input type="hidden" name="status" value="cancelled">
                    <button type="submit" name="update_status" class="btn-chat-txn-cancel" data-confirm="Cancel this transaction?">
                      Cancel
                    </button>
                  </form>
                </div>
              <?php endif; ?>

            <?php elseif ($chat_txn_status === 'completed'): ?>
              <!-- Transaction is done, no further actions -->
              <div class="chat-txn-status chat-txn-completed">
                Transaction Completed
              </div>

            <?php elseif ($chat_txn_status === 'cancelled'): ?>
              <!-- This chat's transaction was cancelled -->
              <div class="chat-txn-status chat-txn-cancelled">
                Transaction Cancelled
              </div>
              <?php if ($can_create_txn): ?>
                <!-- Seller can start a new transaction since item currently has no pending transaction -->
                <div class="chat-txn-actions">
                  <form method="POST">
                    <input type="hidden" name="item_id" value="<?= $active_item ?>">
                    <input type="hidden" name="buyer_id" value="<?= $active_to ?>">
                    <button type="submit" name="create_transaction" class="btn-chat-txn-create" data-confirm="Create a new transaction with this buyer?" data-confirm-green>
                      Create Transaction
                    </button>
                  </form>
                </div>
              <?php endif; ?>

            <?php elseif ($can_create_txn): ?>
              <!-- No transaction yet, seller can start one -->
              <div class="chat-txn-hint">
                Agreed on a transaction?
              </div>
              <div class="chat-txn-actions">
                <form method="POST">
                  <input type="hidden" name="item_id" value="<?= $active_item ?>">
                  <input type="hidden" name="buyer_id" value="<?= $active_to ?>">
                  <button type="submit" name="create_transaction" class="btn-chat-txn-create" data-confirm="Create a pending transaction with this buyer for &#8369;<?= number_format($active_item_data['price'], 2) ?>?" data-confirm-green>
                    Create Transaction
                  </button>
                </form>
              </div>

            <?php elseif ($is_seller_in_chat && $any_pending_on_item && $chat_txn_status === null): ?>
              <!-- Seller sees this chat if another buyer already has a pending transaction on this item -->
              <div class="chat-txn-status chat-txn-pending" style="opacity: 0.7;">
                Item Reserved with Another Buyer
              </div>

            <?php endif; ?>

          </div>
        <?php endif; ?>

        <!-- Messages thread -->
        <div class="chat-messages" id="chatMessages">
          <?php if (empty($active_messages)): ?>
            <p class="chat-start-note">Start the conversation about this item.</p>
          <?php else: ?>
            <?php foreach ($active_messages as $msg): ?>
              <?php $isMine = (int)$msg['sender_id'] === $uid; ?>
              <div class="chat-msg <?= $isMine ? 'mine' : 'theirs' ?>">
                <?php if (!$isMine): ?>
                  <div class="chat-msg-avatar">
                    <?= strtoupper($msg['sender_name'][0] ?? '?') ?>
                  </div>
                <?php endif; ?>
                <div class="chat-msg-bubble">
                  <p><?= nl2br(htmlspecialchars($msg['message'])) ?></p>
                  <span class="chat-msg-time"><?= date('M j, g:i A', strtotime($msg['created_at'])) ?></span>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <!-- Message input -->
        <div class="chat-input-area">
          <form method="POST" class="chat-form" id="chatForm">
            <input type="hidden" name="receiver_id" value="<?= $active_to ?>">
            <input type="hidden" name="item_id" value="<?= $active_item ?>">
            <textarea name="message" id="messageInput" placeholder="Type a message..." maxlength="1000" rows="1" required></textarea>
            <button type="submit" name="send_message" class="btn-send" aria-label="Send message">
              <?= $sendIcon ?>
            </button>
          </form>
        </div>

      <?php endif; ?>
    </div>

  </div>

</div>

<script>
  const chatMessages = document.getElementById('chatMessages');
  if (chatMessages) {
    chatMessages.scrollTop = chatMessages.scrollHeight;
  }

  const msgInput = document.getElementById('messageInput');
  if (msgInput) {
    msgInput.addEventListener('input', function () {
      this.style.height = 'auto';
      this.style.height = Math.min(this.scrollHeight, 120) + 'px';
    });

    msgInput.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        document.getElementById('chatForm').submit();
      }
    });
  }
</script>

<?php include 'includes/footer.php'; ?>