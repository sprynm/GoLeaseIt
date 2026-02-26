(function () {
  "use strict";

  var INLINE_INCLUDES = {
    "partials/preview-header.html": `<header class="site-header primary-hdr">
  <div class="c-container c-container--full">
    <div class="c-header">
      <a href="/" class="logo">
        <img src="../img/logo.svg" width="285" height="57" alt="Go Lease It">
      </a>
      <nav class="site-nav" role="navigation" aria-label="Main navigation" data-site-nav>
        <ul class="menu_level_">
          <li class="first current menu_1"><a href="/">Home</a></li>
          <li class="menu_2"><a href="/finance-solutions">Financing Solutions</a></li>
          <li class="menu_3"><a href="/industries-served">Industries We Serve</a></li>
          <li class="menu_4"><a href="/how-it-works">How It Works</a></li>
          <li class="menu_5"><a href="/about">About</a></li>
          <li class="last menu_6"><a href="/contact">Contact</a></li>
        </ul>
      </nav>
      <div class="header-cta">
        <a class="util-phone" href="tel:18007076165">
          <svg viewBox="0 0 20 20" aria-hidden="true">
            <path d="M2.003 5.884L2 5a2 2 0 012-2h.055A2 2 0 015.96 4.518l.42 1.68a2 2 0 01-.46 1.93l-.516.516a11.064 11.064 0 005.95 5.95l.516-.516a2 2 0 012.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0117 15.055V15a2 2 0 01-2 2h-.084a17 17 0 01-14.916-14.913z"/>
          </svg>
          1-(800)-707-6165
        </a>
        <a class="btn btn--primary btn-sm" href="/contact">APPLY ONLINE</a>
      </div>
      <div class="hdr-links">
        <button
          id="site-nav-toggle"
          class="site-nav__toggle"
          type="button"
          aria-controls="site-nav-drawer"
          aria-expanded="false"
          aria-haspopup="dialog"
          aria-label="Open site menu"
        >Menu</button>
      </div>
    </div>
  </div>
</header>

<dialog id="site-nav-drawer" class="site-nav-drawer" aria-label="Mobile navigation">
  <div class="site-nav-drawer__head">
    <strong>Menu</strong>
    <button class="site-nav-drawer__close" type="button" aria-label="Close menu">x</button>
  </div>
  <div class="site-nav-drawer__body" data-nav-drawer-body></div>
</dialog>`,
    "partials/preview-footer.html": `<footer>
  <div class="c-container c-container--full">
    <div class="ftr-container">
      <div class="ftr-top">
        <nav class="ftr-nav" aria-label="Footer navigation">
          <ul class="menu_level_">
            <li class="first current menu_1"><a href="/">Home</a></li>
            <li class="menu_2"><a href="/finance-solutions">Financing Solutions</a></li>
            <li class="menu_3"><a href="/industries-served">Industries We Serve</a></li>
            <li class="menu_4"><a href="/how-it-works">How It Works</a></li>
            <li class="menu_5"><a href="/about">About</a></li>
            <li class="last menu_6"><a href="/contact">Contact</a></li>
          </ul>
        </nav>
        <div class="ftr-utility">
          <a class="ftr-phone" href="tel:18007076165">
            <svg viewBox="0 0 20 20" aria-hidden="true">
              <path d="M2.003 5.884L2 5a2 2 0 012-2h.055A2 2 0 015.96 4.518l.42 1.68a2 2 0 01-.46 1.93l-.516.516a11.064 11.064 0 005.95 5.95l.516-.516a2 2 0 012.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0117 15.055V15a2 2 0 01-2 2h-.084a17 17 0 01-14.916-14.913z"/>
            </svg>
            1-(800)-707-6165
          </a>
          <div class="ftr-social">
            <div class="social-media">
              <a class="social-icon social-icon--facebook" href="https://www.facebook.com/GoLeaseIt/" target="_blank" rel="noopener" aria-label="Facebook">
                <svg viewBox="0 0 40 40" class="social-icon__svg" aria-hidden="true" focusable="false">
                  <path d="M37 0H3C1.4 0 0 1.2 0 2.8V37c0 1.7 1.3 3 2.9 3h15V24.5h-5.2v-6H18v-4.4c0-5.2 3.1-8 7.7-8 1.5 0 3.1.1 4.7.3v5.4h-3.2c-2.5 0-3 1.2-3 3v3.8h5.9l-.8 6h-5.2V40H37c1.6 0 3-1.2 3-2.9V2.9C40 1.3 38.7 0 37 0z"/>
                </svg>
                <span class="u-visually-hidden">Facebook</span>
              </a>
            </div>
          </div>
        </div>
      </div>
      <div class="copyright">
        © 2026 Go Lease It. | A Commercial Equipment Financing website by <a href="/" rel="nofollow">Radar Hill Web Design</a> The content of this website is the responsibility of the website owner.
      </div>
    </div>
  </div>
</footer>`
  };

  function isFileProtocol() {
    return window.location.protocol === "file:";
  }

  function normalizeIncludePath(includePath) {
    return String(includePath || "")
      .replace(/^[.][\\/]/, "")
      .replace(/\\/g, "/");
  }

  function loadScript(src) {
    return new Promise(function (resolve, reject) {
      var script = document.createElement("script");
      script.src = src;
      script.defer = true;
      script.onload = resolve;
      script.onerror = reject;
      document.body.appendChild(script);
    });
  }

  function injectHtml(target, html) {
    var wrapper = document.createElement("div");
    wrapper.innerHTML = html;
    while (wrapper.firstChild) {
      target.parentNode.insertBefore(wrapper.firstChild, target);
    }
    target.parentNode.removeChild(target);
  }

  function getInlineInclude(includePath) {
    var normalized = normalizeIncludePath(includePath);
    return INLINE_INCLUDES[normalized] || "";
  }

  function applyPreviewCurrentNavState() {
    var requestedCurrent = document.body.getAttribute("data-preview-nav-current");
    if (!requestedCurrent) {
      return;
    }

    var safeCurrent = requestedCurrent.replace(/[^a-zA-Z0-9_-]/g, "");
    if (!safeCurrent) {
      return;
    }

    var navLists = document.querySelectorAll(
      ".site-nav .menu_level_, .ftr-nav .menu_level_"
    );

    for (var i = 0; i < navLists.length; i += 1) {
      var navList = navLists[i];
      var activeItems = navList.querySelectorAll("li.current");
      for (var j = 0; j < activeItems.length; j += 1) {
        activeItems[j].classList.remove("current");
      }

      var target = navList.querySelector("li." + safeCurrent);
      if (target) {
        target.classList.add("current");
      }
    }
  }

  async function fetchInclude(includePath) {
    var response = await fetch(includePath, { cache: "no-cache" });
    if (!response.ok) {
      throw new Error("Failed include: " + includePath + " (" + response.status + ")");
    }
    return response.text();
  }

  async function includeFragments() {
    var includeNodes = Array.prototype.slice.call(
      document.querySelectorAll("[data-include]")
    );

    for (var i = 0; i < includeNodes.length; i += 1) {
      var node = includeNodes[i];
      var includePath = node.getAttribute("data-include");
      if (!includePath) {
        continue;
      }

      try {
        var html = "";

        if (isFileProtocol()) {
          html = getInlineInclude(includePath);
        } else {
          html = await fetchInclude(includePath);
        }

        if (!html) {
          throw new Error(
            "Include not found for path " + includePath + ". Expected fallback in INLINE_INCLUDES map."
          );
        }

        injectHtml(node, html);
      } catch (error) {
        var fallbackHtml = getInlineInclude(includePath);
        if (fallbackHtml) {
          injectHtml(node, fallbackHtml);
          continue;
        }
        console.error(error);
      }
    }
  }

  async function run() {
    await includeFragments();
    applyPreviewCurrentNavState();

    if (document.body.hasAttribute("data-load-nav-js")) {
      try {
        await loadScript("../js/navigation-modern.js");
      } catch (error) {
        console.error(error);
      }
    }
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", run);
  } else {
    run();
  }
})();
