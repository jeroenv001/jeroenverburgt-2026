/* Jeroen Verburgt 2026 – interactions (carousels, scroll reveal) */
(function () {
  'use strict';
  var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  function slide(track, dir, loop) {
    var cards = Array.prototype.slice.call(track.querySelectorAll('.quote'));
    if (!cards.length) return;
    var pad = parseFloat(window.getComputedStyle(track).paddingLeft) || 0;
    var cur = track.scrollLeft;
    var max = track.scrollWidth - track.clientWidth;
    var pos = cards.map(function (c) { return Math.min(Math.max(c.offsetLeft - pad, 0), max); });
    var target = null, i;
    if (dir > 0) {
      for (i = 0; i < pos.length; i++) { if (pos[i] > cur + 4) { target = pos[i]; break; } }
      if (target === null || cur >= max - 4) target = loop ? 0 : max;
    } else {
      for (i = pos.length - 1; i >= 0; i--) { if (pos[i] < cur - 4) { target = pos[i]; break; } }
      if (target === null) target = 0;
    }
    try { track.scrollTo({ left: target, behavior: reduce ? 'auto' : 'smooth' }); } catch (e) { track.scrollLeft = target; }
  }

  function init() {
    // Arrow buttons
    document.querySelectorAll('.ctrl[data-dir]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var sec = btn.closest('section');
        var track = sec ? sec.querySelector('.track') : null;
        if (track) slide(track, parseInt(btn.getAttribute('data-dir'), 10), false);
      });
    });

    // Pause autoplay on interaction
    var tracks = document.querySelectorAll('.track');
    tracks.forEach(function (t) {
      var on = function () { t.dataset.paused = '1'; };
      var off = function () { t.dataset.paused = ''; };
      t.addEventListener('mouseenter', on);
      t.addEventListener('mouseleave', off);
      t.addEventListener('focusin', on);
      t.addEventListener('focusout', off);
      t.addEventListener('touchstart', on, { passive: true });
    });

    if (reduce) return;

    // Autoplay carousels
    setInterval(function () {
      if (document.hidden) return;
      tracks.forEach(function (t) { if (t.dataset.paused !== '1') slide(t, 1, true); });
    }, 5000);

    // Scroll reveal
    if (!('IntersectionObserver' in window)) return;
    document.documentElement.classList.add('js-rv');
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (!en.isIntersecting) return;
        var el = en.target;
        if (el.classList.contains('track')) el.querySelectorAll('.quote').forEach(function (q) { q.classList.add('in'); });
        else el.classList.add('in');
        io.unobserve(el);
      });
    }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });
    document.querySelectorAll('h1, h2, .g3 > article, .g4 > div, .track, .tlwrap, .tl').forEach(function (el) {
      if (!el.closest('.hero') || el.tagName === 'H1') io.observe(el);
    });
    // Quote: words appear one by one, then the red words get an underline
    var qt = document.querySelector('section[aria-label="Quote"] blockquote');
    if (qt) {
      var walker = document.createTreeWalker(qt, NodeFilter.SHOW_TEXT, null);
      var nodes = [], n;
      while ((n = walker.nextNode())) nodes.push(n);
      var count = 0;
      nodes.forEach(function (tn) {
        var frag = document.createDocumentFragment();
        tn.nodeValue.split(/(\s+)/).forEach(function (part) {
          if (!part) return;
          if (/^\s+$/.test(part)) { frag.appendChild(document.createTextNode(part)); return; }
          var w = document.createElement('span');
          w.className = 'w';
          w.style.transitionDelay = (count * 0.06).toFixed(2) + 's';
          w.textContent = part;
          frag.appendChild(w);
          count++;
        });
        tn.parentNode.replaceChild(frag, tn);
      });
      qt.querySelectorAll('span[style*="FF0000"]').forEach(function (h) {
        h.classList.add('hl');
        h.style.transitionDelay = (count * 0.06 + 0.2).toFixed(2) + 's';
      });
      qt.classList.add('qt');
      var qio = new IntersectionObserver(function (entries) {
        entries.forEach(function (en) {
          if (en.isIntersecting) { qt.classList.add('in'); qio.disconnect(); }
        });
      }, { threshold: 0.4 });
      qio.observe(qt);
    }
    requestAnimationFrame(function () {
      requestAnimationFrame(function () {
        document.querySelectorAll('.hero .g4 > div').forEach(function (el) { el.classList.add('in'); });
      });
    });
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
  else init();
})();
