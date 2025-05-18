<?php
require_once 'database/db.php';

function handleStudents($method, $id) {
    global $pdo;

    if ($method === 'GET') {
        if ($id) {
            $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
            $stmt->execute([$id]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$student) {
                http_response_code(404);
                echo json_encode(["error" => "A tanuló nem létezik."]);
                return;
            }

            $stmt = $pdo->prepare("SELECT * FROM grades WHERE student_id = ?");
            $stmt->execute([$id]);
            $grades = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $student['grades'] = $grades;
            echo json_encode($student);
        } else {
            $query = "SELECT * FROM students";
            $params = [];

            if (isset($_GET['year'])) {
                $query .= " WHERE year = ?";
                $params[] = $_GET['year'];
            }

            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        }
    }

    elseif (in_array($method, ['PUT', 'PATCH'])) {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['name']) || !isset($input['year'])) {
            http_response_code(400);
            echo json_encode(["error" => "A 'name' és 'year' mezők kötelezőek."]);
            return;
        }

        $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
        $stmt->execute([$id]);
        $exists = $stmt->fetch();

        if ($method === 'PUT') {
            if ($exists) {
                $stmt = $pdo->prepare("UPDATE students SET name = ?, year = ? WHERE id = ?");
                $stmt->execute([$input['name'], $input['year'], $id]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO students (id, name, year) VALUES (?, ?, ?)");
                $stmt->execute([$id, $input['name'], $input['year']]);
            }
            http_response_code(204);
        }

        elseif ($method === 'PATCH') {
            if (!$exists) {
                http_response_code(404);
                echo json_encode(["error" => "A tanuló nem létezik."]);
                return;
            }
            $stmt = $pdo->prepare("UPDATE students SET name = ?, year = ? WHERE id = ?");
            $stmt->execute([$input['name'], $input['year'], $id]);
            http_response_code(204);
        }
    }

    else {
        http_response_code(405);
        echo json_encode(["error" => "Nem támogatott metódus."]);
    }
}
