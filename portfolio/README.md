# Mansour Alanazi — Personal Portfolio

A simple personal front-end page built for my summer training task
(**"design a front-end that represents you"**).

I'm a 22-year-old computer engineering student who writes software for fun,
so I built the page the same way — from scratch, no frameworks, no build step.

## What's inside

| File | What it does |
|------|--------------|
| `index.html` | The page structure and all the content |
| `style.css` | Styling, dark/light themes, responsive layout |
| `script.js` | Theme toggle, terminal typing effect, scroll reveals |

## Features

- **Dark & light theme** — remembered in `localStorage`, follows your system setting by default
- **Responsive** — works from a phone screen up to a wide desktop
- **Animated terminal** — a small typing effect in the hero section
- **Scroll reveal** — sections fade in as you reach them (`IntersectionObserver`)
- **Accessible** — semantic HTML, and all animation is disabled for `prefers-reduced-motion`

## Built with

Plain **HTML**, **CSS** and **JavaScript**. No libraries.

## Running it

Just open `index.html` in a browser — that's it.

Or serve it locally:

```bash
python3 -m http.server 8000
```

Then visit `http://localhost:8000`.

## Live version

Published with GitHub Pages from the `main` branch:

**https://mansouraz0.github.io/training_tasks/portfolio/**

---

Built by Mansour Alanazi.
