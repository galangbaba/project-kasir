/* KasirKu — tanpa inline script agar lolos CSP (script-src 'self') */
(function () {
  'use strict';
  var $ = function (s, c) { return (c || document).querySelector(s); };
  var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };

  // sidebar (HP)
  $$('[data-toggle-nav]').forEach(function (el) {
    el.addEventListener('click', function () { document.body.classList.toggle('nav-open'); });
  });

  // grafik batang
  $$('[data-h]').forEach(function (el) { el.style.height = el.getAttribute('data-h') + '%'; });

  // konfirmasi aksi
  $$('form[data-confirm]').forEach(function (f) {
    f.addEventListener('submit', function (e) {
      if (!window.confirm(f.getAttribute('data-confirm'))) e.preventDefault();
    });
  });

  // cetak struk
  $$('[data-print]').forEach(function (el) { el.addEventListener('click', function () { window.print(); }); });

  // cegah klik ganda pada form
  $$('form[data-once]').forEach(function (f) {
    f.addEventListener('submit', function () {
      $$('button[type=submit]', f).forEach(function (b) { b.disabled = true; });
    });
  });

  // ---------- Kasir: transaksi baru ----------
  var pos = $('#pos');
  if (!pos) return;

  var rows = $$('.prod', pos), qty = {};
  var rp = function (n) { return 'Rp' + Math.round(n).toLocaleString('id-ID'); };
  var stepA = $('#stepProducts'), stepB = $('#stepPay'), title = $('#posTitle');
  var btnBayar = $('#btnBayar'), btnDone = $('#btnDone'), back = $('#btnBack'), sub = $('#posSub');

  function render() {
    var total = 0, count = 0, lines = '';
    rows.forEach(function (r) {
      var id = r.getAttribute('data-id'), q = qty[id] || 0;
      var harga = +r.getAttribute('data-harga'), stok = +r.getAttribute('data-stok');
      $('.q', r).textContent = q;
      $('[data-act=dec]', r).disabled = q <= 0;
      $('[data-act=inc]', r).disabled = q >= stok;
      total += q * harga; count += q;
      if (q > 0) {
        lines += '<div class="r"><span></span><span>' + rp(q * harga) + '</span></div>';
      }
    });
    $$('[data-total]').forEach(function (e) { e.textContent = rp(total); });
    $$('[data-count]').forEach(function (e) { e.textContent = count; });
    btnBayar.disabled = count === 0;

    // ringkasan (dibuat dengan DOM, bukan innerHTML, agar nama produk tidak bisa menyisipkan HTML)
    var box = $('#sumLines'); box.textContent = '';
    rows.forEach(function (r) {
      var id = r.getAttribute('data-id'), q = qty[id] || 0;
      if (!q) return;
      var d = document.createElement('div'); d.className = 'r';
      var a = document.createElement('span'); a.textContent = r.getAttribute('data-nama') + ' x' + q;
      var b = document.createElement('span'); b.textContent = rp(q * +r.getAttribute('data-harga'));
      d.appendChild(a); d.appendChild(b); box.appendChild(d);
    });
  }

  pos.addEventListener('click', function (e) {
    var b = e.target.closest('[data-act]'); if (!b) return;
    var r = b.closest('.prod'); if (!r) return;
    var id = r.getAttribute('data-id'), stok = +r.getAttribute('data-stok');
    var q = qty[id] || 0;
    if (b.getAttribute('data-act') === 'inc' && q < stok) q++;
    if (b.getAttribute('data-act') === 'dec' && q > 0) q--;
    qty[id] = q; render();
  });

  function step(n) {
    var pay = n === 2;
    stepA.hidden = pay; stepB.hidden = !pay;
    btnBayar.hidden = pay; btnDone.hidden = !pay;
    back.hidden = !pay; sub.hidden = pay;
    title.textContent = pay ? 'Pembayaran' : 'Transaksi Baru';
    window.scrollTo(0, 0);
  }
  btnBayar.addEventListener('click', function () { step(2); });
  back.addEventListener('click', function () { step(1); });

  pos.addEventListener('submit', function () {
    $$('input[data-gen]', pos).forEach(function (i) { i.remove(); });
    rows.forEach(function (r) {
      var id = r.getAttribute('data-id'), q = qty[id] || 0;
      if (q > 0) {
        var i = document.createElement('input');
        i.type = 'hidden'; i.name = 'items[' + id + ']'; i.value = q; i.setAttribute('data-gen', '1');
        pos.appendChild(i);
      }
    });
    btnDone.disabled = true;
  });

  render();
})();
