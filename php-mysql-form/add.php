<?php
/**
 * add.php — saves one person into the database.
 *
 * Called by script.js when the form is submitted.
 * Answers with JSON: { ok: true, person: { id, name, age, status } }
 */
require __DIR__ . '/db.php';

// Only accept POST — you can't add a person by visiting this URL.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json(['ok' => false, 'error' => 'Use POST.'], 405);
}

// ---- validate what came from the browser ---------------------------------
// Never trust the form: the HTML `required` attribute is easy to bypass,
// so everything is checked again here on the server.
$name = trim($_POST['name'] ?? '');
$age  = $_POST['age'] ?? '';

if ($name === '') {
    send_json(['ok' => false, 'error' => 'Name is required.'], 422);
}

if (mb_strlen($name) > 100) {
    send_json(['ok' => false, 'error' => 'Name is too long (100 characters max).'], 422);
}

if (!ctype_digit((string) $age) || (int) $age < 1 || (int) $age > 120) {
    send_json(['ok' => false, 'error' => 'Age must be a whole number between 1 and 120.'], 422);
}

$age = (int) $age;

// ---- insert ---------------------------------------------------------------
// The ? placeholders are what stop SQL injection: the values are sent to
// MySQL separately from the query, so they can never be read as SQL.
$stmt = $pdo->prepare('INSERT INTO people (name, age, status) VALUES (?, ?, 0)');
$stmt->execute([$name, $age]);

$id = (int) $pdo->lastInsertId();

// Send the saved row back so the JavaScript can draw it in the table.
send_json([
    'ok'     => true,
    'person' => [
        'id'     => $id,
        'name'   => $name,
        'age'    => $age,
        'status' => 0,
    ],
]);
