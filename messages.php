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

<!-- PHP Database Query -->
<?php 
  // Handle sending a message
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $receiver_id = (int)($_POST['receiver_id'] ?? 0);
    $item_id     = (int)($_POST['item_id']     ?? 0);
    $message     = trim($_POST['message']      ?? '');

    if ($receiver_id > 0 && $item_id > 0 && $message !== '' && strlen($message) <= 1000) {
      // Verify item exists
      $stmt = $pdo->prepare("SELECT item_id FROM items WHERE item_id = ? LIMIT 1");
      $stmt->execute([$item_id]);
      if ($stmt->fetch()) {
        $pdo->prepare("
          INSERT INTO messages (sender_id, receiver_id, item_id, message)
          VALUES (?, ?, ?, ?)
        ")->execute([$uid, $receiver_id, $item_id, $message]);
      }
    }

    // Redirect back to same conversation to prevent form resubmit on refresh
    header("Location: messages.php?to=$receiver_id&item=$item_id");
    exit;
  }

  // Get all unique conversations for this user
  // A conversation is defined by a unique (item_id, other_user_id) pair
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
  $active_to = isset($_GET['to'])   ? (int)$_GET['to']   : 0;
  $active_item = isset($_GET['item']) ? (int)$_GET['item']  : 0;

  // If no active conversation set, default to the first one in the list
  if ($active_to === 0 && !empty($conversations)) {
    $active_to   = (int)$conversations[0]['other_user_id'];
    $active_item = (int)$conversations[0]['item_id'];
  }

  // Load the active conversation messages
  $active_messages = [];
  $active_item_data = null;
  $other_user = null;

  if ($active_to > 0 && $active_item > 0) {
    // Mark messages in this conversation as read
    $pdo->prepare("
      UPDATE messages SET is_read = 1
      WHERE receiver_id = ? AND sender_id = ? AND item_id = ?
    ")->execute([$uid, $active_to, $active_item]);

    // Fetch the messages in this thread
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

    // Fetch the item this conversation is about
    $stmt = $pdo->prepare("
      SELECT items.item_id, items.title, items.price, items.image_path, items.status
      FROM items WHERE item_id = ?
    ");
    $stmt->execute([$active_item]);
    $active_item_data = $stmt->fetch();

    // Fetch the other user's info
    $stmt = $pdo->prepare("SELECT id, name, avatar, course FROM users WHERE id = ?");
    $stmt->execute([$active_to]);
    $other_user = $stmt->fetch();
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
            <textarea name="message" id="messageInput" placeholder="Type a message..." maxlength="1000" rows="1" required ></textarea>
            <button type="submit" name="send_message" class="btn-send">Send</button>
          </form>
        </div>

      <?php endif; ?>
    </div>

  </div>

</div>

<script>
  // Auto scroll to bottom of chat on load
  const chatMessages = document.getElementById('chatMessages');
  if (chatMessages) {
    chatMessages.scrollTop = chatMessages.scrollHeight;
  }

  // Auto resize textarea as user types
  const msgInput = document.getElementById('messageInput');
  if (msgInput) {
    msgInput.addEventListener('input', function () {
      this.style.height = 'auto';
      this.style.height = Math.min(this.scrollHeight, 120) + 'px';
    });

    // Send on Enter, new line on Shift+Enter
    msgInput.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        document.getElementById('chatForm').submit();
      }
    });
  }
</script>

<?php include 'includes/footer.php'; ?>