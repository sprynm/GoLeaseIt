(function () {
  "use strict";

  if (window.RHLightbox && window.RHLightbox.version) {
    return;
  }

  var IMAGE_URL_PATTERN = /^(data:image\/|blob:)|\.(avif|bmp|gif|jpe?g|png|svg|tiff?|webp)(?:[?#].*)?$/i;
  var AUTO_SCOPE_ATTR = "data-lightbox-scope-id";
  var scopeIndex = 0;
  var RHUtils = window.RHLibrary && window.RHLibrary.utils ? window.RHLibrary.utils : null;

  var ui = null;
  var state = {
    isOpen: false,
    items: [],
    index: 0,
    lastFocus: null,
    loop: true,
    keyboard: true,
    closeOnBackdrop: true,
  };

  function asString(value) {
    if (RHUtils && typeof RHUtils.asString === "function") {
      return RHUtils.asString(value);
    }

    return value == null ? "" : String(value);
  }

  function stripQuotes(value) {
    if (RHUtils && typeof RHUtils.stripWrappingQuotes === "function") {
      return RHUtils.stripWrappingQuotes(value);
    }

    var trimmed = asString(value).trim();
    if (trimmed.length < 2) {
      return trimmed;
    }
    var first = trimmed.charAt(0);
    var last = trimmed.charAt(trimmed.length - 1);
    if ((first === '"' && last === '"') || (first === "'" && last === "'")) {
      return trimmed.slice(1, -1);
    }

    return trimmed;
  }

  function parseScalar(value) {
    if (RHUtils && typeof RHUtils.parseScalar === "function") {
      return RHUtils.parseScalar(value);
    }

    var normalized = stripQuotes(value);
    var lower = normalized.toLowerCase();

    if (lower === "true") {
      return true;
    }
    if (lower === "false") {
      return false;
    }
    if (/^-?\d+(?:\.\d+)?$/.test(normalized)) {
      return Number(normalized);
    }

    return normalized;
  }

  function parseDeclarativeConfig(raw) {
    var value = asString(raw).trim();
    var config = {};

    if (value === "") {
      return config;
    }

    if (value.charAt(0) === "{") {
      try {
        var parsed = JSON.parse(value);
        if (parsed && typeof parsed === "object" && !Array.isArray(parsed)) {
          return parsed;
        }
      } catch (error) {
        // Ignore malformed JSON and fall through to key/value parsing.
      }
    }

    if (value.toLowerCase() === "image") {
      config.type = "image";
      return config;
    }

    var segments = value.split(";");
    if (segments.length === 1 && value.indexOf(";") === -1 && value.indexOf(",") > -1) {
      segments = value.split(",");
    }

    segments.forEach(function (segment) {
      var token = segment.trim();
      if (!token) {
        return;
      }

      var match = token.match(/^([a-zA-Z0-9_-]+)\s*[:=]\s*(.+)$/);
      if (!match) {
        if (token.toLowerCase() === "image") {
          config.type = "image";
          return;
        }
        if (!config.group) {
          config.group = parseScalar(token);
        }
        return;
      }

      config[match[1]] = parseScalar(match[2]);
    });

    return config;
  }

  function isImageUrl(url) {
    var value = asString(url).trim();
    return value !== "" && IMAGE_URL_PATTERN.test(value);
  }

  function getHref(link) {
    if (!link) {
      return "";
    }

    var href = link.getAttribute("href");
    if (!href) {
      return "";
    }

    return link.href || href;
  }

  function getImageAlt(link) {
    if (!link) {
      return "";
    }

    var image = link.querySelector("img");
    return image ? asString(image.getAttribute("alt")).trim() : "";
  }

  function resolveAutoGroup(link) {
    if (!link) {
      return "";
    }

    var scope = link.closest("[data-lightbox-scope], .gallery, .c-gallery, .article-body");
    if (!scope) {
      return "";
    }

    var group = scope.getAttribute(AUTO_SCOPE_ATTR);
    if (!group) {
      scopeIndex += 1;
      group = "scope-" + scopeIndex;
      scope.setAttribute(AUTO_SCOPE_ATTR, group);
    }

    return group;
  }

  function isAutoEligible(link) {
    if (!link || link.hasAttribute("data-lightbox")) {
      return false;
    }

    if (!link.querySelector("img")) {
      return false;
    }

    return isImageUrl(getHref(link));
  }

  function buildItemFromLink(link) {
    if (!link || link.tagName !== "A") {
      return null;
    }

    var hasDeclarativeConfig = link.hasAttribute("data-lightbox");
    if (!hasDeclarativeConfig && !isAutoEligible(link)) {
      return null;
    }

    var config = hasDeclarativeConfig ? parseDeclarativeConfig(link.getAttribute("data-lightbox")) : {};

    if (config.enabled === false || config.disabled === true || config.mode === "off") {
      return null;
    }

    var type = asString(config.type || "image").toLowerCase();
    if (type !== "image") {
      return null;
    }

    var source = config.src || config.href || getHref(link);
    if (!isImageUrl(source)) {
      return null;
    }

    var caption = asString(config.caption || link.getAttribute("data-caption") || getImageAlt(link)).trim();
    var group = asString(config.group || link.getAttribute("data-lightbox-group") || resolveAutoGroup(link)).trim();

    return {
      link: link,
      source: source,
      caption: caption,
      group: group,
      loop: config.loop !== false,
      keyboard: config.keyboard !== false,
      closeOnBackdrop: config.closeOnBackdrop !== false,
    };
  }

  function collectItems(triggerItem) {
    if (!triggerItem.group) {
      return {
        items: [triggerItem],
        index: 0,
      };
    }

    var links = RHUtils && typeof RHUtils.qsa === "function" ? RHUtils.qsa("a[href]") : document.querySelectorAll("a[href]");
    var items = [];
    var activeIndex = -1;

    Array.prototype.forEach.call(links, function (link) {
      var item = buildItemFromLink(link);
      if (!item || item.group !== triggerItem.group) {
        return;
      }

      if (link === triggerItem.link) {
        activeIndex = items.length;
      }

      items.push(item);
    });

    if (activeIndex < 0) {
      activeIndex = 0;
    }

    return {
      items: items.length ? items : [triggerItem],
      index: activeIndex,
    };
  }

  function ensureUi() {
    if (ui) {
      return ui;
    }

    var root = document.createElement("div");
    root.className = "lightbox";
    root.hidden = true;
    root.setAttribute("aria-hidden", "true");

    root.innerHTML = [
      '<div class="lightbox__backdrop" data-lightbox-action="close"></div>',
      '<div class="lightbox__dialog" role="dialog" aria-modal="true" aria-label="Image viewer">',
      '  <button type="button" class="lightbox__button lightbox__button--close" data-lightbox-action="close" aria-label="Close image viewer">',
      '    <span aria-hidden="true">&times;</span>',
      "  </button>",
      "  <figure class=\"lightbox__figure\">",
      '    <button type="button" class="lightbox__button lightbox__button--prev" data-lightbox-action="prev" aria-label="Previous image">',
      '      <span aria-hidden="true">&#8249;</span>',
      "    </button>",
      '    <img class="lightbox__image" alt="" decoding="async" loading="eager">',
      '    <button type="button" class="lightbox__button lightbox__button--next" data-lightbox-action="next" aria-label="Next image">',
      '      <span aria-hidden="true">&#8250;</span>',
      "    </button>",
      '    <figcaption class="lightbox__caption" hidden></figcaption>',
      "  </figure>",
      '  <p class="lightbox__count" aria-live="polite"></p>',
      '  <p class="lightbox__status" role="status" aria-live="polite"></p>',
      "</div>",
    ].join("");

    document.body.appendChild(root);

    ui = {
      root: root,
      image: root.querySelector(".lightbox__image"),
      caption: root.querySelector(".lightbox__caption"),
      count: root.querySelector(".lightbox__count"),
      status: root.querySelector(".lightbox__status"),
      close: root.querySelector(".lightbox__button--close"),
      prev: root.querySelector(".lightbox__button--prev"),
      next: root.querySelector(".lightbox__button--next"),
      dialog: root.querySelector(".lightbox__dialog"),
    };

    root.addEventListener("click", function (event) {
      var actionNode =
        RHUtils && typeof RHUtils.closest === "function"
          ? RHUtils.closest(event.target, "[data-lightbox-action]")
          : event.target.closest("[data-lightbox-action]");
      if (!actionNode) {
        return;
      }

      var action = actionNode.getAttribute("data-lightbox-action");
      if (action === "close") {
        if (actionNode.classList.contains("lightbox__backdrop") && state.closeOnBackdrop === false) {
          return;
        }
        close();
        return;
      }

      if (action === "prev") {
        move(-1);
        return;
      }

      if (action === "next") {
        move(1);
      }
    });

    ui.image.addEventListener("error", function () {
      ui.status.textContent = "Image could not be loaded.";
    });

    document.addEventListener("keydown", onKeyDown);
    document.addEventListener("click", onDocumentClick);

    return ui;
  }

  function updateUi() {
    var current = state.items[state.index];
    if (!current) {
      return;
    }

    ui.status.textContent = "";
    ui.image.src = current.source;
    ui.image.alt = current.caption || "";

    ui.caption.hidden = current.caption === "";
    ui.caption.textContent = current.caption;

    if (state.items.length > 1) {
      ui.count.textContent = state.index + 1 + " / " + state.items.length;
      ui.prev.hidden = false;
      ui.next.hidden = false;

      if (state.loop) {
        ui.prev.disabled = false;
        ui.next.disabled = false;
      } else {
        ui.prev.disabled = state.index === 0;
        ui.next.disabled = state.index === state.items.length - 1;
      }
    } else {
      ui.count.textContent = "";
      ui.prev.hidden = true;
      ui.next.hidden = true;
      ui.prev.disabled = true;
      ui.next.disabled = true;
    }
  }

  function openItem(item) {
    if (!item) {
      return;
    }

    ensureUi();

    state.lastFocus = document.activeElement;

    var collection = collectItems(item);
    state.items = collection.items;
    state.index = collection.index;
    state.loop = item.loop;
    state.keyboard = item.keyboard;
    state.closeOnBackdrop = item.closeOnBackdrop;
    state.isOpen = true;

    ui.root.hidden = false;
    ui.root.setAttribute("aria-hidden", "false");
    document.body.classList.add("is-lightbox-open");

    updateUi();
    ui.close.focus();
  }

  function close() {
    if (!state.isOpen) {
      return;
    }

    state.isOpen = false;
    state.items = [];
    state.index = 0;

    ui.root.hidden = true;
    ui.root.setAttribute("aria-hidden", "true");
    ui.image.removeAttribute("src");
    ui.image.alt = "";
    ui.caption.textContent = "";
    ui.caption.hidden = true;
    ui.count.textContent = "";
    ui.status.textContent = "";

    document.body.classList.remove("is-lightbox-open");

    if (state.lastFocus && typeof state.lastFocus.focus === "function") {
      state.lastFocus.focus();
    }
  }

  function move(step) {
    if (!state.isOpen || state.items.length <= 1) {
      return;
    }

    var nextIndex = state.index + step;

    if (state.loop) {
      if (nextIndex < 0) {
        nextIndex = state.items.length - 1;
      } else if (nextIndex >= state.items.length) {
        nextIndex = 0;
      }
    } else if (nextIndex < 0 || nextIndex >= state.items.length) {
      return;
    }

    state.index = nextIndex;
    updateUi();
  }

  function trapFocus(event) {
    if (!state.isOpen || event.key !== "Tab") {
      return;
    }

    var focusable = ui.dialog.querySelectorAll(
      "button:not([disabled]), [href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex='-1'])"
    );

    if (!focusable.length) {
      return;
    }

    var first = focusable[0];
    var last = focusable[focusable.length - 1];

    if (event.shiftKey && document.activeElement === first) {
      event.preventDefault();
      last.focus();
      return;
    }

    if (!event.shiftKey && document.activeElement === last) {
      event.preventDefault();
      first.focus();
    }
  }

  function onKeyDown(event) {
    if (!state.isOpen) {
      return;
    }

    if (event.key === "Escape") {
      event.preventDefault();
      close();
      return;
    }

    trapFocus(event);

    if (!state.keyboard) {
      return;
    }

    if (event.key === "ArrowLeft") {
      event.preventDefault();
      move(-1);
      return;
    }

    if (event.key === "ArrowRight") {
      event.preventDefault();
      move(1);
    }
  }

  function onDocumentClick(event) {
    var link =
      RHUtils && typeof RHUtils.closest === "function"
        ? RHUtils.closest(event.target, "a[href]")
        : event.target.closest("a[href]");
    if (!link || (ui && ui.root.contains(link))) {
      return;
    }

    var item = buildItemFromLink(link);
    if (!item) {
      return;
    }

    event.preventDefault();
    openItem(item);
  }

  window.RHLightbox = {
    version: "1.0.0",
    openFromLink: function (link) {
      var item = buildItemFromLink(link);
      if (item) {
        openItem(item);
      }
    },
    close: close,
  };

  ensureUi();
})();
