jQuery("body").on("click", ".handle-visible svg", function () {
  var parentElement = jQuery(this).closest("li");

  parentElement.toggleClass("utoc-hidden");

  var dataFor = parentElement.find(".utoc-buttons").attr("data-for");

  jQuery(".utoc-visible-input[data-for='" + dataFor + "']").val(
    parentElement.hasClass("utoc-hidden") ? 0 : 1
  );
});

jQuery("body").on("click", ".handle-edit", function () {
  var parentElement = jQuery(this).closest("li");
  var textElement = parentElement.find("span > a").eq(0);
  var oldValue = textElement.text();
  var newValue = prompt("Enter new value", oldValue);

  if (newValue && oldValue !== newValue) {
    var dataFor = parentElement.find(".utoc-buttons").attr("data-for");

    jQuery(".utoc-text-input[data-for='" + dataFor + "']").val(newValue);
    textElement.text(newValue);
  }
});

jQuery("body").on("click", ".utoc-info", function () {
  jQuery(".utoc-help").toggle();
});

("use strict");

var get_utoc_metabox_html = async function get_utoc_metabox_html() {
  var data = new FormData();

  data.append("action", "get_utoc_metabox_html");
  data.append("nonce", utoc_admin.nonce);
  data.append(
    "post_id",
    new URLSearchParams(window.location.search).get("post")
  );

  return fetch(utoc_admin.ajax_url, {
    method: "POST",
    credentials: "same-origin",
    body: data,
  }).then(function (response) {
    return response.text();
  });
};

var get_utoc_html = async function get_utoc_html() {
  var data = new FormData();

  data.append("action", "get_utoc_html");
  data.append("nonce", utoc_admin.nonce);
  data.append(
    "post_id",
    new URLSearchParams(window.location.search).get("post")
  );

  return fetch(utoc_admin.ajax_url, {
    method: "POST",
    credentials: "same-origin",
    body: data,
  }).then(function (response) {
    return response.text();
  });
};

var utocWasSaving = false;
var utocIsSavingMetaBoxes = wp.data.select("core/edit-post").isSavingMetaBoxes;

if (typeof wp.data !== "undefined")
  wp.data.subscribe(function () {
    var isSaving = utocIsSavingMetaBoxes();

    if (utocWasSaving && !isSaving) {
      if (jQuery(".utoc-block-html").length > 0) {
        get_utoc_html().then(function (res) {
          jQuery(".utoc-block-html").html(res);
        });
      } else {
        get_utoc_metabox_html().then(function (res) {
          jQuery("#utoc-box .inside").html(res);
        });
      }
    }

    utocWasSaving = isSaving;
  });
