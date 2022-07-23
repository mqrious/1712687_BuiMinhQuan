<?php

require_once('common/db.php');
require_once('common/session.php');
require_once 'controller/Controller.php';
require_once('controller/OrderController.php');
require_once('model/EOrderStatus.php');
require_once('model/Service.php');
require_once('model/Order.php');

$requestMethod = strtolower($_SERVER['REQUEST_METHOD']);
$action = $_REQUEST['action'] ?? '';
$controller = new OrderController();

if ($requestMethod === 'get') {
    switch ($action) {
        case 'listAll':
            $controller->listAll();
            break;
        case 'viewOrder':
            $controller->viewOrder();
            break;
        case 'create':
            $controller->create();
            break;
        case 'find':
            $controller->find();
            break;
        case 'success':
            $controller->success();
            break;
        default:
            $controller->responseNotFound();
    }
} else if ($requestMethod === 'post') {
    switch ($action) {
        case 'queryOrder':
            $controller->queryOrder();
            break;
        case 'createOrder':
            $controller->createOrder();
            break;
        case 'findOrder':
            $controller->findOrder();
            break;
        case 'cancelOrder':
            $controller->cancelOrder();
            break;
        case 'updateOrderStatus':
            $controller->updateOrderStatus();
            break;
        default:
            $controller->responseNotFound();
    }
}