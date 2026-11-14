<?php
require_once 'common.php';
require_login();

if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $db->begin_transaction();

    try {
        $userId = (int)$_SESSION['user_id'];
        $total = 0;
        $items = [];

        $ids = array_keys($_SESSION['cart']);
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $types = str_repeat('i', count($ids));

        $stmt = $db->prepare(
            "SELECT id, name, price
             FROM products
             WHERE id IN ($placeholders)
             FOR UPDATE"
        );
        $stmt->bind_param($types, ...$ids);
        $stmt->execute();

        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $row['quantity'] = (int)$_SESSION['cart'][$row['id']];
            $items[] = $row;
            $total += (float)$row['price'] * $row['quantity'];
        }

        if (count($items) !== count($ids)) {
            throw new RuntimeException(
                'A product in your cart is no longer available.'
            );
        }

        $stmt = $db->prepare(
            'INSERT INTO orders (user_id, total) VALUES (?, ?)'
        );
        $stmt->bind_param('id', $userId, $total);
        $stmt->execute();
        $orderId = $db->insert_id;

        $itemStmt = $db->prepare(
            'INSERT INTO order_items
             (order_id, product_id, product_name, quantity, unit_price)
             VALUES (?, ?, ?, ?, ?)'
        );

        foreach ($items as $item) {
            $itemStmt->bind_param(
                'iisid',
                $orderId,
                $item['id'],
                $item['name'],
                $item['quantity'],
                $item['price']
            );
            $itemStmt->execute();
        }

        $db->commit();
        $_SESSION['cart'] = [];
        $done = true;
    } catch (Throwable $e) {
        $db->rollback();
        $error = $e->getMessage();
    }
}

page_start('Checkout');
?>

<h1>Checkout</h1>

<?php if (!empty($done)): ?>
    <p>Order #<?= (int)$orderId ?> placed. Thank you!</p>
<?php else: ?>
    <?php if (!empty($error)): ?>
        <p class="error"><?= e($error) ?></p>
    <?php endif; ?>

    <p>Confirm your order and save it to the database.</p>

    <form method="post">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <button>Place order</button>
    </form>
<?php endif; ?>

<?php page_end(); ?>