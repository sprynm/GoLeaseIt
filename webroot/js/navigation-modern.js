(function () {
  "use strict";

  var nav = document.querySelector("[data-site-nav]");
  var toggle = document.getElementById("site-nav-toggle");
  var drawer = document.getElementById("site-nav-drawer");
  var drawerBody = drawer ? drawer.querySelector("[data-nav-drawer-body]") : null;
  var drawerClose = drawer ? drawer.querySelector(".site-nav-drawer__close") : null;
  var mqDesktop = window.matchMedia("(min-width: 48rem)");
  var popovers = [];
  var activePopover = null;

  if (!nav || !toggle || !drawer || !drawerBody) {
    return;
  }

  document.documentElement.classList.add("js-nav-ready");

  function closeDrawer() {
    if (!drawer.open) {
      return;
    }
    drawer.close();
    toggle.setAttribute("aria-expanded", "false");
    toggle.focus();
    document.body.classList.remove("is-nav-open");
  }

  function openDrawer() {
    if (!drawer.open) {
      drawer.showModal();
    }
    toggle.setAttribute("aria-expanded", "true");
    document.body.classList.add("is-nav-open");
  }

  function closeAllPopovers() {
    popovers.forEach(function (entry) {
      entry.menu.hidePopover();
      entry.trigger.setAttribute("aria-expanded", "false");
    });
    activePopover = null;
  }

  function positionPopover(trigger, menu) {
    var rect = trigger.getBoundingClientRect();
    var top = rect.bottom + 8;
    var left = Math.max(8, rect.left);
    var maxLeft = window.innerWidth - menu.offsetWidth - 8;
    menu.style.top = top + "px";
    menu.style.left = Math.min(left, maxLeft) + "px";
  }

  function focusFirstLink(menu) {
    var link = menu.querySelector("a, button");
    if (link) {
      link.focus();
    }
  }

  function cycleMenuFocus(event, menu) {
    var items = Array.prototype.slice.call(menu.querySelectorAll("a, button"));
    if (!items.length) {
      return;
    }
    var current = items.indexOf(document.activeElement);
    if (event.key === "ArrowDown") {
      event.preventDefault();
      var next = current < 0 ? 0 : (current + 1) % items.length;
      items[next].focus();
    } else if (event.key === "ArrowUp") {
      event.preventDefault();
      var prev = current <= 0 ? items.length - 1 : current - 1;
      items[prev].focus();
    }
  }

  function buildDesktopPopovers() {
    var topItems = nav.querySelectorAll(":scope > ul > li");
    topItems.forEach(function (item, index) {
      var submenu = item.querySelector(":scope > ul");
      if (!submenu) {
        return;
      }

      item.classList.add("has-submenu");
      submenu.classList.add("site-nav__submenu");
      submenu.id = submenu.id || "site-nav-submenu-" + index;
      submenu.setAttribute("popover", "manual");

      var labelSource = item.querySelector(":scope > a");
      var labelText = labelSource ? labelSource.textContent.trim() : "submenu";

      var trigger = document.createElement("button");
      trigger.type = "button";
      trigger.className = "site-nav__submenu-trigger";
      trigger.setAttribute("aria-controls", submenu.id);
      trigger.setAttribute("aria-expanded", "false");
      trigger.setAttribute("aria-haspopup", "menu");
      trigger.setAttribute("aria-label", "Toggle " + labelText + " submenu");

      item.insertBefore(trigger, submenu);

      trigger.addEventListener("click", function (event) {
        event.preventDefault();
        if (!mqDesktop.matches) {
          return;
        }

        var isOpen = trigger.getAttribute("aria-expanded") === "true";
        closeAllPopovers();

        if (!isOpen) {
          submenu.showPopover();
          positionPopover(trigger, submenu);
          trigger.setAttribute("aria-expanded", "true");
          activePopover = { trigger: trigger, menu: submenu };
        }
      });

      trigger.addEventListener("keydown", function (event) {
        if (!mqDesktop.matches) {
          return;
        }
        if (event.key === "ArrowDown" || event.key === "Enter" || event.key === " ") {
          event.preventDefault();
          closeAllPopovers();
          submenu.showPopover();
          positionPopover(trigger, submenu);
          trigger.setAttribute("aria-expanded", "true");
          activePopover = { trigger: trigger, menu: submenu };
          focusFirstLink(submenu);
        }
      });

      submenu.addEventListener("keydown", function (event) {
        if (event.key === "Escape") {
          closeAllPopovers();
          trigger.focus();
          return;
        }
        cycleMenuFocus(event, submenu);
      });

      submenu.addEventListener("click", function (event) {
        if (event.target.closest("a")) {
          closeAllPopovers();
        }
      });

      popovers.push({ trigger: trigger, menu: submenu });
    });
  }

  function transformListToDrawer(list) {
    var clone = list.cloneNode(true);

    clone.querySelectorAll("li").forEach(function (item) {
      var submenu = item.querySelector(":scope > ul");
      var link = item.querySelector(":scope > a");
      if (!submenu || !link) {
        return;
      }

      var details = document.createElement("details");
      details.className = "site-nav-drawer__details";
      var summary = document.createElement("summary");
      summary.textContent = link.textContent.trim();
      details.appendChild(summary);

      var subList = submenu.cloneNode(true);
      var overviewItem = document.createElement("li");
      var overviewLink = link.cloneNode(true);
      overviewLink.textContent = "Overview";
      overviewItem.appendChild(overviewLink);
      subList.insertBefore(overviewItem, subList.firstChild);
      details.appendChild(subList);

      link.remove();
      submenu.remove();
      item.insertBefore(details, item.firstChild);
    });

    clone.querySelectorAll("details").forEach(function (details) {
      details.addEventListener("toggle", function () {
        if (!details.open) {
          return;
        }
        var parent = details.parentElement;
        parent.querySelectorAll(":scope > li > details[open], :scope > details[open]").forEach(function (sibling) {
          if (sibling !== details) {
            sibling.open = false;
          }
        });
      });
    });

    return clone;
  }

  function buildDrawer() {
    var list = nav.querySelector(":scope > ul");
    if (!list) {
      return;
    }
    drawerBody.innerHTML = "";
    drawerBody.appendChild(transformListToDrawer(list));
  }

  function bindGlobalEvents() {
    toggle.addEventListener("click", function () {
      if (drawer.open) {
        closeDrawer();
      } else {
        openDrawer();
      }
    });

    drawerClose.addEventListener("click", closeDrawer);

    drawer.addEventListener("click", function (event) {
      if (event.target === drawer) {
        closeDrawer();
      }
    });

    drawer.addEventListener("close", function () {
      toggle.setAttribute("aria-expanded", "false");
      document.body.classList.remove("is-nav-open");
    });

    drawerBody.addEventListener("click", function (event) {
      if (event.target.closest("a")) {
        closeDrawer();
      }
    });

    document.addEventListener("click", function (event) {
      if (!mqDesktop.matches || !activePopover) {
        return;
      }
      if (event.target.closest(".site-nav__submenu") || event.target.closest(".site-nav__submenu-trigger")) {
        return;
      }
      closeAllPopovers();
    });

    document.addEventListener("keydown", function (event) {
      if (event.key === "Escape") {
        closeDrawer();
        closeAllPopovers();
      }
    });

    window.addEventListener("resize", function () {
      if (activePopover && mqDesktop.matches) {
        positionPopover(activePopover.trigger, activePopover.menu);
      }
      if (mqDesktop.matches) {
        closeDrawer();
      } else {
        closeAllPopovers();
      }
    });

    window.addEventListener("scroll", function () {
      if (activePopover && mqDesktop.matches) {
        positionPopover(activePopover.trigger, activePopover.menu);
      }
    }, { passive: true });
  }

  buildDesktopPopovers();
  buildDrawer();
  bindGlobalEvents();
})();

