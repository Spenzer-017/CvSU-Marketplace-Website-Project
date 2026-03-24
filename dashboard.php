<!-- UNDER CONSTRUCTION, NOT FINALIZE YET, JUST A PLACEHOLDER CODE -->

<?php
/*
  dashboard.php
  The main page users land on after logging in.
*/
 
// --- Mock data (replace with real DB queries later) ---
$user = [
  'name'   => 'Spenzer Lima',
  'course' => 'BS Computer Science',
];
 
$stats = [
  ['label' => 'Active Listings',  'value' => '4'],
  ['label' => 'Items Sold',       'value' => '12'],
  ['label' => 'Purchases Made',   'value' => '7'],
  ['label' => 'Unread Messages',  'value' => '3'],
];
 
$my_listings = [
  ['title' => 'Calculus Textbook',      'price' => '₱280', 'status' => 'active',  'views' => 34],
  ['title' => 'Scientific Calculator',  'price' => '₱450', 'status' => 'active',  'views' => 21],
  ['title' => 'Engineering Drawing Kit','price' => '₱350', 'status' => 'sold',    'views' => 58],
  ['title' => 'Physics Lab Manual',     'price' => '₱120', 'status' => 'active',  'views' => 15],
];
 
$recent_messages = [
  ['from' => 'Maria Santos',  'item' => 'Calculus Textbook',     'msg' => 'Is this still available?',        'time' => '10 min ago',  'unread' => true],
  ['from' => 'Carlo Reyes',   'item' => 'Scientific Calculator', 'msg' => 'Can you do ₱400?',                'time' => '1 hour ago',  'unread' => true],
  ['from' => 'Anna Flores',   'item' => 'Engineering Kit',       'msg' => 'Thank you for the fast delivery!','time' => '3 hours ago', 'unread' => false],
];
 
$recent_purchases = [
  ['title' => 'USB-C Hub',       'from' => 'Leo Mendoza',    'price' => '₱550', 'date' => 'Mar 10'],
  ['title' => 'Notebook Bundle', 'from' => 'Claire Tan',     'price' => '₱150', 'date' => 'Mar 8'],
  ['title' => 'Arduino Kit',     'from' => 'Ryan Castro',    'price' => '₱900', 'date' => 'Mar 5'],
];
?>

<?php 
  session_start();
  $activePage = "dashboard"; 
  include "includes/header.php";
?>

<!-- Dashboard Body -->
<div class="dashboard">
 
  <!-- Welcome Banner -->
  <div class="welcome-banner">
    <div>
      <h1>Welcome back, <?= htmlspecialchars($user['name']) ?>! 👋</h1>
      <p><?= htmlspecialchars($user['course']) ?> &nbsp;·&nbsp; CvSU Main Campus</p>
    </div>
    <a href="/sell.php" class="btn-post">+ Post an Item</a>
  </div>
 
  <!-- Stats -->
  <div class="stats-row">
    <?php foreach ($stats as $stat): ?>
      <div class="stat-card">
        <div class="stat-value"><?= $stat['value'] ?></div>
        <div class="stat-label"><?= $stat['label'] ?></div>
      </div>
    <?php endforeach; ?>
  </div>
 
  <!-- Quick Actions -->
  <div class="section-heading">
    <h2>Quick Actions</h2>
  </div>
  <div class="quick-actions">
    <a href="/sell.php" class="quick-action-btn">
      <div class="quick-action-icon">📦</div>
      <div class="quick-action-label">Post Item</div>
    </a>
    <a href="/browse.php" class="quick-action-btn">
      <div class="quick-action-icon">🔍</div>
      <div class="quick-action-label">Browse</div>
    </a>
    <a href="/messages.php" class="quick-action-btn">
      <div class="quick-action-icon">💬</div>
      <div class="quick-action-label">Messages</div>
    </a>
    <a href="/profile.php" class="quick-action-btn">
      <div class="quick-action-icon">👤</div>
      <div class="quick-action-label">My Profile</div>
    </a>
  </div>
 
  <!-- My Listings + Messages -->
  <div class="dashboard-grid">
 
    <!-- My Listings Table -->
    <div>
      <div class="section-heading">
        <h2>My Listings</h2>
        <a href="/my-listings.php">View all →</a>
      </div>
      <div class="table-box">
        <table>
          <thead>
            <tr>
              <th>Item</th>
              <th>Price</th>
              <th class="hide-mobile">Views</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($my_listings as $listing): ?>
              <tr>
                <td><?= htmlspecialchars($listing['title']) ?></td>
                <td><?= $listing['price'] ?></td>
                <td class="hide-mobile"><?= $listing['views'] ?></td>
                <td>
                  <span class="badge badge-<?= $listing['status'] ?>">
                    <?= ucfirst($listing['status']) ?>
                  </span>
                </td>
                <td class="table-actions">
                  <a href="/edit-listing.php">Edit</a>
                  <a href="/delete-listing.php" class="delete">Delete</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
 
    <!-- Messages Panel -->
    <div>
      <div class="section-heading">
        <h2>Messages</h2>
        <a href="/messages.php">View all →</a>
      </div>
      <div class="messages-box">
        <?php foreach ($recent_messages as $msg): ?>
          <div class="message-item">
            <!-- Avatar: first letter of sender's name -->
            <div class="avatar"><?= strtoupper($msg['from'][0]) ?></div>
 
            <div class="message-content">
              <div class="message-top">
                <span class="message-sender"><?= htmlspecialchars($msg['from']) ?></span>
                <span class="message-time"><?= $msg['time'] ?></span>
              </div>
              <div class="message-item-name"><?= htmlspecialchars($msg['item']) ?></div>
              <div class="message-preview"><?= htmlspecialchars($msg['msg']) ?></div>
            </div>
 
            <!-- Green dot for unread messages -->
            <?php if ($msg['unread']): ?>
              <div class="unread-dot"></div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
 
  </div><!-- /.dashboard-grid -->
 
  <!-- Recent Purchases -->
  <div class="section-heading">
    <h2>Recent Purchases</h2>
    <a href="/purchases.php">View all →</a>
  </div>
  <div class="purchases-box">
    <?php foreach ($recent_purchases as $purchase): ?>
      <div class="purchase-item">
        <div class="purchase-info">
          <div class="purchase-title"><?= htmlspecialchars($purchase['title']) ?></div>
          <div class="purchase-from">From: <?= htmlspecialchars($purchase['from']) ?></div>
        </div>
        <div class="purchase-right">
          <div class="purchase-price"><?= $purchase['price'] ?></div>
          <div class="purchase-date"><?= $purchase['date'] ?></div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
 
</div><!-- /.dashboard -->
 
<?php include "includes/footer.php"; ?>