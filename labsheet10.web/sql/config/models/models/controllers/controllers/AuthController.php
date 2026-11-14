<?php
require_once APP_ROOT . '/models/User.php';

final class AuthController
{
    public function login(): void
    {
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            check_csrf();

            $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
            $password = $_POST['password'] ?? '';

            if (!$email) {
                $error = 'Enter a valid email address.';
            } else {
                $users = new User();
                $user = $users->findByEmail($email);

                if ($user && !empty($user['locked_until']) &&
                    strtotime($user['locked_until']) > time()) {
                    $error = 'Account temporarily locked. Try again later.';
                } elseif ($user && password_verify($password, $user['password_hash'])) {
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = (int)$user['id'];
                    $_SESSION['user_name'] = $user['name'];

                    $db = Database::connection();
                    $stmt = $db->prepare(
                        'UPDATE users SET failed_attempts = 0, locked_until = NULL WHERE id = ?'
                    );
                    $stmt->execute([$user['id']]);

                    header('Location: /');
                    exit;
                } else {
                    if ($user) {
                        $attempts = (int)$user['failed_attempts'] + 1;
                        $lockedUntil = $attempts >= 5
                            ? date('Y-m-d H:i:s', time() + 15 * 60)
                            : null;

                        $db = Database::connection();
                        $stmt = $db->prepare(
                            'UPDATE users SET failed_attempts = ?, locked_until = ? WHERE id = ?'
                        );
                        $stmt->execute([$attempts, $lockedUntil, $user['id']]);
                    }

                    $error = 'Email or password is incorrect.';
                }
            }
        }

        require APP_ROOT . '/views/login.php';
    }
}