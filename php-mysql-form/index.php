<?php
/**
 * index.php — the page itself.
 *
 * It does two things:
 *   1. reads every row from the `people` table
 *   2. prints the form and the table
 *
 * Adding a person and toggling a status are handled by add.php and
 * toggle.php, which the JavaScript calls in the background so the
 * page never reloads.
 */
require __DIR__ . '/db.php';

$people = $pdo->query('SELECT id, name, age, status FROM people ORDER BY id')->fetchAll();

/** Escape text before printing it, so a name like <script> can't run. */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>People — PHP &amp; MySQL Task</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <main class="wrap">

    <header class="page-head">
      <p class="kicker">// task 02</p>
      <h1>People <span class="accent">directory</span></h1>
      <p class="lead">
        A small page that saves names into a MySQL database and lets you flip
        each person's status without reloading.
      </p>
    </header>

    <!-- ---------- the form ---------- -->
    <form id="add-form" class="card form" autocomplete="off">
      <div class="field">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" maxlength="100" placeholder="Mansour" required>
      </div>

      <div class="field field-age">
        <label for="age">Age</label>
        <input type="number" id="age" name="age" min="1" max="120" placeholder="22" required>
      </div>

      <button type="submit" class="btn btn-primary">Submit</button>
    </form>

    <p id="message" class="message" role="status" aria-live="polite"></p>

    <!-- ---------- the table ---------- -->
    <div class="card table-card">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Age</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody id="people-body">
          <?php if (!$people): ?>
            <tr class="empty-row">
              <td colspan="5">No one here yet — add the first person above.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($people as $person): ?>
              <tr data-id="<?= (int) $person['id'] ?>">
                <td class="col-id"><?= (int) $person['id'] ?></td>
                <td><?= e($person['name']) ?></td>
                <td><?= (int) $person['age'] ?></td>
                <td>
                  <span class="status status-<?= (int) $person['status'] ?>">
                    <?= (int) $person['status'] ?>
                  </span>
                </td>
                <td>
                  <button type="button" class="btn btn-ghost btn-toggle">Toggle</button>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <footer>
      <p>Built by Mansour Alanazi — HTML, CSS, JavaScript, PHP &amp; MySQL</p>
    </footer>

  </main>

  <script src="script.js"></script>
</body>
</html>
