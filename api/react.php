<?php
  /*
    api/react.php
    AJAX endpoint for listing reactions.
    Accepts POST: item_id, reaction_type
    Returns JSON.
  */

  session_start();
  require_once '../includes/db.php';
  require_once '../includes/reactions.php';

  header('Content-Type: application/json');

  if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not logged in']);
    exit;
  }

  $user_id = (int)$_SESSION['user']['id'];
  $item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
  $reaction_type = trim($_POST['reaction_type'] ?? '');

  if ($item_id <= 0 || $reaction_type === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input']);
    exit;
  }

  // Verify item exists
  $stmt = $pdo->prepare("SELECT item_id, seller_id FROM items WHERE item_id = ? LIMIT 1");
  $stmt->execute([$item_id]);
  $item = $stmt->fetch();

  if (!$item) {
    http_response_code(404);
    echo json_encode(['error' => 'Item not found']);
    exit;
  }

  $result = handleReaction($pdo, $item_id, $user_id, $reaction_type);
  $counts = getReactionCounts($pdo, $item_id);

  /*
    NOTIFICATION HOOK POINT:
    When $result['action'] === 'added' or 'changed', you can insert a notification here:
    if ($result['action'] === 'added' && (int)$item['seller_id'] !== $user_id) {
        // Insert into notifications table
    }
  */

  echo json_encode([
    'action' => $result['action'],
    'reaction_type' => $result['reaction_type'],
    'counts' => $counts,
  ]);
  exit;
?>