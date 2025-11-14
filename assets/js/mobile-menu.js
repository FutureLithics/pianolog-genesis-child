(function () {
  var doc = document;
  var body = doc.body;
  var toggle = doc.querySelector('.header-menu-toggle');
  var drawer = doc.getElementById('mobile-menu');
  var overlay = doc.querySelector('[data-mobile-menu-overlay]');
  var closeBtn = doc.querySelector('.mobile-menu__close');

  if (!toggle || !drawer) return;

  function openMenu() {
    drawer.classList.add('is-open');
    drawer.setAttribute('aria-hidden', 'false');
    toggle.setAttribute('aria-expanded', 'true');
    if (overlay) {
      overlay.hidden = false;
      overlay.classList.add('is-visible');
    }
    body.classList.add('mobile-menu-open');
  }

  function closeMenu() {
    drawer.classList.remove('is-open');
    drawer.setAttribute('aria-hidden', 'true');
    toggle.setAttribute('aria-expanded', 'false');
    if (overlay) {
      overlay.classList.remove('is-visible');
      overlay.hidden = true;
    }
    body.classList.remove('mobile-menu-open');
  }

  toggle.addEventListener('click', function () {
    if (drawer.classList.contains('is-open')) {
      closeMenu();
    } else {
      openMenu();
    }
  });

  if (overlay) {
    overlay.addEventListener('click', closeMenu);
  }
  if (closeBtn) {
    closeBtn.addEventListener('click', closeMenu);
  }

  // Expand/collapse immediate children on items with children.
  var nav = drawer.querySelector('.mobile-menu__nav');
  if (nav) {
    var items = nav.querySelectorAll('li.menu-item-has-children');
    items.forEach(function (li) {
      // Respect pre-rendered expand buttons from the walker; only add if missing.
      var btn = li.querySelector(':scope > .mobile-menu__expand');
      if (!btn) {
        btn = doc.createElement('button');
        btn.type = 'button';
        btn.className = 'mobile-menu__expand';
        btn.setAttribute('aria-expanded', 'false');
        btn.innerHTML = '<svg aria-hidden=\"true\" viewBox=\"0 0 24 24\" width=\"20\" height=\"20\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\"><polyline points=\"6 9 12 15 18 9\"></polyline></svg>';
        var link = li.querySelector(':scope > a');
        if (link) {
          link.after(btn);
        } else {
          li.prepend(btn);
        }
      }

      var childUl = li.querySelector(':scope > ul');
      if (childUl) {
        // Prepare smooth collapse/expand
        childUl.style.overflow = 'hidden';
        childUl.style.maxHeight = '0px';
        childUl.style.transition = 'max-height .3s ease';
        childUl.setAttribute('aria-hidden', 'true');
        childUl.hidden = true; // start closed
        btn.addEventListener('click', function () {
          var isOpen = btn.getAttribute('aria-expanded') === 'true';
          btn.setAttribute('aria-expanded', String(!isOpen));
          li.classList.toggle('is-open', !isOpen);
          if (!isOpen) {
            // Opening: remove hidden so it can animate
            childUl.hidden = false;
            childUl.setAttribute('aria-hidden', 'false');
            // set to natural height for transition
            childUl.style.maxHeight = childUl.scrollHeight + 'px';
            childUl.addEventListener('transitionend', function openDone() {
              childUl.style.maxHeight = 'none'; // allow dynamic height after expand
              childUl.removeEventListener('transitionend', openDone);
            }, { once: true });
          } else {
            // Closing: from auto -> fixed height -> 0 for smooth collapse
            var current = childUl.scrollHeight;
            childUl.style.maxHeight = current + 'px';
            requestAnimationFrame(function () {
              childUl.style.maxHeight = '0px';
            });
            childUl.addEventListener('transitionend', function closeDone() {
              childUl.setAttribute('aria-hidden', 'true');
              childUl.hidden = true;
              childUl.removeEventListener('transitionend', closeDone);
            }, { once: true });
          }
        });
      } else {
        btn.disabled = true;
        btn.style.display = 'none';
      }
    });
  }
})(); 


