<?php
require_once 'database/db.php';

function handleSubjects($method) {
    global $pdo;
    if ($method === 'GET') {
        $stmt = $pdo->query("SELECT * FROM subjects");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    } else {
        http_response_code(405);
        echo json_encode(["error" => "Nem támogatott metódus."]);
    }
}