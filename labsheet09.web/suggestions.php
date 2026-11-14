<?php
require_once 'common.php';
require_login();

header('Content-Type: application/json; charset=utf-8');

$term = trim($_GET['q'] ?? '');

if (strlen($term) < 2) {
    echo json_encode([]);
    exit;
}

$like = '%' . $term . '%';

$stmt = $db->prepare(
    'SELECT name, category
     FROM products
     WHERE name LIKE ? OR category LIKE ?
     ORDER BY name
     LIMIT 8'
);

$stmt->bind_param('ss', $like, $like);
$stmt->execute();

echo json_encode(
    $stmt->get_result()->fetch_all(MYSQLI_ASSOC),
    JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
);