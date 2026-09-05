<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../Controller/AuthController.php';
$controller = new AuthController();
$controller->logout();
header("Location: login.php");
exit;
