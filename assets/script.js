(function ($) {
  "use strict";

  $(document).ready(function () {
    var image_frame;

    $(".wp-toolkit__field-container").on(
      "click",
      ".js-wp-toolkit-image-upload-button",
      function (e) {
        e.preventDefault();

        image_frame = wp.media.frames.image_frame = wp.media({
          library: { type: "image" },
        });
        image_frame.open();

        var id = $(this)
          .data("hidden-input")
          .replace(/(\[|\])/g, "\\$1");
        console.log(id);

        image_frame.on("select", function () {
          var attachment = image_frame
            .state()
            .get("selection")
            .first()
            .toJSON();
          console.log(id);
          $("#image-" + id).val(attachment.url);

          $("#js-" + id + "-image-preview")
            .removeClass("is-hidden")
            .attr("src", attachment.url);

          $(".js-wp-toolkit-image-upload-button").text("Change Image");

          $("#" + id).css({ background: "red" });
        });

        image_frame.open();
      }
    );

    $(".wp-toolkit__field-container").on(
      "click",
      ".wp-toolkit-repeated-header",
      function () {
        $(this)
          .siblings(".wp-toolkit__repeated-content")
          .toggleClass("is-hidden");
      }
    );

    $(".wp-toolkit__repeated-blocks").on(
      "click",
      ".wp-toolkit__remove",
      function () {
        $(this).closest(".wp-toolkit__repeated").remove();
        return false;
      }
    );

    $(".wp-toolkit__repeated-blocks").sortable({
      opacity: 0.6,
      revert: true,
      cursor: "move",
      handle: ".js-wp-toolkit-sort",
    });
  });
})(jQuery);
