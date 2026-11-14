<?php
require_once 'common.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $name = trim($_POST['name'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';

    if (strlen($name) < 2 || !$email || strlen($password) < 8) {
        $error = 'Enter a name, valid email, and password of at least 8 characters.';
    } else {
        try {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $db->prepare(
                'INSERT INTO users (name, email, password) VALUES (?, ?, ?)'
            );
            $stmt->bind_param('sss', $name, $email, $hash);
            $stmt->execute();

            header('Location: login.php');
            exit;
        } catch (mysqli_sql_exception $e) {
            $error = 'That email may already be registered.';
        }
    }
}

page_start('Register');
?>

<h1>Create account</h1>

<?php if (!empty($error)): ?>
    <p class="error"><?= e($error) ?></p>
<?php endif; ?>

<form method="post">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

    <label>
        Name
        <input name="name" required minlength="2"
               value="<?= e($_POST['name'] ?? '') ?>">
    </label>

    <label>
        Email
        <input type="email" name="email" required>
    </label>

    <label>
        Password
        <input type="password" name="password" required minlength="8">
    </label>

    <button>Register</button>
</form>

<?php page_end(); ?>