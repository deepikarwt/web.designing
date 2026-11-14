<?php
require_once 'common.php';
require_login();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    exit('Invalid ID');
}

$stmt = $db->prepare('SELECT * FROM products WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    exit('Product not found');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $price = filter_var($_POST['price'] ?? '', FILTER_VALIDATE_FLOAT);
    $description = trim($_POST['description'] ?? '');

    if (!$name || !$category || $price === false || $price < 0) {
        $error = 'Enter valid values.';
    } else {
        $image = $product['image'];

        if (!empty($_FILES['image']['name'])) {
            if (
                $_FILES['image']['error'] !== UPLOAD_ERR_OK ||
                $_FILES['image']['size'] > 2097152
            ) {
                throw new RuntimeException('Image must be under 2 MB.');
            }

            $mime = (new finfo(FILEINFO_MIME_TYPE))
                ->file($_FILES['image']['tmp_name']);

            $allowed = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp'
            ];

            if (!isset($allowed[$mime])) {
                throw new RuntimeException('Use JPG, PNG, or WebP.');
            }

            $image = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];

            move_uploaded_file(
                $_FILES['image']['tmp_name'],
                __DIR__ . '/uploads/' . $image
            );
        }

        $stmt = $db->prepare(
            'UPDATE products
             SET name = ?, category = ?, description = ?, price = ?, image = ?
             WHERE id = ?'
        );
        $stmt->bind_param(
            'sssdsi',
            $name,
            $category,
            $description,
            $price,
            $image,
            $id
        );
        $stmt->execute();

        header('Location: products.php');
        exit;
    }
}

page_start('Edit product');
?>

<h1>Edit product</h1>

<?php if (!empty($error)): ?>
    <p class="error"><?= e($error) ?></p>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

    <label>Name
        <input name="name" value="<?= e($product['name']) ?>" required>
    </label>

    <label>Category
        <input name="category" value="<?= e($product['category']) ?>" required>
    </label>

    <label>Description
        <textarea name="description"><?= e($product['description']) ?></textarea>
    </label>

    <label>Price
        <input type="number" name="price" min="0" step="0.01"
               value="<?= e($product['price']) ?>" required>
    </label>

    <label>Replace photo
        <input type="file" name="image"
               accept="image/jpeg,image/png,image/webp">
    </label>

    <button>Save</button>
</form>

<?php page_end(); ?>