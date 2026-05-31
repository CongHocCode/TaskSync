<?php
session_start();

// Nạp các file lõi của hệ thống
require_once '../app/core/App.php';
require_once '../app/core/Controller.php';
require_once '../app/core/Database.php';

// Bật công tắc chạy hệ thống
$app = new App();