<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

if (current_user()) {
    redirect('dashboard.php');
}

$error = '';
$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $token = $_POST['csrf'] ?? '';

    if (!csrf_validate($token)) {
        $error = 'Invalid session. Please try again.';
    } elseif ($name === '') {
        $error = 'Please enter your name.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } else {
        $stmt = db()->prepare('SELECT id FROM users WHERE email = :email');
        $stmt->execute([':email' => $email]);

        if ($stmt->fetch()) {
            $error = 'That email is already registered.';
        } else {
            $stmt = db()->prepare(
                'INSERT INTO users (name, email, password_hash, created_at)
                 VALUES (:name, :email, :hash, :created_at)'
            );
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':hash' => password_hash($password, PASSWORD_DEFAULT),
                ':created_at' => gmdate('c'),
            ]);

            $_SESSION['user_id'] = (int) db()->lastInsertId();
            redirect('dashboard.php');
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Create Account</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <main class="card">
    <h1>Create Account</h1>
    <?php if ($error): ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="post" action="register.php">
      <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
      <label>
        Name
        <input type="text" name="name" value="<?= htmlspecialchars($name) ?>" required>
      </label>
      <label>
        Email
        <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
      </label>
      <label>
        Password
        <input type="password" name="password" required>
      </label>
      <button type="submit">Create account</button>
    </form>
    <p class="muted">Already have an account? <a href="index.php">Log in</a>.</p>
  </main>
</body>
</html>
