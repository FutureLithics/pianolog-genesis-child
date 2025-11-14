(function () {
  function on(selector, event, handler, root) {
    (root || document).addEventListener(event, function (e) {
      const el = e.target.closest(selector);
      if (el && (root || document).contains(el)) handler(e, el);
    });
  }

  on('.posts-gear__loadmore', 'click', async function (e, btn) {
    e.preventDefault();
    if (!window.pianologPostsGrid) return;
    if (btn.getAttribute('aria-busy') === 'true') return;
    const section = btn.closest('section.posts-gear');
    const grid = section ? section.querySelector('.gear-grid') : null;
    if (!grid) return;
    const category = btn.dataset.category || '';
    const perPage = parseInt(btn.dataset.perPage || '9', 10);
    const nextPage = parseInt(btn.dataset.nextPage || '2', 10);

    btn.setAttribute('aria-busy', 'true');
    btn.style.opacity = '0.7';

    try {
      const form = new FormData();
      form.append('action', 'pianolog_load_more_posts');
      form.append('nonce', pianologPostsGrid.nonce);
      form.append('category', category);
      form.append('page', String(nextPage));
      form.append('per_page', String(perPage));

      const res = await fetch(pianologPostsGrid.ajax_url, { method: 'POST', body: form });
      const data = await res.json();
      if (!data || !data.success) throw new Error('Failed to load posts');

      const tmp = document.createElement('div');
      tmp.innerHTML = data.data.html || '';
      while (tmp.firstChild) grid.appendChild(tmp.firstChild);

      // If fewer than per_page returned, hide the button
      if (!data.data || typeof data.data.count !== 'number' || data.data.count < perPage) {
        btn.parentElement && btn.parentElement.removeChild(btn);
      } else {
        btn.dataset.nextPage = String(nextPage + 1);
      }
    } catch (err) {
      console.error(err);
    } finally {
      btn.removeAttribute('aria-busy');
      btn.style.opacity = '';
    }
  });
})();


