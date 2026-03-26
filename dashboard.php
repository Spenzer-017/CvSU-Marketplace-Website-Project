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
      <h1>Welcome back, <?= htmlspecialchars($user['name']) ?>!</h1>
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
      <div class="quick-action-icon"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#434343"><path d="M600-800H360v280h240v-280Zm200 0H680v280h120v-280ZM575-440H320v240h222q21 0 40.5-7t35.5-21l166-137q-8-8-18-12t-21-6q-17-3-33 1t-30 15l-108 87H400v-80h146l44-36q5-3 7.5-8t2.5-11q0-10-7.5-17.5T575-440Zm-335 0h-80v280h80v-280Zm40 0v-360q0-33 23.5-56.5T360-880h440q33 0 56.5 23.5T880-800v280q0 33-23.5 56.5T800-440H280ZM240-80h-80q-33 0-56.5-23.5T80-160v-280q0-33 23.5-56.5T160-520h415q85 0 164 29t127 98l27 41-223 186q-27 23-60 34.5T542-120H309q-11 18-29 29t-40 11Z"/></svg></div>
      <div class="quick-action-label">Post Item</div>
    </a>
    <a href="/browse.php" class="quick-action-btn">
      <div class="quick-action-icon"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#434343"><path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/></svg></div>
      <div class="quick-action-label">Browse</div>
    </a>
    <a href="/messages.php" class="quick-action-btn">
      <div class="quick-action-icon"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#434343"><path d="M80-80v-720q0-33 23.5-56.5T160-880h640q33 0 56.5 23.5T880-800v480q0 33-23.5 56.5T800-240H240L80-80Zm126-240h594v-480H160v525l46-45Zm-46 0v-480 480Z"/></svg></div>
      <div class="quick-action-label">Messages</div>
    </a>
    <a href="/profile.php" class="quick-action-btn">
      <div class="quick-action-icon"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#434343"><path d="M234-276q51-39 114-61.5T480-360q69 0 132 22.5T726-276q35-41 54.5-93T800-480q0-133-93.5-226.5T480-800q-133 0-226.5 93.5T160-480q0 59 19.5 111t54.5 93Zm146.5-204.5Q340-521 340-580t40.5-99.5Q421-720 480-720t99.5 40.5Q620-639 620-580t-40.5 99.5Q539-440 480-440t-99.5-40.5ZM480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm100-95.5q47-15.5 86-44.5-39-29-86-44.5T480-280q-53 0-100 15.5T294-220q39 29 86 44.5T480-160q53 0 100-15.5ZM523-537q17-17 17-43t-17-43q-17-17-43-17t-43 17q-17 17-17 43t17 43q17 17 43 17t43-17Zm-43-43Zm0 360Z"/></svg></div>
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