<?php
require_once 'database/db.php';

function handleGrades($method, $id) {
    global $pdo;
    if ($method === 'GET') {
        if (isset($_GET['student_id'])) {
            $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
            $stmt->execute([$_GET['student_id']]);
            if (!$stmt->fetch()) {
                echo json_encode([]);
                return;
            }

            $stmt = $pdo->prepare("SELECT * FROM grades WHERE student_id = ?");
            $stmt->execute([$_GET['student_id']]);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        } else {
            $stmt = $pdo->query("SELECT * FROM grades");
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        }
    }

    elseif ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['subject_id'], $input['student_id'], $input['grade'])) {
            http_response_code(400);
            echo json_encode(["error" => "subject_id, student_id és grade kötelező."]);
            return;
        }

        $grade = (int)$input['grade'];
        if ($grade < 1 || $grade > 5) {
            http_response_code(400);
            echo json_encode(["error" => "A jegy csak 1 és 5 közötti szám lehet."]);
            return;
        }

        $stmt = $pdo->prepare("SELECT 1 FROM students WHERE id = ?");
        $stmt->execute([$input['student_id']]);
        if (!$stmt->fetch()) {
            http_response_code(400);
            echo json_encode(["error" => "A tanuló nem létezik."]);
            return;
        }

        $stmt = $pdo->prepare("SELECT 1 FROM subjects WHERE id = ?");
        $stmt->execute([$input['subject_id']]);
        if (!$stmt->fetch()) {
            http_response_code(400);
            echo json_encode(["error" => "A tárgy nem létezik."]);
            return;
        }

        $stmt = $pdo->prepare("INSERT INTO grades (student_id, subject_id, grade) VALUES (?, ?, ?)");
        $stmt->execute([$input['student_id'], $input['subject_id'], $grade]);
        http_response_code(201);
    }

    elseif ($method === 'DELETE') {
        $stmt = $pdo->prepare("SELECT * FROM grades WHERE id = ?");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) {
            http_response_code(404);
            echo json_encode(["error" => "A jegy nem létezik."]);
            return;
        }

        $stmt = $pdo->prepare("DELETE FROM grades WHERE id = ?");
        $stmt->execute([$id]);
        http_response_code(204);
    }

    else {
        http_response_code(405);
        echo json_encode(["error" => "Nem támogatott metódus."]);
    }
}