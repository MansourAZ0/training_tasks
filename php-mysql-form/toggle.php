<?php
/**
 * toggle.php — flips one person's status between 0 and 1.
 *
 * Called by script.js when a "Toggle" button is clicked.
 * Answers with JSON: { ok: true, id: 3, status: 1 }
 */
require __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json(['ok' => false, 'error' => 'Use POST.'], 405);
}

$id = $_POST['id'] ?? '';

if (!ctype_digit((string) $id) || (int) $id < 1) {
    send_json(['ok' => false, 'error' => 'Invalid id.'], 422);
}

$id = (int) $id;

// Flip the value inside MySQL itself: 1 - 0 = 1, and 1 - 1 = 0.
// Doing it in one query means two fast clicks can't overwrite each other.
$stmt = $pdo->prepare('UPDATE people SET status = 1 - status WHERE id = ?');
$stmt->execute([$id]);

if ($stmt->rowCount() === 0) {
    send_json(['ok' => false, 'error' => 'That person no longer exists.'], 404);
}

// Read the new value back so the page shows exactly what the database holds.
$stmt = $pdo->prepare('SELECT status FROM people WHERE id = ?');
$stmt->execute([$id]);
$status = (int) $stmt->fetchColumn();

send_json(['ok' => true, 'id' => $id, 'status' => $status]);
