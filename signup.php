<?php
/*
  signup.php - User Registration
*/

session_start();

// If already logged in, redirect to dashboard
if (isset($_SESSION['user'])) {
  header('Location: dashboard.php');
  exit;
}

// DB connection
require_once 'includes/db.php';

$errors = [];
$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name'] ?? '');
  $email = strtolower(trim($_POST['email'] ?? ''));
  $password = $_POST['password'] ?? '';
  $confirm = trim($_POST['confirm'] ?? '');

  // Validation
  if ($name === '') $errors[] = 'Full name is required.';
  if (strlen($name) > 80) $errors[] = 'Name must be 80 characters or less.';

  if ($email === '') $errors[] = 'Email is required.';
  elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';
  elseif (!str_ends_with($email, '@cvsu.edu.ph')) $errors[] = 'Only CvSU email addresses are allowed.';

  if ($password === '') $errors[] = 'Password is required.';
  elseif (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';

  if ($confirm === '') $errors[] = 'Please confirm your password.';
  elseif ($password !== $confirm) $errors[] = 'Passwords do not match.';

  // Check if email is already taken
  if (empty($errors)) {
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
      $errors[] = 'An account with that email already exists.';
    }
  }

  // Create the account
  if (empty($errors)) {
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare(
      'INSERT INTO users (name, email, password, avatar, created_at)
       VALUES (?, ?, ?, ?, NOW())'
    );
    $stmt->execute([$name, $email, $hashed, 'junimo_0']);

    header('Location: login.php?registered=1');
    exit;
  }
}
?>

<?php
  $activePage = '';
  include 'includes/header.php';
?>

<div class="auth-page">
  <div class="auth-card">

    <div class="auth-header">
      <a href="index.php" class="logo">CvSU <span>Marketplace</span></a>
      <h1>Create an account</h1>
      <p>Join the CvSU student marketplace for free</p>
    </div>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-error">
        <ul>
          <?php foreach ($errors as $err): ?>
            <li><?= htmlspecialchars($err) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="POST" class="auth-form">

      <div class="form-group">
        <label for="name">Full Name <span class="required">*</span></label>
        <input type="text" id="name" name="name" placeholder="e.g. Spenzer Lima" value="<?= htmlspecialchars($name) ?>" maxlength="80" required/>
      </div>

      <div class="form-group">
        <label for="email">Email Address <span class="required">*</span></label>
        <input type="email" id="email" name="email" placeholder="firstname.lastname@cvsu.edu.ph" value="<?= htmlspecialchars($email) ?>" required/>
      </div>

      <div class="form-group">
        <label for="password">Password <span class="required">*</span></label>
        <div class="password-input">
          <input type="password" id="password" name="password" placeholder="At least 8 characters" required/>
          <button type="button" class="toggle-password" onclick="togglePassword('password', this)" aria-label="Show password">
            <span class="icon-visibility-on"><?= $visibilityOnIcon ?></span>
            <span class="icon-visibility-off" style="display:none;"><?= $visibilityOffIcon ?></span>
          </button>
        </div>
      </div>

      <div class="form-group">
        <label for="confirm">Confirm Password <span class="required">*</span></label>
        <div class="password-input">
          <input type="password" id="confirm" name="confirm" placeholder="Repeat your password" required/>
          <button type="button" class="toggle-password" onclick="togglePassword('confirm', this)" aria-label="Show password">
            <span class="icon-visibility-on"><?= $visibilityOnIcon ?></span>
            <span class="icon-visibility-off" style="display:none;"><?= $visibilityOffIcon ?></span>
          </button>
        </div>
        <span class="form-hint" id="matchHint"></span>
      </div>

      <button type="submit" class="btn-submit">Create Account</button>

    </form>

    <p class="auth-switch">
      Already have an account? <a href="login.php">Log in</a>
    </p>

  </div>
</div>

<script>
  function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    const iconOn = btn.querySelector('.icon-visibility-on');
    const iconOff = btn.querySelector('.icon-visibility-off');

    if (input.type === 'password') {
      input.type = 'text';
      iconOn.style.display = 'none';
      iconOff.style.display = 'inline-flex';
    } else {
      input.type = 'password';
      iconOn.style.display = 'inline-flex';
      iconOff.style.display = 'none';
    }
  }

  const password = document.getElementById('password');
  const confirm = document.getElementById('confirm');
  const hint = document.getElementById('matchHint');

  function checkMatch() {
    if (confirm.value === '') {
      hint.textContent = '';
      hint.style.color = '';
      return;
    }
    if (password.value === confirm.value) {
      hint.textContent = 'Passwords match';
      hint.style.color = 'var(--primary)';
    } else {
      hint.textContent = 'Passwords do not match';
      hint.style.color = '#b94040';
    }
  }

  password.addEventListener('input', checkMatch);
  confirm.addEventListener('input', checkMatch);
</script>

<?php include 'includes/footer.php'; ?>