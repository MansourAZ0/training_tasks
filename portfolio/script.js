/* ==========================================================
   Mansour Alanazi — portfolio scripts
   ========================================================== */

/* ---------- 1. theme toggle (remembers your choice) ---------- */
const root = document.documentElement;
const toggle = document.getElementById('theme-toggle');

const savedTheme = localStorage.getItem('theme');
if (savedTheme) {
  root.setAttribute('data-theme', savedTheme);
} else if (window.matchMedia('(prefers-color-scheme: light)').matches) {
  root.setAttribute('data-theme', 'light');
}

toggle.addEventListener('click', () => {
  const next = root.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
  root.setAttribute('data-theme', next);
  localStorage.setItem('theme', next);
});

/* ---------- 2. typing effect in the terminal ---------- */
const target = document.getElementById('typed');
const lines = [
  'building something new...',
  'learning something new...',
  'npm run dev',
  'git commit -m "it works now"'
];

let lineIndex = 0;
let charIndex = 0;
let deleting = false;

function type() {
  const current = lines[lineIndex];

  if (deleting) {
    charIndex--;
  } else {
    charIndex++;
  }

  target.textContent = current.slice(0, charIndex);

  let delay = deleting ? 40 : 85;

  if (!deleting && charIndex === current.length) {
    delay = 1800;          // pause on a finished line
    deleting = true;
  } else if (deleting && charIndex === 0) {
    deleting = false;
    lineIndex = (lineIndex + 1) % lines.length;
    delay = 400;
  }

  setTimeout(type, delay);
}

if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
  type();
} else {
  target.textContent = lines[0];
}

/* ---------- 3. reveal sections as they scroll into view ---------- */
root.classList.add('js');   // lets the CSS hide sections only when JS can reveal them

const observer = new IntersectionObserver((entries) => {
  entries.forEach((entry) => {
    if (entry.isIntersecting) {
      entry.target.classList.add('visible');
      observer.unobserve(entry.target);
    }
  });
}, { threshold: 0.12 });

document.querySelectorAll('.reveal').forEach((el) => observer.observe(el));

/* ---------- 4. footer year ---------- */
document.getElementById('year').textContent = new Date().getFullYear();
