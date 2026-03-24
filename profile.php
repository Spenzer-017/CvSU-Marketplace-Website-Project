<?php
/*
  profile.php — User Profile Editor
  Users can update their info and pick an avatar.
*/

// Uncomment when you have real auth:
// if (!isset($_SESSION['user'])) {
//   header('Location: /login.php');
//   exit;
// }

// --- Mock current user data (replace with DB query later) ---
// Real query: SELECT * FROM users WHERE id = $_SESSION['user']['id']
$user = [
  'name'     => 'Spenzer Lima',
  'email'    => 'kevinspenzer.lima@cvsu.edu.ph',
  'course'   => 'BS Computer Science',
  'year'     => '1st Year',
  'bio'      => 'CS student, and the creator of this platform.',
  'contact'  => 'FB: Spenzer Lima',
  'avatar'   => 'avatar_5',   // which avatar is currently selected
  'joined'   => 'January 2025',
  'listings' => 4,
  'sold'     => 12,
  'bought'   => 7,
];

// --- Available avatars ---
// Each avatar is an SVG defined inline below via PHP.
// Key = avatar id,  value = display label
$avatars = [
  'avatar_1'  => 'Scholar',
  'avatar_2'  => 'Engineer',
  'avatar_3'  => 'Artist',
  'avatar_4'  => 'Scientist',
  'avatar_5'  => 'Coder',
  'avatar_6'  => 'Athlete',
  'avatar_7'  => 'Musician',
  'avatar_8'  => 'Chef',
  'avatar_9'  => 'Medic',
  'avatar_10' => 'Explorer',
  'avatar_11' => 'Gamer',
  'avatar_12' => 'Builder',
];

// --- Handle form submission ---
$success = false;
$errors  = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name    = trim($_POST['name']    ?? '');
  $course  = trim($_POST['course']  ?? '');
  $year    = trim($_POST['year']    ?? '');
  $bio     = trim($_POST['bio']     ?? '');
  $contact = trim($_POST['contact'] ?? '');
  $avatar  = trim($_POST['avatar']  ?? '');

  if ($name === '')   $errors[] = 'Full name is required.';
  if ($course === '') $errors[] = 'Course is required.';
  if ($year === '')   $errors[] = 'Year level is required.';
  if (!array_key_exists($avatar, $avatars)) $errors[] = 'Please select an avatar.';

  if (empty($errors)) {
    // TODO: UPDATE users SET name=?, course=?, year=?, bio=?, contact=?, avatar=?
    //       WHERE id = $_SESSION['user']['id']
    $user['name']    = $name;
    $user['course']  = $course;
    $user['year']    = $year;
    $user['bio']     = $bio;
    $user['contact'] = $contact;
    $user['avatar']  = $avatar;
    $success = true;
  }
}

// --- SVG avatar definitions ---
// 12 avatars, each a unique character built from SVG shapes.
// All use the site color palette: #005F02, #427A43, #C0B87A, #F2E3BB
function get_avatar_svg(string $id): string {
  $avatars = [

    'avatar_1' => '<!-- Scholar -->
      <circle cx="60" cy="60" r="60" fill="#005F02"/>
      <circle cx="60" cy="46" r="20" fill="#F2E3BB"/>
      <ellipse cx="60" cy="90" rx="28" ry="20" fill="#F2E3BB"/>
      <rect x="44" y="28" width="32" height="6" rx="3" fill="#C0B87A"/>
      <rect x="55" y="22" width="10" height="8" rx="2" fill="#C0B87A"/>
      <circle cx="53" cy="45" r="2.5" fill="#005F02"/>
      <circle cx="67" cy="45" r="2.5" fill="#005F02"/>
      <path d="M54 52 Q60 57 66 52" stroke="#005F02" stroke-width="2" fill="none" stroke-linecap="round"/>',

    'avatar_2' => '<!-- Engineer -->
      <circle cx="60" cy="60" r="60" fill="#427A43"/>
      <circle cx="60" cy="46" r="20" fill="#F2E3BB"/>
      <ellipse cx="60" cy="90" rx="28" ry="20" fill="#F2E3BB"/>
      <path d="M44 30 L60 22 L76 30 L76 36 L44 36 Z" fill="#C0B87A"/>
      <rect x="56" y="22" width="8" height="8" fill="#C0B87A"/>
      <circle cx="53" cy="45" r="2.5" fill="#427A43"/>
      <circle cx="67" cy="45" r="2.5" fill="#427A43"/>
      <path d="M54 52 Q60 57 66 52" stroke="#427A43" stroke-width="2" fill="none" stroke-linecap="round"/>
      <rect x="52" y="58" width="16" height="3" rx="1.5" fill="#C0B87A"/>',

    'avatar_3' => '<!-- Artist -->
      <circle cx="60" cy="60" r="60" fill="#C0B87A"/>
      <circle cx="60" cy="46" r="20" fill="#F2E3BB"/>
      <ellipse cx="60" cy="90" rx="28" ry="20" fill="#F2E3BB"/>
      <circle cx="60" cy="26" r="10" fill="#005F02"/>
      <circle cx="53" cy="45" r="2.5" fill="#005F02"/>
      <circle cx="67" cy="45" r="2.5" fill="#005F02"/>
      <path d="M54 52 Q60 57 66 52" stroke="#005F02" stroke-width="2" fill="none" stroke-linecap="round"/>
      <circle cx="50" cy="46" r="4" fill="#C0B87A" opacity="0.6"/>
      <circle cx="70" cy="46" r="4" fill="#C0B87A" opacity="0.6"/>',

    'avatar_4' => '<!-- Scientist -->
      <circle cx="60" cy="60" r="60" fill="#1A2B1A"/>
      <circle cx="60" cy="46" r="20" fill="#F2E3BB"/>
      <ellipse cx="60" cy="90" rx="28" ry="20" fill="#F2E3BB"/>
      <rect x="47" y="34" width="26" height="16" rx="8" fill="none" stroke="#C0B87A" stroke-width="3"/>
      <circle cx="53" cy="45" r="2.5" fill="#005F02"/>
      <circle cx="67" cy="45" r="2.5" fill="#005F02"/>
      <path d="M54 52 Q60 57 66 52" stroke="#005F02" stroke-width="2" fill="none" stroke-linecap="round"/>
      <rect x="57" y="22" width="6" height="12" rx="3" fill="#F2E3BB"/>',

    'avatar_5' => '<!-- Coder -->
      <circle cx="60" cy="60" r="60" fill="#005F02"/>
      <circle cx="60" cy="46" r="20" fill="#F2E3BB"/>
      <ellipse cx="60" cy="90" rx="28" ry="20" fill="#F2E3BB"/>
      <rect x="42" y="26" width="36" height="22" rx="4" fill="#1A2B1A"/>
      <text x="60" y="41" text-anchor="middle" font-size="9" fill="#4CAF50" font-family="monospace">&lt;/&gt;</text>
      <circle cx="53" cy="52" r="2.5" fill="#005F02"/>
      <circle cx="67" cy="52" r="2.5" fill="#005F02"/>
      <path d="M54 58 Q60 63 66 58" stroke="#005F02" stroke-width="2" fill="none" stroke-linecap="round"/>',

    'avatar_6' => '<!-- Athlete -->
      <circle cx="60" cy="60" r="60" fill="#427A43"/>
      <circle cx="60" cy="46" r="20" fill="#F2E3BB"/>
      <ellipse cx="60" cy="90" rx="28" ry="20" fill="#F2E3BB"/>
      <path d="M44 26 Q60 18 76 26" stroke="#C0B87A" stroke-width="4" fill="none" stroke-linecap="round"/>
      <circle cx="53" cy="45" r="2.5" fill="#427A43"/>
      <circle cx="67" cy="45" r="2.5" fill="#427A43"/>
      <path d="M53 53 Q60 59 67 53" stroke="#427A43" stroke-width="2.5" fill="none" stroke-linecap="round"/>',

    'avatar_7' => '<!-- Musician -->
      <circle cx="60" cy="60" r="60" fill="#C0B87A"/>
      <circle cx="60" cy="46" r="20" fill="#F2E3BB"/>
      <ellipse cx="60" cy="90" rx="28" ry="20" fill="#F2E3BB"/>
      <ellipse cx="60" cy="28" rx="12" ry="8" fill="#005F02"/>
      <rect x="71" y="20" width="3" height="16" rx="1.5" fill="#005F02"/>
      <circle cx="53" cy="45" r="2.5" fill="#005F02"/>
      <circle cx="67" cy="45" r="2.5" fill="#005F02"/>
      <path d="M54 52 Q60 57 66 52" stroke="#005F02" stroke-width="2" fill="none" stroke-linecap="round"/>',

    'avatar_8' => '<!-- Chef -->
      <circle cx="60" cy="60" r="60" fill="#1A2B1A"/>
      <circle cx="60" cy="46" r="20" fill="#F2E3BB"/>
      <ellipse cx="60" cy="90" rx="28" ry="20" fill="#F2E3BB"/>
      <ellipse cx="60" cy="29" rx="16" ry="10" fill="#FFFFFF"/>
      <rect x="44" y="28" width="32" height="5" fill="#FFFFFF"/>
      <circle cx="53" cy="45" r="2.5" fill="#1A2B1A"/>
      <circle cx="67" cy="45" r="2.5" fill="#1A2B1A"/>
      <path d="M54 52 Q60 57 66 52" stroke="#1A2B1A" stroke-width="2" fill="none" stroke-linecap="round"/>',

    'avatar_9' => '<!-- Medic -->
      <circle cx="60" cy="60" r="60" fill="#005F02"/>
      <circle cx="60" cy="46" r="20" fill="#F2E3BB"/>
      <ellipse cx="60" cy="90" rx="28" ry="20" fill="#F2E3BB"/>
      <rect x="55" y="22" width="10" height="10" rx="2" fill="#FFFFFF"/>
      <rect x="52" y="25" width="16" height="4" rx="2" fill="#b94040"/>
      <rect x="57" y="20" width="6" height="14" rx="2" fill="#b94040"/>
      <circle cx="53" cy="45" r="2.5" fill="#005F02"/>
      <circle cx="67" cy="45" r="2.5" fill="#005F02"/>
      <path d="M54 52 Q60 57 66 52" stroke="#005F02" stroke-width="2" fill="none" stroke-linecap="round"/>',

    'avatar_10' => '<!-- Explorer -->
      <circle cx="60" cy="60" r="60" fill="#427A43"/>
      <circle cx="60" cy="46" r="20" fill="#F2E3BB"/>
      <ellipse cx="60" cy="90" rx="28" ry="20" fill="#F2E3BB"/>
      <path d="M42 30 L60 22 L78 30" stroke="#C0B87A" stroke-width="4" stroke-linecap="round" fill="none"/>
      <rect x="44" y="29" width="32" height="5" rx="2" fill="#C0B87A"/>
      <circle cx="53" cy="45" r="2.5" fill="#427A43"/>
      <circle cx="67" cy="45" r="2.5" fill="#427A43"/>
      <path d="M54 52 Q60 57 66 52" stroke="#427A43" stroke-width="2" fill="none" stroke-linecap="round"/>',

    'avatar_11' => '<!-- Gamer -->
      <circle cx="60" cy="60" r="60" fill="#1A2B1A"/>
      <circle cx="60" cy="46" r="20" fill="#F2E3BB"/>
      <ellipse cx="60" cy="90" rx="28" ry="20" fill="#F2E3BB"/>
      <rect x="44" y="24" width="32" height="18" rx="6" fill="#005F02"/>
      <circle cx="54" cy="33" r="3" fill="#C0B87A"/>
      <circle cx="66" cy="33" r="3" fill="#C0B87A"/>
      <circle cx="60" cy="27" r="2" fill="#C0B87A"/>
      <circle cx="60" cy="39" r="2" fill="#C0B87A"/>
      <circle cx="53" cy="46" r="2.5" fill="#1A2B1A"/>
      <circle cx="67" cy="46" r="2.5" fill="#1A2B1A"/>
      <path d="M54 52 Q60 57 66 52" stroke="#1A2B1A" stroke-width="2" fill="none" stroke-linecap="round"/>',

    'avatar_12' => '<!-- Builder -->
      <circle cx="60" cy="60" r="60" fill="#C0B87A"/>
      <circle cx="60" cy="46" r="20" fill="#F2E3BB"/>
      <ellipse cx="60" cy="90" rx="28" ry="20" fill="#F2E3BB"/>
      <rect x="42" y="26" width="36" height="10" rx="3" fill="#1A2B1A"/>
      <rect x="48" y="22" width="6" height="6" rx="1" fill="#1A2B1A"/>
      <rect x="66" y="22" width="6" height="6" rx="1" fill="#1A2B1A"/>
      <circle cx="53" cy="45" r="2.5" fill="#C0B87A"/>
      <circle cx="67" cy="45" r="2.5" fill="#C0B87A"/>
      <path d="M54 52 Q60 57 66 52" stroke="#C0B87A" stroke-width="2" fill="none" stroke-linecap="round"/>',

  ];
  return $avatars[$id] ?? $avatars['avatar_1'];
}
?>

<?php 
    session_start();
    $activePage = "profile"; 
    include "includes/header.php";
?>

<div class="profile-page">

  <!-- Page header -->
  <div class="profile-page-header">
    <div>
      <h1>Edit Profile</h1>
      <p>Update your info and choose how you appear to other students.</p>
    </div>
    <a href="/cvsu-marketplace/dashboard.php" class="btn-back">← Back to Dashboard</a>
  </div>

  <!-- Success / error alerts -->
  <?php if ($success): ?>
    <div class="alert alert-success">✅ Profile updated successfully!</div>
  <?php endif; ?>
  <?php if (!empty($errors)): ?>
    <div class="alert alert-error">
      <strong>Please fix the following:</strong>
      <ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
    </div>
  <?php endif; ?>

  <form class="profile-form" method="POST">

    <!-- LEFT: Avatar picker + stats -->
    <div class="profile-left">

      <!-- Current avatar display -->
      <div class="current-avatar-card">
        <div class="current-avatar" id="currentAvatarDisplay">
          <svg viewBox="0 0 120 120" xmlns="http://www.w3.org/2000/svg">
            <?= get_avatar_svg($user['avatar']) ?>
          </svg>
        </div>
        <div class="current-avatar-name" id="currentAvatarName">
          <?= htmlspecialchars($avatars[$user['avatar']]) ?>
        </div>
        <p class="current-avatar-hint">Click an avatar below to select</p>
      </div>

      <!-- Avatar grid picker -->
      <div class="avatar-picker-card">
        <h3 class="card-title">Choose Your Avatar</h3>
        <div class="avatar-grid">
          <?php foreach ($avatars as $id => $label): ?>
            <div
              class="avatar-option <?= $user['avatar'] === $id ? 'selected' : '' ?>"
              data-id="<?= $id ?>"
              data-label="<?= htmlspecialchars($label) ?>"
              title="<?= htmlspecialchars($label) ?>"
              onclick="selectAvatar(this)"
            >
              <svg viewBox="0 0 120 120" xmlns="http://www.w3.org/2000/svg">
                <?= get_avatar_svg($id) ?>
              </svg>
              <span><?= htmlspecialchars($label) ?></span>
            </div>
          <?php endforeach; ?>
        </div>
        <!-- Hidden input that stores the chosen avatar id -->
        <input type="hidden" name="avatar" id="avatarInput" value="<?= htmlspecialchars($user['avatar']) ?>" />
      </div>

      <!-- Stats (read-only) -->
      <div class="profile-stats-card">
        <h3 class="card-title">Your Stats</h3>
        <div class="profile-stats">
          <div class="pstat">
            <div class="pstat-value"><?= $user['listings'] ?></div>
            <div class="pstat-label">Active Listings</div>
          </div>
          <div class="pstat">
            <div class="pstat-value"><?= $user['sold'] ?></div>
            <div class="pstat-label">Items Sold</div>
          </div>
          <div class="pstat">
            <div class="pstat-value"><?= $user['bought'] ?></div>
            <div class="pstat-label">Purchases</div>
          </div>
        </div>
        <p class="joined-note">Member since <?= htmlspecialchars($user['joined']) ?></p>
      </div>

    </div><!-- /.profile-left -->

    <!-- RIGHT: Edit fields -->
    <div class="profile-right">

      <div class="profile-fields-card">
        <h3 class="card-title">Personal Information</h3>

        <!-- Full name -->
        <div class="form-group">
          <label for="name">Full Name <span class="required">*</span></label>
          <input
            type="text"
            id="name"
            name="name"
            value="<?= htmlspecialchars($user['name']) ?>"
            placeholder="Your full name"
            maxlength="80"
          />
        </div>

        <!-- Email (read-only — tied to account) -->
        <div class="form-group">
          <label for="email">Email Address</label>
          <input
            type="email"
            id="email"
            name="email"
            value="<?= htmlspecialchars($user['email']) ?>"
            disabled
          />
          <span class="form-hint">Email is linked to your CvSU account and cannot be changed here.</span>
        </div>

        <!-- Course + Year row -->
        <div class="form-row">
          <div class="form-group">
            <label for="course">Course <span class="required">*</span></label>
            <select id="course" name="course">
              <option value="" disabled>Select course</option>
              <?php
              $courses = [
                'BS Computer Science',
                'BS Information Technology',
                'BS Civil Engineering',
                'BS Electrical Engineering',
                'BS Mechanical Engineering',
                'BS Electronics Engineering',
                'BS Nursing',
                'BS Education',
                'BS Agriculture',
                'BS Business Administration',
                'BS Accountancy',
                'Other',
              ];
              foreach ($courses as $c):
              ?>
                <option value="<?= $c ?>" <?= $user['course'] === $c ? 'selected' : '' ?>>
                  <?= $c ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label for="year">Year Level <span class="required">*</span></label>
            <select id="year" name="year">
              <option value="" disabled>Select year</option>
              <?php
              $years = ['1st Year','2nd Year','3rd Year','4th Year','5th Year','Graduate'];
              foreach ($years as $y):
              ?>
                <option value="<?= $y ?>" <?= $user['year'] === $y ? 'selected' : '' ?>>
                  <?= $y ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <!-- Bio -->
        <div class="form-group">
          <label for="bio">Bio</label>
          <textarea
            id="bio"
            name="bio"
            rows="3"
            maxlength="200"
            placeholder="Tell other students a little about yourself…"
          ><?= htmlspecialchars($user['bio']) ?></textarea>
          <span class="form-hint char-count">
            <span id="bioCount"><?= strlen($user['bio']) ?></span> / 200 characters
          </span>
        </div>

        <!-- Contact -->
        <div class="form-group" style="margin-bottom:0;">
          <label for="contact">Contact / Messenger</label>
          <input
            type="text"
            id="contact"
            name="contact"
            value="<?= htmlspecialchars($user['contact']) ?>"
            placeholder="e.g. FB: Juan dela Cruz"
            maxlength="80"
          />
          <span class="form-hint">Shown to buyers when they message you about a listing.</span>
        </div>

      </div><!-- /.profile-fields-card -->

      <!-- Change password section -->
      <div class="profile-fields-card">
        <h3 class="card-title">Change Password</h3>

        <div class="form-group">
          <label for="current_password">Current Password</label>
          <input type="password" id="current_password" name="current_password" placeholder="Enter current password" />
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="new_password">New Password</label>
            <input type="password" id="new_password" name="new_password" placeholder="New password" />
          </div>
          <div class="form-group">
            <label for="confirm_password">Confirm New Password</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Repeat new password" />
          </div>
        </div>

        <span class="form-hint" style="margin-top:-8px; display:block;">
          Leave all password fields empty if you don't want to change it.
        </span>

      </div>

      <!-- Save button -->
      <button type="submit" class="btn-submit">Save Changes</button>

      <p class="submit-note">
        Changes are saved immediately and visible to other students.
      </p>

    </div><!-- /.profile-right -->

  </form>

</div><!-- /.profile-page -->

<script>
  // ── Bio character counter ─────────────────────
  const bio      = document.getElementById('bio');
  const bioCount = document.getElementById('bioCount');
  bio.addEventListener('input', () => {
    bioCount.textContent = bio.value.length;
  });

  // ── Avatar picker ─────────────────────────────
  // Build the SVG map in PHP with json_encode so all special characters,
  // newlines and quotes are safely escaped — no template literal issues.
  <?php
    $avatar_svg_map = [];
    foreach ($avatars as $id => $label) {
      $avatar_svg_map[$id] = get_avatar_svg($id);
    }
  ?>
  const avatarSVGs = <?= json_encode($avatar_svg_map, JSON_HEX_TAG | JSON_HEX_AMP) ?>;

  function selectAvatar(el) {
    // Deselect all
    document.querySelectorAll('.avatar-option').forEach(opt => {
      opt.classList.remove('selected');
    });

    // Select clicked
    el.classList.add('selected');

    const id    = el.dataset.id;
    const label = el.dataset.label;

    // Update hidden input
    document.getElementById('avatarInput').value = id;

    // Update large preview
    document.querySelector('#currentAvatarDisplay svg').innerHTML = avatarSVGs[id];
    document.getElementById('currentAvatarName').textContent = label;
  }
</script>

<?php include 'includes/footer.php'; ?>