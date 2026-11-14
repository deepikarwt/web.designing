<?php
require_once 'common.php';
require_login();

$_SESSION['cart'] ??= [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($action === 'add' && $id) {
        $stmt = $db->prepare('SELECT id FROM products WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();

        if ($stmt->get_result()->num_rows) {
            $_SESSION['cart'][$id] = min(
                99,
                ($_SESSION['cart'][$id] ?? 0) + 1
            );
        }
    } elseif ($action === 'update') {
        foreach ($_POST['qty'] ?? [] as $key => $value) {
            $key = (int)$key;
            $quantity = (int)$value;

            if ($quantity < 1) {
                unset($_SESSION['cart'][$key]);
            } else {
                $_SESSION['cart'][$key] = min(99, $quantity);
            }
        }
    } elseif ($action === 'remove') {
        unset($_SESSION['cart'][$id]);
    }

    header('Location: cart.php');
    exit;
}

$items = [];
$total = 0;

if ($_SESSION['cart']) {
    $ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $types = str_repeat('i', count($ids));

    $stmt = $db->prepare(
        "SELECT id, name, price FROM products WHERE id IN ($placeholders)"
    );
    $stmt->bind_param($types, ...$ids);
    $stmt->execute();

    $result = $stmt->get_result();

    while ($item = $result->fetch_assoc()) {
        $item['quantity'] = $_SESSION['cart'][$item['id']];
        $item['subtotal'] = $item['price'] * $item['quantity'];
        $total += $item['subtotal'];
        $items[] = $item;
    }
}

page_start('Cart');
?>

<h1>Your cart</h1>

<?php if (!$items): ?>
    <p>Your cart is empty.</p>
<?php else: ?>
    <form method="post">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="action" value="update">

        <table>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
                <th>Remove</th>
            </tr>

            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= e($item['name']) ?></td>
                    <td>₹<?= e($item['price']) ?></td>
                    <td>
                        <input type="number" min="0" max="99"
                               name="qty[<?= (int)$item['id'] ?>]"
                               value="<?= (int)$item['quantity'] ?>">
                    </td>
                    <td>₹<?= number_format($item['subtotal'], 2) ?></td>
                    <td>
                        <button type="submit"
                                formaction="cart.php"
                                formmethod="post"
                                name="action"
                                value="remove">
                            Remove
                        </button>
                        <input type="hidden" name="id"
                               value="<?= (int)$item['id'] ?>">
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <button type="submit" name="action" value="update">
            Update quantities
        </button>
    </form>

    <h2>Total ₹<?= number_format($total, 2) ?></h2>
    <a class="button" href="checkout.php">Checkout</a>
<?php endif; ?>

<?php page_end(); ?>