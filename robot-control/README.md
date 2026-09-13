# Robot control pad

This is the week 5 web-track task from the training folder. The five-button pad maps browser commands to one character in a single MySQL row:

| Button | Stored value |
| --- | --- |
| Forward | `f` |
| Backward | `b` |
| Left | `l` |
| Right | `r` |
| Stop | `S` |

The page loads the current state from `get_state.php`, sends commands with `fetch()` to `update_command.php`, and updates the status without a reload. PHP uses prepared statements, validates the command against an allow-list, and returns generic errors so database details are not exposed.

## Deploy to InfinityFree

1. Create a MySQL database and run [`setup.sql`](setup.sql) in phpMyAdmin.
2. Copy [`db.example.php`](db.example.php) to `db.php` on the server and fill in the credentials. Keep `db.php` out of Git.
3. Upload `index.html`, `style.css`, `script.js`, `get_state.php`, `update_command.php`, and `db.php` to the same `htdocs` directory.
4. Open the site and confirm that each button changes the stored command. `SELECT * FROM robot_state;` should show one row with the latest value.

The PHP endpoints require PHP's `mysqli` extension. No credentials are included in this repository.
