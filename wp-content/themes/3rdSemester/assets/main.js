document.addEventListener('DOMContentLoaded', () => {
  const btn = document.querySelector('.js-search-toggle');
  const panel = document.querySelector('.search-panel');
  if (!btn || !panel) return;
  btn.addEventListener('click', () => {
    panel.style.display = (panel.style.display === 'none' || !panel.style.display) ? 'block' : 'none';
  });
});
