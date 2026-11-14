<?php
$title = 'Login';
require APP_ROOT . '/views/header.php';
?>

<h1>Login</h1>

<?php if ($error): ?>
    <p><?= e($error) ?></p>
<?php endif; ?>

<form method="post" action="/login">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

    <label>Email
        <input type="email" name="email" required>
    </label>

    <label>Password
        <input type="password" name="password" required>
    </label>

    <button type="submit">Login</button>
</form>

<?php require APP_ROOT . '/views/footer.php'; ?>