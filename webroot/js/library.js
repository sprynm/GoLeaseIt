(function () {
  "use strict";

  if (window.RHLibrary && window.RHLibrary.version) {
    return;
  }

  var CLOSE_TRIGGER_SELECTOR = '.js-close, [data-function="close"], .close';
  var matchesSelector =
    Element.prototype.matches ||
    Element.prototype.msMatchesSelector ||
    Element.prototype.webkitMatchesSelector;

  function isElement(value) {
    return value instanceof Element;
  }

  function asString(value) {
    return value == null ? "" : String(value);
  }

  function stripWrappingQuotes(value) {
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
    var normalized = stripWrappingQuotes(value);
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

  function toArray(value) {
    if (!value) {
      return [];
    }

    if (Array.isArray(value)) {
      return value.slice();
    }

    return Array.prototype.slice.call(value);
  }

  function qs(selector, root) {
    if (!selector) {
      return null;
    }

    var scope = root || document;
    return scope.querySelector(selector);
  }

  function qsa(selector, root) {
    if (!selector) {
      return [];
    }

    var scope = root || document;
    return toArray(scope.querySelectorAll(selector));
  }

  function matches(element, selector) {
    if (!isElement(element) || !selector) {
      return false;
    }
    return matchesSelector.call(element, selector);
  }

  function closest(element, selector) {
    if (!selector) {
      return null;
    }

    if (isElement(element) && typeof element.closest === "function") {
      return element.closest(selector);
    }

    var node = element && element.parentElement ? element.parentElement : null;
    if (!node || typeof node.closest !== "function") {
      return null;
    }

    return node.closest(selector);
  }

  function ready(callback) {
    if (typeof callback !== "function") {
      return;
    }

    if (document.readyState === "loading") {
      document.addEventListener("DOMContentLoaded", callback, { once: true });
      return;
    }

    callback();
  }

  function on(root, eventName, selector, handler, options) {
    var host = root || document;
    var delegatedSelector = selector;
    var listener = handler;
    var listenerOptions = options;

    if (typeof selector === "function") {
      delegatedSelector = null;
      listener = selector;
      listenerOptions = handler;
    }

    if (!host || typeof host.addEventListener !== "function" || typeof listener !== "function") {
      return function noop() {};
    }

    var wrapped = function (event) {
      if (!delegatedSelector) {
        listener.call(host, event, host);
        return;
      }

      var matched = closest(event.target, delegatedSelector);
      if (!matched) {
        return;
      }
      if (host !== document && host !== window && typeof host.contains === "function" && !host.contains(matched)) {
        return;
      }

      listener.call(matched, event, matched);
    };

    host.addEventListener(eventName, wrapped, listenerOptions);

    return function unsubscribe() {
      host.removeEventListener(eventName, wrapped, listenerOptions);
    };
  }

  function setHidden(target, isHidden) {
    if (!target) {
      return;
    }

    if (isHidden) {
      target.setAttribute("hidden", "hidden");
      target.style.display = "none";
      target.setAttribute("aria-hidden", "true");
      return;
    }

    target.removeAttribute("hidden");
    target.style.display = "";
    target.removeAttribute("aria-hidden");
  }

  var RHUtils = {
    asString: asString,
    stripWrappingQuotes: stripWrappingQuotes,
    parseScalar: parseScalar,
    toArray: toArray,
    qs: qs,
    qsa: qsa,
    matches: matches,
    closest: closest,
    ready: ready,
    on: on,
    setHidden: setHidden,
  };

  function resolveExplicitTarget(trigger) {
    var targetSelector = trigger.getAttribute("data-close-target");
    if (!targetSelector) return null;

    if (targetSelector === "self") {
      return trigger;
    }

    var closestMatch = RHUtils.closest(trigger, targetSelector);
    if (closestMatch) return closestMatch;

    return RHUtils.qs(targetSelector);
  }

  function resolveCloseTarget(trigger) {
    var explicitTarget = resolveExplicitTarget(trigger);
    if (explicitTarget) return explicitTarget;

    return (
      trigger.closest("[data-close-container]") ||
      trigger.closest(".notification") ||
      trigger.closest(".legal-notice") ||
      trigger.closest(".notice") ||
      trigger.closest(".message") ||
      trigger.closest(".error-message") ||
      null
    );
  }

  function closeElement(target, trigger) {
    if (!target) return false;

    RHUtils.setHidden(target, true);

    var detail = { trigger: trigger || null };
    target.dispatchEvent(new CustomEvent("rh:closed", { bubbles: true, detail: detail }));
    return true;
  }

  function handleCloseTrigger(trigger, nativeEvent) {
    if (!trigger) return false;

    var target = resolveCloseTarget(trigger);
    if (!target) return false;

    if (nativeEvent) {
      nativeEvent.preventDefault();
    }
    return closeElement(target, trigger);
  }

  document.addEventListener("click", function (event) {
    var trigger = RHUtils.closest(event.target, CLOSE_TRIGGER_SELECTOR);
    if (!trigger) return;

    handleCloseTrigger(trigger, event);
  });

  window.RHLibrary = {
    version: "1.1.0",
    utils: RHUtils,
    closeElement: closeElement,
    resolveCloseTarget: resolveCloseTarget,
    handleCloseTrigger: handleCloseTrigger,
  };
})();
