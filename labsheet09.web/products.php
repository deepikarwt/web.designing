<?php
require_once 'common.php';
require_login();

function sort_clause(string $sort): string
{
    $valid = [
        'name' => 'name',
        'category' => 'category',
        'price' => 'price',
        'created_at' => 'created_at'
    ];

    $field = substr($sort, 1);

    if (!isset($valid[$field])) {
        return 'name ASC';
    }

    return $valid[$field] . (str_starts_with($sort, '-') ? ' DESC' : ' ASC');
}

$q = trim($_GET['q'] ?? '');
$category = trim($_GET['category'] ?? '');
$min = filter_var($_GET['min'] ?? '', FILTER_VALIDATE_FLOAT);
$max = filter_var($_GET['max'] ?? '', FILTER_VALIDATE_FLOAT);
$page = max(1, (int)($_GET['page'] ?? 1));

$limit = 8;
$offset = ($page - 1) * $limit;

$where = [];
$params = [];
$types = '';

if ($q !== '') {
    $where[] = 'name LIKE ?';
    $params[] = '%' . $q . '%';
    $types .= 's';
}

if ($category !== '') {
    $where[] = 'category = ?';
    $params[] = $category;
    $types .= 's';
}

if ($min !== false && $min !== null && $min !== '') {
    $where[] = 'price >= ?';
    $params[] = $min;
    $types .= 'd';
}

if ($max !== false && $max !== null && $max !== '') {
    $where[] = 'price <= ?';
    $params[] = $max;
    $types .= 'd';
}

$whereClause = $where ? ' WHERE ' . implode(' AND ', $where) : '';

$countStmt = $db->prepare(
    'SELECT COUNT(*) AS total FROM products' . $whereClause
);

if ($types) {
    $countStmt->bind_param($types, ...$params);
}

$countStmt->execute();
$total = (int)$countStmt->get_result()->fetch_assoc()['total'];
$totalPages = max(1, (int)ceil($total / $limit));

$sort = $_GET['sort'] ?? 'name';
$orderBy = sort_clause($sort);

$sql = 'SELECT * FROM products' . $whereClause .
       ' ORDER BY ' . $orderBy . ' LIMIT ? OFFSET ?';

$stmt = $db->prepare($sql);

$queryParams = $params;
$queryTypes = $types . 'ii';
$queryParams[] = $limit;
$queryParams[] = $offset;

$stmt->bind_param($queryTypes, ...$queryParams);
$stmt->execute();
$products = $stmt->get_result();

function sort_link(string $field): string
{
    $current = $_GET['sort'] ?? 'name';
    $next = $current === $field ? '-' . $field : $field;
    $params = $_GET;
    $params['sort'] = $next;

    return '?' . http_build_query($params);
}

page_start('Products');
?>

<h1>Products</h1>
<p><a class="button" href="add_product.php">Add product</a></p>

<form class="filters" method="get">
    <input name="q" placeholder="Search product name" value="<?= e($q) ?>">
    <input name="category" placeholder="Category" value="<?= e($category) ?>">
    <input type="number" step="0.01" name="min"
           placeholder="Min price" value="<?= e($_GET['min'] ?? '') ?>">
    <input type="number" step="0.01" name="max"
           placeholder="Max price" value="<?= e($_GET['max'] ?? '') ?>">
    <button>Search</button>
</form>

<p id="suggestions"></p>

<table>
    <thead>
    <tr>
        <th><a href="<?= e(sort_link('name')) ?>">Name ↕</a></th>
        <th><a href="<?= e(sort_link('category')) ?>">Category ↕</a></th>
        <th><a href="<?= e(sort_link('price')) ?>">Price ↕</a></th>
        <th>Photo</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php while ($product = $products->fetch_assoc()): ?>
        <tr>
            <td><?= e($product['name']) ?></td>
            <td><?= e($product['category']) ?></td>
            <td>₹<?= e($product['price']) ?></td>
            <td>
                <?php if ($product['image']): ?>
                    <img class="thumb"
                         src="uploads/<?= e($product['image']) ?>"
                         alt="<?= e($product['name']) ?>">
                <?php endif; ?>
            </td>
            <td>
                <a href="edit_product.php?id=<?= (int)$product['id'] ?>">Edit</a>

                <form class="inline" method="post" action="delete_product.php"
                      onsubmit="return confirm('Delete this product?')">
                    <input type="hidden" name="csrf"
                           value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="id"
                           value="<?= (int)$product['id'] ?>">
                    <button class="danger">Delete</button>
                </form>

                <form class="inline" method="post" action="cart.php">
                    <input type="hidden" name="csrf"
                           value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="id"
                           value="<?= (int)$product['id'] ?>">
                    <button>Add to cart</button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

<nav class="pages">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <?php $params = $_GET; $params['page'] = $i; ?>
        <a class="<?= $i === $page ? 'current' : '' ?>"
           href="?<?= e(http_build_query($params)) ?>">
            <?= $i ?>
        </a>
    <?php endfor; ?>
</nav>

<?php if ($page > 1): ?>
    <?php $params = $_GET; $params['page'] = $page - 1; ?>
    <a href="?<?= e(http_build_query($params)) ?>">Previous</a>
<?php endif; ?>

<?php if ($page < $totalPages): ?>
    <?php $params = $_GET; $params['page'] = $page + 1; ?>
    <a href="?<?= e(http_build_query($params)) ?>">Next</a>
<?php endif; ?>

<?php page_end(); ?>