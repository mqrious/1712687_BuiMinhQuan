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
            $controller->responseNotFound();
    }
} else if ($requestMethod === 'post') {
    switch ($action) {
        default:
            $controller->responseNotFound();
    }
}