var utoc_post_parent = document.querySelector(".entry-content");
var utoc_element = document.querySelector(".utoc");

if (!Element.prototype.matches) {
  Element.prototype.matches =
    Element.prototype.msMatchesSelector ||
    Element.prototype.webkitMatchesSelector;
}

if (!Element.prototype.closest) {
  Element.prototype.closest = function (s) {
    var el = this;

    do {
      if (Element.prototype.matches.call(el, s)) return el;
      el = el.parentElement || el.parentNode;
    } while (el !== null && el.nodeType === 1);
    return null;
  };
}

jQuery("body").on("click", ".utoc-item > span", function (e) {
  if (["A", "path", "svg"].indexOf(e.target.tagName) === -1)
    jQuery(this).parent().toggleClass("is-active");
});

jQuery("body").on("click", ".utoc-title", function (e) {
  jQuery(this).parent().toggleClass("is-active");
});

var utoc_is_admin = document.querySelectorAll("body.admin-bar").length > 0;

jQuery(".utoc-item > span > a").on("click", function (e) {
  var elem = jQuery(this);
  var href = elem.attr("data-utoc");

  jQuery(".utoc").removeClass("is-active");

  if (href) {
    e.preventDefault();
    var heading = document.getElementById(href);

    if (heading) {
      var move_from_top =
        utoc_calculate_offset(heading).top -
        (utoc_is_admin ? 30 : 0) -
        (utoc_element.clientHeight || 0) -
        50;

      window.scrollTo({
        top: move_from_top,
        behavior: "smooth",
      });

      if (
        window.innerWidth > 941 &&
        document.querySelectorAll(".smart-head-sticky").length
      ) {
        window.scrollTo({
          top: move_from_top - 90,
          behavior: "smooth",
        });
      }
    }
  }
});

function utoc_calculate_offset(el) {
  var rect = el.getBoundingClientRect(),
    scrollLeft = window.pageXOffset || document.documentElement.scrollLeft,
    scrollTop = window.pageYOffset || document.documentElement.scrollTop;
  return { top: rect.top + scrollTop, left: rect.left + scrollLeft };
}

if (utoc_post_parent && utoc_element) {
  var utoc_section = utoc_post_parent.querySelectorAll(
    utoc_element.dataset.parse
  );
  var utoc_nav = utoc_post_parent.querySelectorAll(".utoc li");

  window.onscroll = () => {
    var current = "";

    utoc_section.forEach((section) => {
      var section_top = utoc_calculate_offset(section).top;
      if (
        window.scrollY >=
        section_top -
          100 -
          (utoc_element.classList.contains("is-sticky")
            ? utoc_element.clientHeight || 0
            : 0)
      ) {
        current = section.getAttribute("id");
      }
    });

    utoc_nav.forEach((li) => {
      li.classList.remove("is-current");
      if (li.classList.contains(`utoc-${current}`)) {
        li.classList.add("is-current");
      }
    });

    // if (document.querySelector(".smart-head-sticky:not(.off)"))
    //   document.body.classList.add("has-sticky-menu");
    // else document.body.classList.remove("has-sticky-menu");
  };
}
