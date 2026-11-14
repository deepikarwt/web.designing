<?php
require_once APP_ROOT . '/models/Product.php';

final class ProductController
{
    private Product $products;

    public function __construct()
    {
        $this->products = new Product();
    }

    public function index(): void
    {
        $products = $this->products->all();
        require APP_ROOT . '/views/products.php';
    }

    public function api(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $method = $_SERVER['REQUEST_METHOD'];
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if ($method !== 'GET') {
            ApiAuth::requireToken();
        }

        try {
            if ($method === 'GET') {
                $result = $id ? $this->products->find($id) : $this->products->all();
                echo json_encode($result);
                return;
            }

            $body = json_decode(file_get_contents('php://input'), true) ?? [];

            if ($method === 'POST') {
                $data = $this->validateProduct($body);
                $newId = $this->products->create($data);
                http_response_code(201);
                echo json_encode(['id' => $newId]);
                return;
            }

            if ($method === 'PUT' && $id) {
                $this->products->update($id, $this->validateProduct($body));
                echo json_encode(['updated' => true]);
                return;
            }

            if ($method === 'DELETE' && $id) {
                $this->products->delete($id);
                echo json_encode(['deleted' => true]);
                return;
            }

            http_response_code(400);
            echo json_encode(['error' => 'Invalid API request']);
        } catch (Throwable $e) {
            error_log($e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Server error']);
        }
    }

    private function validateProduct(array $input): array
    {
        $name = filter_var(trim($input['name'] ?? ''), FILTER_SANITIZE_SPECIAL_CHARS);
        $description = filter_var(
            trim($input['description'] ?? ''),
            FILTER_SANITIZE_SPECIAL_CHARS
        );
        $price = filter_var($input['price'] ?? null, FILTER_VALIDATE_FLOAT);

        if (!$name || $price === false || $price < 0) {
            throw new InvalidArgumentException('Invalid product data');
        }

        return [
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'image_path' => null
        ];
    }
}