<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

$map = [
    'forward' => 'f',
    'backward' => 'b',
    'left' => 'l',
    'right' => 'r',
    'stop' => 'S',
];

$button = is_string($_POST['command'] ?? null) ? strtolower(trim($_POST['command'])) : '';
if (!isset($map[$button])) {
    http_response_code(422);
    echo json_encode(['status' => 'error', 'message' => 'Unknown robot command']);
    exit;
}

try {
    require __DIR__ . '/db.php';
    $stmt = $conn->prepare('UPDATE robot_state SET command = ? WHERE id = 1');
    $letter = $map[$button];
    $stmt->bind_param('s', $letter);
    $stmt->execute();
    if ($stmt->affected_rows === 0) {
        $check = $conn->query('SELECT id FROM robot_state WHERE id = 1');
        if (!$check || !$check->fetch_assoc()) {
            throw new RuntimeException('Robot state row is missing');
        }
    }
    $stmt->close();

    $state = $conn->query('SELECT command, updated_at FROM robot_state WHERE id = 1')->fetch_assoc();
    $conn->close();
    echo json_encode(['status' => 'success', 'command' => $button, 'stored_as' => $state['command'], 'updated_at' => $state['updated_at']]);
} catch (Throwable $error) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Unable to update robot state']);
}
