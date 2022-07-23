<?php

require_once('common/db.php');
require_once('common/session.php');
require_once('controller/AdminController.php');

$action = $_REQUEST["action"] ?? '';

$controller = new AdminController();

switch ($action) {
    default:
        $controller->index();
        break;
}
