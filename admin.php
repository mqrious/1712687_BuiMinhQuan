<?php

require_once('common/db.php');
require_once('common/session.php');
require_once('controller/Controller.php');
require_once('controller/AdminController.php');
require_once('model/Model.php');
require_once('model/Account.php');

$requestMethod = strtolower($_SERVER['REQUEST_METHOD']);
$action = $_REQUEST['action'] ?? '';
$controller = new AdminController();

if ($requestMethod === 'get') {
    switch ($action) {
        case '':
            $controller->index();
            break;
        case 'login':
            $controller->login();
            break;
        case 'logout':
            $controller->logout();
            break;
        default:
            $controller->responseNotFound();
    }
} else if ($requestMethod === 'post') {
    switch ($action) {
        case 'login':
            $controller->loginAdmin();
            break;
        default:
            $controller->responseNotFound();
    }
}
