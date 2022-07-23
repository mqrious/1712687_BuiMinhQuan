<?php

require_once('common/db.php');
require_once('common/session.php');
require_once('controller/ServiceController.php');
require_once('model/Service.php');

$requestMethod = strtolower($_SERVER['REQUEST_METHOD']);
$action = $_REQUEST['action'] ?? '';
$controller = new ServiceController();

if ($requestMethod === 'get') {
    switch ($action) {
        case '':
            $controller->getAll();
            break;
        default:
            http_response_code(404);
            echo 'Not found';
            die();
    }
} else if ($requestMethod === 'post') {
    switch ($action) {
        default:
            http_response_code(404);
            echo 'Not found';
            die();
    }
}