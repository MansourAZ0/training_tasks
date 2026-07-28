# Task 2 — Web page connected to a MySQL database

A small web page where you type a **name** and an **age**, press **Submit**, and the
record is saved into a MySQL database and appears in the table below the form.
Each row has a **Toggle** button that flips that person's `status` between `0` and `1` —
the database is updated and the number changes on screen **without reloading the page**.

Built with **HTML, CSS, JavaScript, PHP and MySQL**. No frameworks.

## Live version

Hosted on InfinityFree:

**https://mansour-tasks.ifree.page/**

---

## How it works

The page is split into a front end (what you see) and a back end (what talks to MySQL):

```
Browser                          Server (PHP)                 MySQL
───────                          ────────────                 ─────
index.php  ──── first load ────► reads all rows ────────────► SELECT * FROM people
                                 prints the table

form submit ─── fetch() ───────► add.php                ────► INSERT INTO people
            ◄── JSON: new row ──┘
JS draws the new row

Toggle click ── fetch() ───────► toggle.php             ────► UPDATE people
             ◄── JSON: status ──┘                             SET status = 1 - status
JS updates just that cell
```

Because the two buttons use `fetch()` instead of a normal form post, the browser
never reloads — which is exactly what requirement 6 asks for.

## The files

| File | What it does |
|------|--------------|
| `index.php` | The page. Reads every row from MySQL and prints the form + table. |
| `db.example.php` | Template for the connection settings. Copy it to `db.php` and fill it in. |
| `db.php` | The real connection (PDO). **Not in git** — it holds the password. |
| `add.php` | Receives the form, validates it, runs the `INSERT`, replies with JSON. |
| `toggle.php` | Receives an id, flips that row's status, replies with the new value. |
| `script.js` | Sends both requests with `fetch()` and updates the page in place. |
| `style.css` | All the styling. |
| `schema.sql` | The `CREATE TABLE` statement for the `people` table. |

## The database table

```sql
CREATE TABLE people (
    id     INT AUTO_INCREMENT PRIMARY KEY,
    name   VARCHAR(100) NOT NULL,
    age    INT          NOT NULL,
    status TINYINT(1)   NOT NULL DEFAULT 0
);
```

---

## Steps I followed

### 1. Built the interface (HTML + CSS)
`index.php` holds a one-line form with two inputs (name, age) and a Submit button,
and an empty table underneath with the columns **ID, Name, Age, Status, Action**.
`style.css` styles it — same dark look as my portfolio page, and it stacks
vertically on a phone.

### 2. Created the database
In phpMyAdmin I created a database and ran the `CREATE TABLE` from `schema.sql`.
`status` is `TINYINT(1)` because it only ever holds `0` or `1`.

### 3. Connected PHP to MySQL
`db.php` opens a **PDO** connection. PDO is set to throw exceptions on errors and
to use real prepared statements.

### 4. Saving the form (`add.php`)
The values are validated **on the server** — name not empty and under 100 characters,
age a whole number between 1 and 120. The HTML `required` attribute alone isn't enough,
because anyone can bypass it. Then:

```php
$stmt = $pdo->prepare('INSERT INTO people (name, age, status) VALUES (?, ?, 0)');
$stmt->execute([$name, $age]);
```

The `?` placeholders are what prevent **SQL injection** — the values travel to MySQL
separately from the query, so they can never be executed as SQL.

### 5. Displaying the data
On page load `index.php` runs `SELECT id, name, age, status FROM people ORDER BY id`
and prints a row for each record. Every value printed goes through `htmlspecialchars()`
so a name containing HTML shows as text instead of running as code (**XSS** protection).

### 6. The Toggle button (`toggle.php`)
The flip happens inside MySQL in a single statement:

```php
$stmt = $pdo->prepare('UPDATE people SET status = 1 - status WHERE id = ?');
```

`1 - 0` is `1` and `1 - 1` is `0`. Doing it in one query — instead of reading the value,
flipping it in PHP, and writing it back — means two fast clicks can't overwrite
each other. The new value is then read back and returned, so the page always shows
what the database actually holds.

### 7. Updating without a reload
`script.js` intercepts the form's `submit` event, calls `event.preventDefault()` to stop
the normal reload, and sends the data with `fetch()`. The reply is JSON, and JavaScript
builds the new `<tr>` from it.

The Toggle buttons use **one** listener on the `<tbody>` instead of one per button.
That way rows added after the page loaded get the behaviour too, for free.

---

## Publishing on InfinityFree

1. Sign up at [infinityfree.com](https://infinityfree.com) and create a hosting account.
2. In the Client Area open **MySQL Databases** and create one. Write down the four
   values it gives you: **hostname**, **database name**, **username**, **password**.
3. Open **phpMyAdmin** for that database, go to the **SQL** tab, paste the contents of
   `schema.sql`, and run it.
4. Copy `db.example.php` to `db.php` and put those four values into it:
   ```bash
   cp db.example.php db.php
   ```
5. Upload `index.php`, `db.php`, `add.php`, `toggle.php`, `script.js` and `style.css`
   into the **`htdocs`** folder — either with the online File Manager or over FTP
   (FileZilla). Delete the default `index2.html` InfinityFree puts there.
   (`db.example.php` doesn't need to be uploaded.)
6. Open your site. New accounts can take a few minutes before the domain works.

> **Why the two files?** `db.php` is the only file holding a password, so it's listed
> in `.gitignore` and never reaches GitHub. `db.example.php` is the placeholder version
> that *is* committed, so anyone reading the repo can see what settings are needed
> without seeing my credentials. This is the standard way to handle secrets in a
> public repository.

## Running it on your own computer

With PHP installed and a local MySQL (XAMPP, MAMP or Homebrew):

```bash
php -S localhost:8000
```

Then open `http://localhost:8000` — after setting the local values in `db.php`.

---

## شرح مختصر بالعربي

صفحة بسيطة فيها فورم للاسم والعمر، وأي بيانات تُدخل تُحفظ في قاعدة بيانات MySQL
وتظهر مباشرة في جدول تحت الفورم.

- **`index.php`** — الصفحة نفسها: تقرأ كل الصفوف من قاعدة البيانات وتعرض الفورم والجدول.
- **`db.php`** — الاتصال بقاعدة البيانات باستخدام PDO.
- **`add.php`** — يتحقق من البيانات ثم يحفظها بأمر `INSERT`.
- **`toggle.php`** — يغيّر قيمة `status` بين `0` و `1` بأمر `UPDATE`.
- **`script.js`** — يرسل الطلبات بـ `fetch()` ويحدّث الجدول **بدون تحديث الصفحة**.

استُخدمت **Prepared Statements** للحماية من SQL Injection، و **`htmlspecialchars()`**
للحماية من XSS، مع التحقق من المدخلات في السيرفر وليس في المتصفح فقط.

---

Built by Mansour Alanazi.
