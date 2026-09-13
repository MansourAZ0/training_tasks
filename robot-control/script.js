const statusEl = document.querySelector('#status');
const connectionEl = document.querySelector('#connection');
const buttons = document.querySelectorAll('[data-command]');

const labels = { forward: 'Forward', backward: 'Backward', left: 'Left', right: 'Right', stop: 'Stop' };

function setConnection(state, text) {
  connectionEl.dataset.state = state;
  connectionEl.textContent = text;
}

function showStored(command, updatedAt) {
  const label = labels[command] || `Unknown (${command})`;
  statusEl.textContent = `Stored command: ${label}${updatedAt ? ` · ${updatedAt}` : ''}`;
}

async function readState() {
  const response = await fetch('get_state.php', { headers: { Accept: 'application/json' } });
  const data = await response.json();
  if (!response.ok || data.status === 'error') throw new Error(data.message || 'Could not read robot state');
  showStored(data.command, data.updated_at);
  setConnection('ready', 'Connected');
}

async function sendCommand(command, button) {
  buttons.forEach((item) => { item.disabled = true; });
  statusEl.textContent = `Sending ${labels[command].toLowerCase()}…`;
  try {
    const body = new URLSearchParams({ command });
    const response = await fetch('update_command.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded', Accept: 'application/json' },
      body
    });
    const data = await response.json();
    if (!response.ok || data.status !== 'success') throw new Error(data.message || 'The server rejected the command');
    showStored(data.command, data.updated_at);
    setConnection('ready', 'Connected');
    button?.focus();
  } catch (error) {
    setConnection('error', 'Connection error');
    statusEl.textContent = error.message;
  } finally {
    buttons.forEach((item) => { item.disabled = false; });
  }
}

buttons.forEach((button) => button.addEventListener('click', () => sendCommand(button.dataset.command, button)));
readState().catch((error) => { setConnection('error', 'Unavailable'); statusEl.textContent = error.message; });
