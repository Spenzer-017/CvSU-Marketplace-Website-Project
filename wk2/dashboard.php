<!-- PHP Logic (Authentication, Session, etc.) -->
<?php 
  /*
    dashboard.php
    The main page users land on after logging in.
  */

  session_start();

  // Forced user login (Remove/change later on for authentication)
  $_SESSION['user'] = ["name" => "Spenzer", 'course' => 'BS Computer Science', "id" => 17];
  $user = $_SESSION['user'] ?? null;
?>

<!-- PHP UI/UX Logic -->
<?php 
  $activePage = "dashboard"; 
  include "includes/header.php"; 
?>

<!-- PHP Database Query -->
<?php
  // Mock data (replace with real DB queries later)
  $stats = [
    ['label' => 'Active Listings', 'value' => 6],
    ['label' => 'Items Sold', 'value' => 9],
    ['label' => 'Purchases Made', 'value' => 6],
    ['label' => 'Unread Messages', 'value' => 7],
  ];
  
  $my_listings = [
    ['title' => 'Portrait of Myself', 'price' => 777, 'status' => 'active', 'views' => 34, 'id' => 967],
    ['title' => 'Junimo Plushe', 'price' => 500, 'status' => 'active', 'views' => 21, 'id' => 190],
    ['title' => 'Statue of My Dog (Life Size)', 'price' => 2500, 'status' => 'sold', 'views' => 58, 'id' => 275],
    ['title' => 'Rubiks Cube', 'price' => 420, 'status' => 'active', 'views' => 15, 'id' => 489],
  ];
  
  $recent_messages = [
    ['from' => 'Mom', 'item' => 'Statue of My Dog (Life Size)', 'msg' => 'Why are you selling this son?', 'time' => '10 min ago',  'unread' => true],
    ['from' => 'Xymon', 'item' => 'Portrait of Myself', 'msg' => 'How much for you to burn it?', 'time' => '1 hour ago', 'unread' => true],
    ['from' => 'Rob', 'item' => 'Junimo Plushe', 'msg' => 'I will take this out of your hand for 50.','time' => '3 hours ago', 'unread' => false],
  ];
  
  $recent_purchases = [
    ['title' => 'Death Note', 'from' => 'Ryuk', 'price' => 666, 'date' => 'Mar 10'],
    ['title' => 'Key Chain', 'from' => 'Yñigo', 'price' => 85, 'date' => 'Mar 8'],
    ['title' => 'Venus Fly Trap', 'from' => 'Gypsy', 'price' => 800, 'date' => 'Mar 5'],
  ];
?>

<!-- Dashboard Body -->
<div class="dashboard">
 
  <!-- Welcome Banner -->
  <div class="welcome-banner">
    <div>
      <h1>Welcome back, <?= htmlspecialchars($user['name'] ?? '') ?>!</h1>
      <p><?= htmlspecialchars($user['course'] ?? '' ) ?> &nbsp;-&nbsp; CvSU Main Campus</p>
    </div>
    <a href="sell.php" class="btn-post">+ Post an Item</a>
  </div>
 
  <!-- Stats -->
  <div class="stats-row">
    <?php foreach ($stats as $stat): ?>
      <div class="stat-card">
        <div class="stat-value"><?= htmlspecialchars($stat['value']) ?></div>
        <div class="stat-label"><?= htmlspecialchars($stat['label']) ?></div>
      </div>
    <?php endforeach; ?>
  </div>
 
  <!-- Quick Actions -->
  <div class="section-heading">
    <h2>Quick Actions</h2>
  </div>
  <div class="quick-actions">
    <a href="sell.php" class="quick-action-btn">
      <div class="quick-action-icon"><?= $postItemIcon ?></div>
      <div class="quick-action-label">Post Item</div>
    </a>
    <a href="browse.php" class="quick-action-btn">
      <div class="quick-action-icon"><?= $searchIcon ?></div>
      <div class="quick-action-label">Browse</div>
    </a>
    <a href="messages.php" class="quick-action-btn">
      <div class="quick-action-icon"><?= $messageIcon ?></div>
      <div class="quick-action-label">Messages</div>
    </a>
    <a href="profile.php" class="quick-action-btn">
      <div class="quick-action-icon"><?= $userIcon ?></div>
      <div class="quick-action-label">My Profile</div>
    </a>
  </div>
 
  <!-- My Listings & Messages Grid Section -->
  <div class="dashboard-grid">
 
    <!-- My Listings Table -->
    <div>
      <div class="section-heading">
        <h2>My Listings</h2>
        <a href="my-listings.php">View all ></a>
      </div>
      <div class="table-container">
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
                <td><?= number_format($listing['price']) ?></td>
                <td class="hide-mobile"><?= htmlspecialchars($listing['views']) ?></td>
                <td>
                  <span class="badge badge-<?= htmlspecialchars($listing['status']) ?>">
                    <?= htmlspecialchars(ucfirst($listing['status'])) ?>
                  </span>
                </td>
                <td class="table-actions">
                  <a href="my-listings.php?id=<?= urlencode($listing['id']) ?>">Edit</a>
                  <a href="delete-listing.php?id=<?= urlencode($listing['id']) ?>" class="delete">Delete</a>
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
        <a href="messages.php">View all ></a>
      </div>
      <div class="messages-box">
        <?php foreach ($recent_messages as $msg): ?>
          <div class="message-item">
            <!-- Avatar (first letter of the sender's name) (changed later to their profile avatar) -->
            <div class="avatar"><?= htmlspecialchars(strtoupper($msg['from'][0] ?? '')) ?></div>
 
            <div class="message-content">
              <div class="message-top">
                <span class="message-sender"><?= htmlspecialchars($msg['from']) ?></span>
                <span class="message-time"><?= htmlspecialchars($msg['time']) ?></span>
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
 
  </div>
 
  <!-- Recent Purchases -->
  <div class="section-heading">
    <h2>Recent Purchases</h2>
    <a href="purchases.php">View all ></a>
  </div>
  <div class="purchases-box">
    <?php foreach ($recent_purchases as $purchase): ?>
      <div class="purchase-item">
        <div class="purchase-info">
          <div class="purchase-title"><?= htmlspecialchars($purchase['title']) ?></div>
          <div class="purchase-from">From: <?= htmlspecialchars($purchase['from']) ?></div>
        </div>
        <div class="purchase-right">
          <div class="purchase-price"><?= htmlspecialchars($purchase['price']) ?></div>
          <div class="purchase-date"><?= htmlspecialchars($purchase['date']) ?></div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
 
</div>
 
<?php include "includes/footer.php"; ?>