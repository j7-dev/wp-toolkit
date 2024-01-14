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
    // TODO 好像壞了
    $(".wp-toolkit__repeated").sortable({
      opacity: 0.6,
      revert: true,
      cursor: "move",
      handle: ".js-wp-toolkit-sort",
    });
  });

  //---------------- OPTIONS PAGE ----------------//

  //Initiate Color Picker
  if ($(".wp-color-picker-field").length > 0) {
    $(".wp-color-picker-field").wpColorPicker();
  }

  // For Files Upload
  $(".boospot-browse-button").on("click", function (event) {
    event.preventDefault();

    var self = $(this);

    // Create the media frame.
    var file_frame = (wp.media.frames.file_frame = wp.media({
      title: self.data("uploader_title"),
      button: {
        text: self.data("uploader_button_text"),
      },
      multiple: false,
    }));

    file_frame.on("select", function () {
      attachment = file_frame.state().get("selection").first().toJSON();
      self.prev(".wpsa-url").val(attachment.url).change();
    });

    // Finally, open the modal
    file_frame.open();
  });

  // Prevent page navigation for un-saved changes
  $(function () {
    var changed = false;

    $("input, textarea, select, checkbox").change(function () {
      changed = true;
    });

    $(".nav-tab-wrapper a").click(function () {
      if (changed) {
        window.onbeforeunload = function () {
          return "Changes you made may not be saved.";
        };
      } else {
        window.onbeforeunload = "";
      }
    });

    $(".submit :input").click(function () {
      window.onbeforeunload = "";
    });
  });

  // The "Upload" button
  $(".boospot-image-upload").click(function () {
    var send_attachment_bkp = wp.media.editor.send.attachment;
    var button = $(this);
    wp.media.editor.send.attachment = function (props, attachment) {
      $(button).parent().prev().attr("src", attachment.url);
      if (attachment.id) {
        $(button).prev().val(attachment.id);
      }
      wp.media.editor.send.attachment = send_attachment_bkp;
    };
    wp.media.editor.open(button);
    return false;
  });

  // The "Remove" button (remove the value from input type='hidden')
  $(".boospot-image-remove").click(function () {
    var answer = confirm("Are you sure?");
    if (answer == true) {
      var src = $(this).parent().prev().attr("data-src");
      $(this).parent().prev().attr("src", src);
      $(this).prev().prev().val("");
    }
    return false;
  });
})(jQuery);
