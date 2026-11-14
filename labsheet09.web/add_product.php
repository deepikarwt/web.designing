<?php
require_once 'common.php';
require_login();

function upload_image(): ?string
{
    if (empty($_FILES['image']['name'])) {
        return null;
    }

    if (
        $_FILES['image']['error'] !== UPLOAD_ERR_OK ||
        $_FILES['image']['size'] > 2 * 1024 * 1024
    ) {
        throw new RuntimeException('Image must be under 2 MB.');
    }

    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($_FILES['image']['tmp_name']);
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp'
    ];

    if (!isset($allowed[$mime])) {
        throw new RuntimeException('Use JPG, PNG, or WebP.');
    }

    $filename = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
    $destination = __DIR__ . '/uploads/' . $filename;

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
        throw new RuntimeException('Upload failed.');
    }

    return $filename;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    try {
        $name = trim($_POST['name'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $price = filter_var($_POST['price'] ?? '', FILTER_VALIDATE_FLOAT);
        $description = trim($_POST['description'] ?? '');

        if (!$name || !$category || $price === false || $price < 0) {
            throw new RuntimeException(
                'Enter valid name, category, and non-negative price.'
            );
        }

        $image = upload_image();

        $stmt = $db->prepare(
            'INSERT INTO products (name, category, description, price, image)
             VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->bind_param(
            'sssds',
            $name,
            $category,
            $description,
            $price,
            $image
        );
        $stmt->execute();

        header('Location: products.php');
        exit;
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

page_start('Add product');
?>

<h1>Add product</h1>

<?php if (!empty($error)): ?>
    <p class="error"><?= e($error) ?></p>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

    <label>Name
        <input name="name" required>
    </label>

    <label>Category
        <input name="category" required>
    </label>

    <label>Description
        <textarea name="description"></textarea>
    </label>

    <label>Price
        <input type="number" name="price" min="0" step="0.01" required>
    </label>

    <label>Photo (JPG, PNG, WebP; max 2 MB)
        <input type="file" name="image"
               accept="image/jpeg,image/png,image/webp">
    </label>

    <button>Add product</button>
</form>

<?php page_end(); ?>