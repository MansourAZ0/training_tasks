<?php
/**
 * db.example.php — TEMPLATE for the database connection.
 * ---------------------------------------------------------------
 * This is the version kept in git. It holds placeholders only.
 *
 *   1. copy this file to db.php
 *   2. put your real credentials in the copy
 *
 * db.php is listed in .gitignore, so your real password never
 * reaches GitHub. Every other PHP file requires db.php, not this one.
 *
 * Get the four values from your InfinityFree control panel
 * (Client Area -> MySQL Databases).
 */

// ---- 1. your database settings -------------------------------------------
$DB_HOST = 'sqlXXX.infinityfree.com';   // e.g. sql107.infinityfree.com
$DB_NAME = 'ifX_XXXXXXXX_people';       // e.g. if0_37123456_people
$DB_USER = 'ifX_XXXXXXXX';              // e.g. if0_37123456
$DB_PASS = 'your_password_here';

// For testing on your own computer instead, use these:
// $DB_HOST = 'localhost';
// $DB_NAME = 'people_db';
// $DB_USER = 'root';
// $DB_PASS = '';

// ---- 2. connect -----------------------------------------------------------
try {
    $pdo = new PDO(
        "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            // throw an exception when a query fails, instead of failing silently
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            // return rows as associative arrays: $row['name']
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // use real prepared statements (protects against SQL injection)
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    // Never print $e->getMessage() on a live site — it can leak the password.
    http_response_code(500);
    exit('Database connection failed. Check your settings in db.php.');
}

/**
 * Small helper so the JSON endpoints (add.php / toggle.php) all
 * answer in the same shape and then stop.
 */
function send_json(array $data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}
