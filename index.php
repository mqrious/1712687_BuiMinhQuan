<?php

require_once('common/db.php');
require_once('common/session.php');
require_once('controller/HomeController.php');

$action = $_REQUEST["action"] ?? '';

$controller = new HomeController();

switch ($action) {
    default:
        $controller->index();
        break;
}
