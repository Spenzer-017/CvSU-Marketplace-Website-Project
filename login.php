<?php
/*
  login.php - User Login
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
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $password = trim($_POST['password'] ?? '');

  // Validation
  if ($email === '') $errors[] = 'Email is required.';
  if ($password === '') $errors[] = 'Password is required.';

  if (empty($errors)) {
    // Look up user by email
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
      $errors[] = 'Incorrect email or password.';
    } else {
      // Login success - store user in session
      $_SESSION['user'] = [
        'id' => $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'course' => $user['course'],
        'avatar' => $user['avatar'],
      ];
      header('Location: dashboard.php');
      exit;
    }
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
      <h1>Welcome back</h1>
      <p>Log in to your student account</p>
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

    <?php if (isset($_GET['registered'])): ?>
      <div class="alert alert-success">
        Account created! You can now log in.
      </div>
    <?php endif; ?>

    <?php if (isset($_GET['logged_out'])): ?>
      <div class="alert alert-success">
        You have been logged out.
      </div>
    <?php endif; ?>

    <form method="POST" class="auth-form">

      <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" placeholder="firstname.lastname@cvsu.edu.ph" value="<?= htmlspecialchars($email) ?>" required/>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <div class="password-input">
          <input type="password" id="password" name="password" placeholder="Enter your password" required/>
          <button type="button" class="toggle-password" onclick="togglePassword('password', this)">Show</button>
        </div>
      </div>

      <button type="submit" class="btn-submit">Log In</button>

    </form>

    <p class="auth-switch">
      Don't have an account? <a href="signup.php">Sign up</a>
    </p>

  </div>
</div>

<script>
  function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    if (input.type === 'password') {
      input.type = 'text';
      btn.textContent = 'Hide';
    } else {
      input.type = 'password';
      btn.textContent = 'Show';
    }
  }
</script>

<?php include 'includes/footer.php'; ?>