<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

try {
    require __DIR__ . '/db.php';
    $stmt = $conn->prepare('SELECT command, updated_at FROM robot_state WHERE id = 1');
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    $conn->close();

    if (!$row) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Robot state row is missing']);
        exit;
    }
    echo json_encode(['status' => 'success', 'command' => $row['command'], 'updated_at' => $row['updated_at']]);
} catch (Throwable $error) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Unable to read robot state']);
}
