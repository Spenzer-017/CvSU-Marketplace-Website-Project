<?php
/*
  sell.php — Post a New Listing
  Only logged-in users should reach this page.
  Redirect guests to login if no session exists.
*/

// Uncomment when you have real auth:
// if (!isset($_SESSION['user'])) {
//   header('Location: /login.php');
//   exit;
// }

// --- Handle form submission ---
$success = false;
$errors  = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // --- Collect and sanitize inputs ---
  $title       = trim($_POST['title']       ?? '');
  $category    = trim($_POST['category']    ?? '');
  $condition   = trim($_POST['condition']   ?? '');
  $price       = trim($_POST['price']       ?? '');
  $description = trim($_POST['description'] ?? '');
  $meetup      = trim($_POST['meetup']      ?? '');
  $contact     = trim($_POST['contact']     ?? '');

  // --- Validation ---
  if ($title === '')                          $errors[] = 'Item title is required.';
  if ($category === '')                       $errors[] = 'Please select a category.';
  if ($condition === '')                      $errors[] = 'Please select the item condition.';
  if ($price === '' || !is_numeric($price))   $errors[] = 'Please enter a valid price.';
  if ((float)$price < 0)                     $errors[] = 'Price cannot be negative.';
  if ($description === '')                    $errors[] = 'Description is required.';
  if ($meetup === '')                         $errors[] = 'Meetup location is required.';
  if ($contact === '')                        $errors[] = 'Contact info is required.';

  // --- Image upload validation ---
  $uploaded_image = null;
  if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
    $max_size      = 5 * 1024 * 1024; // 5MB

    if (!in_array($_FILES['image']['type'], $allowed_types)) {
      $errors[] = 'Image must be JPG, PNG, or WebP.';
    } elseif ($_FILES['image']['size'] > $max_size) {
      $errors[] = 'Image must be smaller than 5MB.';
    } else {
      // In production: move_uploaded_file() to your uploads folder
      $uploaded_image = $_FILES['image']['name'];
    }
  }

  // --- If no errors, save to DB (placeholder) ---
  if (empty($errors)) {
    // TODO: INSERT INTO listings (...) VALUES (...)
    // Example:
    // $stmt = $pdo->prepare("INSERT INTO listings
    //   (title, category, condition, price, description, meetup, contact, image, seller_id)
    //   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    // $stmt->execute([$title, $category, $condition, $price,
    //                 $description, $meetup, $contact,
    //                 $uploaded_image, $_SESSION['user']['id']]);

    $success = true;
  }
}
?>

<?php 
    session_start();
    $activePage = "sell"; 
    include "includes/header.php";
?>

<div class="sell-page">

  <!-- Page header -->
  <div class="sell-header">
    <div>
      <h1>Post an Item</h1>
      <p>Fill in the details below and your listing will be live immediately.</p>
    </div>
    <a href="/cvsu-marketplace/dashboard.php" class="btn-back">← Back to Dashboard</a>
  </div>

  <!-- Success message -->
  <?php if ($success): ?>
    <div class="alert alert-success">
      ✅ Your listing has been posted! <a href="/cvsu-marketplace/browse.php">View it on Browse</a>
    </div>
  <?php endif; ?>

  <!-- Error messages -->
  <?php if (!empty($errors)): ?>
    <div class="alert alert-error">
      <strong>Please fix the following:</strong>
      <ul>
        <?php foreach ($errors as $e): ?>
          <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <!-- Form -->
  <form class="sell-form" method="POST" enctype="multipart/form-data">

    <!-- LEFT column: main details -->
    <div class="sell-main">

      <!-- Item title -->
      <div class="form-group">
        <label for="title">Item Title <span class="required">*</span></label>
        <input
          type="text"
          id="title"
          name="title"
          placeholder="e.g. Calculus Textbook 10th Edition"
          value="<?= htmlspecialchars($title ?? '') ?>"
          maxlength="100"
        />
        <span class="form-hint">Keep it short and descriptive (max 100 characters)</span>
      </div>

      <!-- Category + Condition row -->
      <div class="form-row">
        <div class="form-group">
          <label for="category">Category <span class="required">*</span></label>
          <select id="category" name="category">
            <option value="" disabled selected>Select category</option>
            <option value="Books"       <?= ($category ?? '') === 'Books'       ? 'selected' : '' ?>>📚 Books</option>
            <option value="Electronics"<?= ($category ?? '') === 'Electronics' ? 'selected' : '' ?>>💻 Electronics</option>
            <option value="Supplies"   <?= ($category ?? '') === 'Supplies'    ? 'selected' : '' ?>>✏️ Supplies</option>
            <option value="Clothing"   <?= ($category ?? '') === 'Clothing'    ? 'selected' : '' ?>>👕 Clothing</option>
            <option value="Food"       <?= ($category ?? '') === 'Food'        ? 'selected' : '' ?>>🍱 Food</option>
            <option value="Services"   <?= ($category ?? '') === 'Services'    ? 'selected' : '' ?>>🛠️ Services</option>
            <option value="Other"      <?= ($category ?? '') === 'Other'       ? 'selected' : '' ?>>📦 Other</option>
          </select>
        </div>

        <div class="form-group">
          <label for="condition">Condition <span class="required">*</span></label>
          <select id="condition" name="condition">
            <option value="" disabled selected>Select condition</option>
            <option value="New"      <?= ($condition ?? '') === 'New'      ? 'selected' : '' ?>>🆕 New</option>
            <option value="Like New" <?= ($condition ?? '') === 'Like New' ? 'selected' : '' ?>>✨ Like New</option>
            <option value="Good"     <?= ($condition ?? '') === 'Good'     ? 'selected' : '' ?>>👍 Good</option>
            <option value="Fair"     <?= ($condition ?? '') === 'Fair'     ? 'selected' : '' ?>>🔧 Fair</option>
          </select>
        </div>
      </div>

      <!-- Price -->
      <div class="form-group">
        <label for="price">Price (₱) <span class="required">*</span></label>
        <div class="price-input">
          <span class="price-prefix">₱</span>
          <input
            type="number"
            id="price"
            name="price"
            placeholder="0.00"
            min="0"
            step="0.01"
            value="<?= htmlspecialchars($price ?? '') ?>"
          />
        </div>
        <span class="form-hint">Set to 0 if you want to give it away for free</span>
      </div>

      <!-- Description -->
      <div class="form-group">
        <label for="description">Description <span class="required">*</span></label>
        <textarea
          id="description"
          name="description"
          rows="5"
          placeholder="Describe your item — include edition, size, defects, or anything buyers should know…"
          maxlength="1000"
        ><?= htmlspecialchars($description ?? '') ?></textarea>
        <span class="form-hint char-count">
          <span id="charCount">0</span> / 1000 characters
        </span>
      </div>

    </div><!-- /.sell-main -->

    <!-- RIGHT column: image + logistics -->
    <div class="sell-side">

      <!-- Image upload -->
      <div class="form-card">
        <h3 class="card-title">Item Photo</h3>

        <div class="upload-area" id="uploadArea">
          <div class="upload-placeholder" id="uploadPlaceholder">
            <div class="upload-icon">📷</div>
            <p class="upload-label">Click to upload a photo</p>
            <p class="upload-sub">JPG, PNG or WebP · Max 5MB</p>
          </div>
          <img id="imagePreview" class="image-preview" src="" alt="Preview" style="display:none;" />
          <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp" />
        </div>

        <?php if (!empty($uploaded_image)): ?>
          <p class="form-hint" style="margin-top:8px;">✅ <?= htmlspecialchars($uploaded_image) ?></p>
        <?php endif; ?>
      </div>

      <!-- Meetup location -->
      <div class="form-card">
        <h3 class="card-title">Meetup & Contact</h3>

        <div class="form-group">
          <label for="meetup">Preferred Meetup Spot <span class="required">*</span></label>
          <select id="meetup" name="meetup">
            <option value="" disabled selected>Select location</option>
            <option value="Main Building"     <?= ($meetup ?? '') === 'Main Building'     ? 'selected' : '' ?>>Main Building</option>
            <option value="Library"           <?= ($meetup ?? '') === 'Library'           ? 'selected' : '' ?>>Library</option>
            <option value="Engineering Bldg"  <?= ($meetup ?? '') === 'Engineering Bldg'  ? 'selected' : '' ?>>Engineering Bldg</option>
            <option value="IT Building"       <?= ($meetup ?? '') === 'IT Building'       ? 'selected' : '' ?>>IT Building</option>
            <option value="Health Sciences"   <?= ($meetup ?? '') === 'Health Sciences'   ? 'selected' : '' ?>>Health Sciences</option>
            <option value="Dormitory"         <?= ($meetup ?? '') === 'Dormitory'         ? 'selected' : '' ?>>Dormitory</option>
            <option value="Canteen"           <?= ($meetup ?? '') === 'Canteen'           ? 'selected' : '' ?>>Canteen</option>
            <option value="Covered Court"     <?= ($meetup ?? '') === 'Covered Court'     ? 'selected' : '' ?>>Covered Court</option>
          </select>
        </div>

        <div class="form-group" style="margin-bottom:0;">
          <label for="contact">Contact / Messenger <span class="required">*</span></label>
          <input
            type="text"
            id="contact"
            name="contact"
            placeholder="e.g. FB: Juan dela Cruz"
            value="<?= htmlspecialchars($contact ?? '') ?>"
          />
          <span class="form-hint">How should buyers reach you?</span>
        </div>
      </div>

      <!-- Tips card -->
      <div class="form-card tips-card">
        <h3 class="card-title">💡 Selling Tips</h3>
        <ul class="tips-list">
          <li>Use a clear, well-lit photo</li>
          <li>Be honest about the condition</li>
          <li>Set a fair price — check similar listings</li>
          <li>Reply to messages quickly</li>
          <li>Meet in a safe, public campus spot</li>
        </ul>
      </div>

      <!-- Submit button -->
      <button type="submit" class="btn-submit">
        Post Listing
      </button>

      <p class="submit-note">
        By posting, you agree to the <a href="/guidelines.php">Community Guidelines</a>.
      </p>

    </div><!-- /.sell-side -->

  </form>

</div><!-- /.sell-page -->

<script>
  // ── Character counter for description ──
  const desc      = document.getElementById('description');
  const charCount = document.getElementById('charCount');

  function updateCount() {
    charCount.textContent = desc.value.length;
  }
  desc.addEventListener('input', updateCount);
  updateCount();

  // ── Image upload preview ──
  const fileInput      = document.getElementById('image');
  const uploadArea     = document.getElementById('uploadArea');
  const placeholder    = document.getElementById('uploadPlaceholder');
  const imagePreview   = document.getElementById('imagePreview');

  // Click anywhere on the upload area to trigger the file input
  uploadArea.addEventListener('click', () => fileInput.click());

  fileInput.addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (e) {
      imagePreview.src = e.target.result;
      imagePreview.style.display = 'block';
      placeholder.style.display  = 'none';
    };
    reader.readAsDataURL(file);
  });

  // Drag and drop support
  uploadArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadArea.classList.add('drag-over');
  });

  uploadArea.addEventListener('dragleave', () => {
    uploadArea.classList.remove('drag-over');
  });

  uploadArea.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadArea.classList.remove('drag-over');
    const file = e.dataTransfer.files[0];
    if (file) {
      fileInput.files = e.dataTransfer.files;
      const reader = new FileReader();
      reader.onload = (ev) => {
        imagePreview.src = ev.target.result;
        imagePreview.style.display = 'block';
        placeholder.style.display  = 'none';
      };
      reader.readAsDataURL(file);
    }
  });
</script>

<?php include 'includes/footer.php'; ?>