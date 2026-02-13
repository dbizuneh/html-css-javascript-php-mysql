<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/StudentRepository.php';
require_once __DIR__ . '/../src/StudentController.php';

use App\Database;
use App\StudentController;
use App\StudentRepository;

$pdo = Database::connectFromEnv();
$repository = new StudentRepository($pdo);
$controller = new StudentController($repository);
$controller->handleRequest();
