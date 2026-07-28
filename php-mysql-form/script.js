/* ==========================================================
   script.js — talks to add.php and toggle.php in the background
   so the page never has to reload.
   ========================================================== */

const form    = document.getElementById('add-form');
const tbody   = document.getElementById('people-body');
const message = document.getElementById('message');

/* Show a short message under the form. */
function say(text, kind = 'ok') {
  message.textContent = text;
  message.className = 'message show message-' + kind;
  clearTimeout(say.timer);
  say.timer = setTimeout(() => { message.className = 'message'; }, 3500);
}

/* Text -> safe text node. Building rows this way (instead of innerHTML)
   means a name containing HTML can never run as code. */
function cell(text, className) {
  const td = document.createElement('td');
  td.textContent = text;
  if (className) td.className = className;
  return td;
}

/* Build one <tr> for a person object. */
function buildRow(person) {
  const tr = document.createElement('tr');
  tr.dataset.id = person.id;

  tr.append(
    cell(person.id, 'col-id'),
    cell(person.name),
    cell(person.age)
  );

  const statusCell = document.createElement('td');
  const badge = document.createElement('span');
  badge.className = 'status status-' + person.status;
  badge.textContent = person.status;
  statusCell.append(badge);
  tr.append(statusCell);

  const actionCell = document.createElement('td');
  const button = document.createElement('button');
  button.type = 'button';
  button.className = 'btn btn-ghost btn-toggle';
  button.textContent = 'Toggle';
  actionCell.append(button);
  tr.append(actionCell);

  return tr;
}

/* ---------- 1. submitting the form ---------- */
form.addEventListener('submit', async (event) => {
  event.preventDefault();                 // stop the normal page reload

  const submitButton = form.querySelector('button[type="submit"]');
  submitButton.disabled = true;

  try {
    const response = await fetch('add.php', {
      method: 'POST',
      body: new FormData(form)
    });
    const data = await response.json();

    if (!data.ok) {
      say(data.error || 'Could not save.', 'error');
      return;
    }

    // Drop the "No one here yet" row the first time something is added.
    tbody.querySelector('.empty-row')?.remove();

    const row = buildRow(data.person);
    row.classList.add('row-new');
    tbody.append(row);

    form.reset();
    document.getElementById('name').focus();
    say('Saved ' + data.person.name + '.');

  } catch (error) {
    say('Could not reach the server.', 'error');
  } finally {
    submitButton.disabled = false;
  }
});

/* ---------- 2. the Toggle buttons ----------
   One listener on the table body handles every button, including the
   rows added after the page loaded. */
tbody.addEventListener('click', async (event) => {
  const button = event.target.closest('.btn-toggle');
  if (!button) return;

  const row = button.closest('tr');
  const id  = row.dataset.id;

  button.disabled = true;

  try {
    const response = await fetch('toggle.php', {
      method: 'POST',
      body: new URLSearchParams({ id })
    });
    const data = await response.json();

    if (!data.ok) {
      say(data.error || 'Could not update.', 'error');
      return;
    }

    // Show the value the database now holds.
    const badge = row.querySelector('.status');
    badge.textContent = data.status;
    badge.className = 'status status-' + data.status;

  } catch (error) {
    say('Could not reach the server.', 'error');
  } finally {
    button.disabled = false;
  }
});
