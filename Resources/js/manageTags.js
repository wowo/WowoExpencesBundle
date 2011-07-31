$(document).ready(function () {
  $('.taggable div div label').hide();
  $('.taggable input[type="text"]').each(function (k,v) {
    if ($(v).attr('name').match(/\$\$key\$\$/i)) {
      var className = "add_tag";
      var path = "/css/fugue-icons/icons/plus-button.png";
    } else {
      var className = "remove_tag";
      var path = "/css/fugue-icons/icons/cross-button.png";
    }
    $('<a href="#" class="'+ className +'"><img src="'+ path +'" alt=""/></a>').insertAfter($(v));
  });

  var insertedFieldsCounter = 100;
  $('.taggable .add_tag').live("click", function() {
    var div = $(this).parent("div").clone();
    var name = $(div).find("input").attr("name");
    $(div).find("input").attr("name", name.replace(/\$\$key\$\$/, (insertedFieldsCounter++) + "]"));
    $(div).find("input").val("");
    $(div).find("img").remove();
    var className = "remove_tag";
    var path = "/css/fugue-icons/icons/cross-button.png";
    $('<a href="#" class="'+ className +'"><img src="'+ path +'" alt=""/></a>').insertAfter($(div).find("input"));
    $(div).insertAfter($(this).parent("div"));
  });

  $('.taggable .remove_tag').live("click", function() {
    $(this).parent("div").remove();
  });
});
