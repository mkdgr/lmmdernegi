/* LLM Derneği — arayüz etkileşimleri (bağımlılıksız) */
(function () {
  "use strict";

  var root = document.documentElement;
  function store(key, val) {
    try { if (val === undefined) return localStorage.getItem(key); localStorage.setItem(key, val); } catch (e) { return null; }
  }

  /* Erişilebilirlik: yazı boyutu ve kontrast */
  var fs = store("llm-fontsize"); if (fs) root.dataset.fontsize = fs;
  var hc = store("llm-contrast"); if (hc) root.dataset.contrast = hc;

  function syncA11y() {
    document.querySelectorAll("[data-fontsize-set]").forEach(function (b) {
      b.setAttribute("aria-pressed", String((root.dataset.fontsize || "md") === b.dataset.fontsizeSet));
    });
    document.querySelectorAll("[data-contrast-toggle]").forEach(function (b) {
      b.setAttribute("aria-pressed", String(root.dataset.contrast === "high"));
    });
  }
  document.addEventListener("click", function (e) {
    var f = e.target.closest("[data-fontsize-set]");
    if (f) { root.dataset.fontsize = f.dataset.fontsizeSet; store("llm-fontsize", f.dataset.fontsizeSet); syncA11y(); }
    var c = e.target.closest("[data-contrast-toggle]");
    if (c) {
      var on = root.dataset.contrast !== "high";
      if (on) root.dataset.contrast = "high"; else delete root.dataset.contrast;
      store("llm-contrast", on ? "high" : ""); syncA11y();
    }
  });
  syncA11y();

  /* Mobil menü */
  var nav = document.getElementById("site-nav");
  var backdrop = document.querySelector(".nav-backdrop");
  var toggle = document.querySelector(".nav-toggle[aria-controls='site-nav']");
  function setNav(open) {
    if (!nav) return;
    nav.classList.toggle("is-open", open);
    if (backdrop) backdrop.classList.toggle("is-open", open);
    if (toggle) toggle.setAttribute("aria-expanded", String(open));
    document.body.style.overflow = open ? "hidden" : "";
    if (open) { var first = nav.querySelector("a, button"); if (first) first.focus(); }
    else if (toggle) toggle.focus();
  }
  if (toggle) toggle.addEventListener("click", function () { setNav(!nav.classList.contains("is-open")); });
  document.querySelectorAll("[data-nav-close]").forEach(function (b) { b.addEventListener("click", function () { setNav(false); }); });
  document.addEventListener("keydown", function (e) { if (e.key === "Escape" && nav && nav.classList.contains("is-open")) setNav(false); });

  /* Alt menüler: dokunmatik ve klavye için aç/kapa */
  document.querySelectorAll(".nav__link[aria-controls]").forEach(function (btn) {
    btn.addEventListener("click", function () {
      var menu = document.getElementById(btn.getAttribute("aria-controls"));
      var open = btn.getAttribute("aria-expanded") !== "true";
      document.querySelectorAll(".nav__link[aria-expanded='true']").forEach(function (o) {
        if (o !== btn) { o.setAttribute("aria-expanded", "false"); document.getElementById(o.getAttribute("aria-controls")).classList.remove("is-open"); }
      });
      btn.setAttribute("aria-expanded", String(open));
      menu.classList.toggle("is-open", open);
    });
  });

  /* Bağış tutarı: hazır tutar ↔ serbest tutar ↔ özet */
  document.querySelectorAll("[data-donate]").forEach(function (form) {
    var custom = form.querySelector("[data-amount-custom]");
    var outs = document.querySelectorAll("[data-amount-out]");
    var freqOuts = document.querySelectorAll("[data-freq-out]");
    function fmt(n) { return document.documentElement.lang === "en" ? "₺" + new Intl.NumberFormat("en-GB").format(n) : new Intl.NumberFormat("tr-TR").format(n) + " ₺"; }
    function update() {
      var checked = form.querySelector("input[name='amount']:checked");
      var val = custom && custom.value ? parseInt(custom.value, 10) : (checked ? parseInt(checked.value, 10) : 0);
      outs.forEach(function (o) { o.textContent = val ? fmt(val) : "—"; });
      var freq = form.querySelector("input[name='frequency']:checked");
      if (freq) freqOuts.forEach(function (o) { o.textContent = freq.dataset.label || freq.value; });
    }
    form.addEventListener("change", function (e) {
      if (e.target.name === "amount" && custom) custom.value = "";
      update();
    });
    if (custom) custom.addEventListener("input", function () {
      custom.value = custom.value.replace(/\D/g, "");
      if (custom.value) form.querySelectorAll("input[name='amount']").forEach(function (r) { r.checked = false; });
      update();
    });
    update();
  });

  /* IBAN kopyala */
  document.addEventListener("click", function (e) {
    var b = e.target.closest("[data-copy]");
    if (!b) return;
    var text = b.dataset.copy;
    var done = function () { var t = b.textContent; b.textContent = b.dataset.copied || "Kopyalandı"; setTimeout(function () { b.textContent = t; }, 1600); };
    if (navigator.clipboard) navigator.clipboard.writeText(text).then(done, done); else done();
  });

  /* Etkinlik geri sayımı */
  document.querySelectorAll("[data-countdown]").forEach(function (el) {
    var target = new Date(el.dataset.countdown).getTime();
    function tick() {
      var d = Math.max(0, target - Date.now());
      var set = function (k, v) { var n = el.querySelector("[data-cd='" + k + "']"); if (n) n.textContent = v; };
      set("d", Math.floor(d / 864e5)); set("h", Math.floor(d / 36e5) % 24); set("m", Math.floor(d / 6e4) % 60);
    }
    tick(); setInterval(tick, 30000);
  });

  /* Haber listesi filtresi */
  document.querySelectorAll("[data-filter-group]").forEach(function (group) {
    var list = document.getElementById(group.dataset.filterGroup);
    group.addEventListener("click", function (e) {
      var chip = e.target.closest("[data-filter]");
      if (!chip) return;
      group.querySelectorAll("[data-filter]").forEach(function (c) { c.setAttribute("aria-pressed", String(c === chip)); });
      var f = chip.dataset.filter;
      list.querySelectorAll("[data-cat]").forEach(function (card) {
        card.classList.toggle("is-hidden", f !== "all" && card.dataset.cat !== f);
      });
    });
  });

  /* Prototip notu */
  document.querySelectorAll("[data-dismiss]").forEach(function (b) {
    b.addEventListener("click", function () { b.closest(b.dataset.dismiss).remove(); });
  });
})();
