<?php
$title = 'Products';
require APP_ROOT . '/views/header.php';
?>

<h1>Products</h1>

<table>
    <thead>
    <tr>
        <th>Name</th>
        <th>Description</th>
        <th>Price</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($products as $product): ?>
        <tr>
            <td><?= e($product['name']) ?></td>
            <td><?= e($product['description'] ?? '') ?></td>
            <td><?= e($product['price']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<script src="/assets/app.js" defer></script>

<?php require APP_ROOT . '/views/footer.php'; ?>