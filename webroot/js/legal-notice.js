(function () {
  "use strict";

  var notice = document.querySelector("[data-legal-notice]");
  if (!notice) return;

  var storageKey = "legal_notice_dismissed";
  var dismissValue = "1";

  try {
    if (window.localStorage && window.localStorage.getItem(storageKey) === dismissValue) {
      document.documentElement.classList.add("legal-notice-dismissed");
      notice.setAttribute("hidden", "hidden");
      return;
    }
  } catch (_error) {
    // If localStorage is unavailable, keep showing the notice.
  }

  // Ensure visibility when not dismissed (guards against stale hidden markup/cache).
  document.documentElement.classList.remove("legal-notice-dismissed");
  notice.removeAttribute("hidden");

  notice.addEventListener("rh:closed", function () {
    document.documentElement.classList.add("legal-notice-dismissed");
    notice.setAttribute("hidden", "hidden");
    try {
      if (window.localStorage) {
        window.localStorage.setItem(storageKey, dismissValue);
      }
    } catch (_error) {
      // Ignore storage write failures.
    }
  });
})();
