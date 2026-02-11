<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

$user = current_user();
if (!$user) {
    redirect('index.php');
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <main class="card">
    <h1>Welcome</h1>
    <p>You are signed in as <strong><?= htmlspecialchars($user['email']) ?></strong>.</p>
    <p class="muted">Name: <?= htmlspecialchars($user['name']) ?></p>
    <p><a href="logout.php">Sign out</a></p>
  </main>
</body>
</html>
