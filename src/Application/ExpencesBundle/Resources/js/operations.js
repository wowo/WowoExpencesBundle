$(document).ready(function () {
  var tagsEdit = false;
  var tagsEditButton = '<input type="submit" value="Save" id="tags-save"/>';
  $(".tags").mouseover(function () {
    if (!tagsEdit) {
      $(this).find(".tags-edit").show();
    }
  });
  $(".tags").mouseout(function () {
    $(this).find(".tags-edit").hide();
  });
  $(".tags-edit").click(function () {
    if (!tagsEdit) {
      var tags = [];
      $(this).parent("td").find("a.tag").each(function (index, element) {
        tags.push($(element).html());
      });
      $(this).parent("td").find("span").empty().append('<input type="text" id="tags-input" value="' + tags.join(", ") +'"/> ' + tagsEditButton);
      $("#tags-input").focus();
      $(this).find(".tags-edit").hide();
      tagsEdit = true;
    }
  });
  function saveTags()
  {
    $("#tags-save").replaceWith('<img src="/css/ajax-loader.gif" class="ajax-loader"/>');
    $.ajax({
      url: router.save_tags,
      type: "POST",
      data: {id: $("#tags-input").parents("tr").attr("id").split("_")[1], tags: $("#tags-input").val()},
      success: function (html) {
        $("#tags-input").parents("td").find("span").empty().append(html);
        tagsEdit = false;
      },
      error: function(xhr) {
        $(".ajax-loader").replaceWith(tagsEditButton);
        alert(xhr.responseText);
      }
    });
  }
  $("#tags-save").live("click", saveTags);
  $("#tags-input").live("keydown", function (event) {
    if (event.keyCode == "13") {
      saveTags();
    }
  });
});
