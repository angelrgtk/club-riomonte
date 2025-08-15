jQuery(document).ready(function ($) {
  var mediaUploader;

  // Open media library when select button is clicked
  $("#select_profile_picture").click(function (e) {
    e.preventDefault();

    // If the uploader object has already been created, reopen the dialog
    if (mediaUploader) {
      mediaUploader.open();
      return;
    }

    // Extend the wp.media object
    mediaUploader = wp.media.frames.file_frame = wp.media({
      title: "Seleccionar Foto de Perfil",
      button: {
        text: "Seleccionar Imagen",
      },
      library: {
        type: "image",
      },
      multiple: false,
    });

    // When a file is selected, grab the URL and set it as the text field's value
    mediaUploader.on("select", function () {
      var attachment = mediaUploader.state().get("selection").first().toJSON();

      $("#profile_picture_id").val(attachment.id);
      $("#profile_picture_preview").html(
        '<img src="' +
          attachment.sizes.thumbnail.url +
          '" style="max-width: 150px; height: auto; border: 1px solid #ddd; padding: 5px;">'
      );
      $("#remove_profile_picture").show();
    });

    // Open the uploader dialog
    mediaUploader.open();
  });

  // Remove image when remove button is clicked
  $("#remove_profile_picture").click(function (e) {
    e.preventDefault();

    $("#profile_picture_id").val("");
    $("#profile_picture_preview").html("<p>No hay imagen seleccionada</p>");
    $(this).hide();
  });
});
