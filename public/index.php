<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/StudentRepository.php';
require_once __DIR__ . '/../src/StudentController.php';

use App\StudentController;
use App\StudentRepository;

$repository = new StudentRepository(__DIR__ . '/../data/students.json');
$controller = new StudentController($repository);
$controller->handleRequest();
