<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

header('Content-Type: application/json');

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

switch ($action) {
    case 'add':
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
        $qty = isset($_REQUEST['qty']) ? (int)$_REQUEST['qty'] : 1;
        $name = isset($_REQUEST['name']) ? $_REQUEST['name'] : '';
        $subname = isset($_REQUEST['subname']) ? $_REQUEST['subname'] : '';
        $img = isset($_REQUEST['img']) ? $_REQUEST['img'] : '';

        if (!empty($id)) {
            if (isset($_SESSION['cart'][$id])) {
                $_SESSION['cart'][$id]['qty'] += $qty;
            } else {
                $_SESSION['cart'][$id] = [
                    'id' => $id,
                    'qty' => $qty,
                    'name' => $name,
                    'subname' => $subname,
                    'img' => $img
                ];
            }
            echo json_encode(['success' => true, 'cart' => $_SESSION['cart']]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid ID']);
        }
        break;

    case 'update':
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
        $qty = isset($_REQUEST['qty']) ? (int)$_REQUEST['qty'] : 1;
        if (!empty($id) && isset($_SESSION['cart'][$id])) {
            if ($qty > 0) {
                $_SESSION['cart'][$id]['qty'] = $qty;
            } else {
                unset($_SESSION['cart'][$id]);
            }
            echo json_encode(['success' => true, 'cart' => $_SESSION['cart']]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid ID or item not in cart']);
        }
        break;

    case 'remove':
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
        if (!empty($id) && isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
            echo json_encode(['success' => true, 'cart' => $_SESSION['cart']]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid ID']);
        }
        break;

    case 'get':
        echo json_encode(['success' => true, 'cart' => $_SESSION['cart']]);
        break;

    case 'clear':
        $_SESSION['cart'] = [];
        echo json_encode(['success' => true]);
        break;

    default:
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
        break;
}
