(() => {
  function find(el, sel) { return el ? el.querySelector(sel) : null; }
  function show(el) { if (el) el.style.display = ''; }
  function hide(el) { if (el) el.style.display = 'none'; }
  function text(el, t) { if (el) el.textContent = t; }

  function handleSubmit(e) {
    const form = e.currentTarget;
    const email = find(form, 'input[name="email"]');
    const btn = find(form, '.sib-default-btn');
    const loader = find(form, '.sib_loader');
    const msg = find(form, '.sib_msg_disp');
    const listId = form.dataset.listId || find(form, 'input[name="list_id"]')?.value || '';
    const nonce = (window.pianologEmailSignup && window.pianologEmailSignup.nonce) || form.dataset.nonce || find(form, 'input[name="nonce"]')?.value || '';
    const action = form.dataset.action || find(form, 'input[name="action"]')?.value || 'pianolog_subscribe';
    const doi = form.dataset.doi || find(form, 'input[name="doi"]')?.value || '0';
    const templateId = form.dataset.templateId || find(form, 'input[name="template_id"]')?.value || '';
    const redirectUrl = form.dataset.redirectUrl || find(form, 'input[name="redirect_url"]')?.value || '';

    e.preventDefault();
    if (!email || !email.value) return;

    hide(msg);
    text(msg, '');
    btn && (btn.disabled = true);
    show(loader);

    const body = new URLSearchParams();
    body.set('action', action);
    body.set('email', email.value);
    body.set('list_id', listId);
    body.set('nonce', nonce);
    body.set('doi', doi);
    body.set('template_id', templateId);
    body.set('redirect_url', redirectUrl);

    const ajaxUrl = (window.pianologEmailSignup && window.pianologEmailSignup.ajaxUrl) || form.action;
    fetch(ajaxUrl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
      body: body.toString()
    })
      .then(async (res) => {
        let payload = null;
        try { payload = await res.json(); } catch (e) { /* fall back to text */ }
        if (res.ok && payload && payload.success) {
          return payload;
        }
        const fallback = payload && (payload.data && payload.data.message ? payload.data.message : (payload.message || ''));
        const msgText = fallback || `Request failed (${res.status})`;
        throw new Error(msgText);
      })
      .then((data) => {
        text(msg, (data && data.data && data.data.message) ? data.data.message : 'Thank you!');
        show(msg);
        form.reset();
      })
      .catch((err) => {
        console.error('Signup error:', err);
        text(msg, err && err.message ? err.message : 'Network error. Please try again.');
        show(msg);
      })
      .finally(() => {
        hide(loader);
        btn && (btn.disabled = false);
      });
  }

  function init() {
    document.querySelectorAll('form.sib_signup_form').forEach(form => {
      form.addEventListener('submit', handleSubmit);
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();


