<?php
final class ApiAuth
{
    public static function requireToken(): void
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        $token = str_starts_with($header, 'Bearer ')
            ? substr($header, 7)
            : '';

        if (!$token) {
            http_response_code(401);
            echo json_encode(['error' => 'Bearer token required']);
            exit;
        }

        $hash = hash('sha256', $token);
        $db = Database::connection();
        $stmt = $db->prepare(
            'SELECT id FROM api_tokens WHERE token_hash = ? AND expires_at > NOW()'
        );
        $stmt->execute([$hash]);

        if (!$stmt->fetch()) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid or expired token']);
            exit;
        }
    }
}