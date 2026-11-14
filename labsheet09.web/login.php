<?php
require_once 'common.php';

$error = '';

if (!empty($_COOKIE['remember'])) {
    $parts = explode(':', $_COOKIE['remember'], 2);

    if (count($parts) === 2) {
        $stmt = $db->prepare(
            'SELECT id, name, remember_token_hash FROM users WHERE id = ?'
        );
        $stmt->bind_param('i', $parts[0]);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if (
            $user &&
            hash_equals(
                (string)$user['remember_token_hash'],
                hash('sha256', $parts[1])
            )
        ) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            header('Location: dashboard.php');
            exit;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';

    $stmt = $db->prepare(
        'SELECT id, name, password FROM users WHERE email = ?'
    );
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];

        if (!empty($_POST['remember'])) {
            $rawToken = bin2hex(random_bytes(32));
            $tokenHash = hash('sha256', $rawToken);

            $stmt = $db->prepare(
                'UPDATE users SET remember_token_hash = ? WHERE id = ?'
            );
            $stmt->bind_param('si', $tokenHash, $user['id']);
            $stmt->execute();

            setcookie(
                'remember',
                $user['id'] . ':' . $rawToken,
                [
                    'expires' => time() + 60 * 60 * 24 * 30,
                    'path' => '/',
                    'httponly' => true,
                    'secure' => !empty($_SERVER['HTTPS']),
                    'samesite' => 'Lax'
                ]
            );
        }

        header('Location: dashboard.php');
        exit;
    }

    $error = 'Email or password is incorrect.';
}

page_start('Login');
?>

<h1>Login</h1>

<?php if ($error): ?>
    <p class="error"><?= e($error) ?></p>
<?php endif; ?>

<form method="post">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

    <label>
        Email
        <input type="email" name="email" required>
    </label>

    <label>
        Password
        <input type="password" name="password" required>
    </label>

    <label class="inline">
        <input type="checkbox" name="remember" value="1">
        Remember me
    </label>

    <button>Login</button>
    <p><a href="register.php">Create an account</a></p>
</form>

<?php page_end(); ?>