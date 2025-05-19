<?php
header('Content-Type: application/json');
require 'private/php/auth.php';

if (!isAuthorized()) {
    http_response_code(401);
    echo json_encode(["error" => "Érvénytelen vagy hiányzó token."]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
$resource = $uri[1] ?? '';
$id = $uri[2] ?? null;

switch ($resource) {
    case 'students':
        require 'private/php/students.php';
        handleStudents($method, $id);
        break;
    case 'subjects':
        require 'private/php/subjects.php';
        handleSubjects($method);
        break;
    case 'grades':
        require 'private/php/grades.php';
        handleGrades($method, $id);
        break;
    default:
        http_response_code(404);
        echo json_encode(["error" => "Nem létező végpont."]);
}