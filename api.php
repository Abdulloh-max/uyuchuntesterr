<?php
require_once 'config.php';

header('Content-Type: application/json');

$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'set_currency':
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['currency']) && in_array($data['currency'], ['UZS', 'USD'])) {
            $_SESSION['currency'] = $data['currency'];
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid currency']);
        }
        break;

    case 'order':
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['name']) || !isset($data['phone']) || !isset($data['product']) || !isset($data['price'])) {
            echo json_encode(['success' => false, 'error' => 'Missing required fields']);
            break;
        }
        
        $orderData = [
            'name' => strip_tags($data['name']),
            'phone' => strip_tags($data['phone']),
            'product' => strip_tags($data['product']),
            'price' => strip_tags($data['price']),
            'product_id' => isset($data['product_id']) ? intval($data['product_id']) : null
        ];
        
        if (saveOrder($orderData)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to save order']);
        }
        break;

    case 'get_products':
        $brand = isset($_GET['brand']) ? $_GET['brand'] : 'all';
        $products = getProducts();
        
        if ($brand !== 'all') {
            $products = array_filter($products, function($p) use ($brand) {
                return $p['brand'] === $brand;
            });
            $products = array_values($products);
        }
        
        $currency = isset($_SESSION['currency']) ? $_SESSION['currency'] : 'UZS';
        foreach ($products as &$p) {
            $p['price_formatted'] = formatPrice(convertCurrency($p['price'], 'UZS', $currency));
        }
        
        echo json_encode(['success' => true, 'products' => $products, 'currency' => $currency]);
        break;

    default:
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
        break;
}   