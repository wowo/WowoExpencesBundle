$(document).ready(function () {
  var tagsEdit = false;
  var tagsEditButton = '<input type="submit" value="Save" id="tags-save"/>';
  var ajaxLoader = '<img src="/css/ajax-loader.gif" class="ajax-loader"/>';
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
    return false;
  });
  function saveTags()
  {
    $("#tags-save").replaceWith(ajaxLoader);
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

  $(".multi-tags").click(function () {
    var checkboxes = $("#operations tbody input:checked");
    if (checkboxes.length == 0) {
      alert("Please check operations to tag");
    } else {
      var tags = prompt("Please type tags for selected operations (separate by comma)");
      var ids  = [];
      checkboxes.each(function (index, element) {
        ids.push($(element).attr("name").match(/\[(.*)\]/)[1]);
      });
      
      $(".multi-tags").after(ajaxLoader);
      $.ajax({
        url: router.multi_tags,
        type: "POST",
        data: {ids: ids, tags: tags},
        success: function (html) {
          checkboxes.each(function (index, element) {
            if (html.length > 0 && $(element).parents("tr").find(".tags a.tag").length > 0) {
              $(element).parents("tr").find(".tags span").append(", ");
            }
            $(element).parents("tr").find(".tags span").append(html);
            $("#operations input[type=checkbox]").attr("checked", false);
          });
          $(".ajax-loader").remove();
        },
        error: function(xhr) {
          $(".ajax-loader").remove();
          alert(xhr.responseText);
        }
      });
    }
  });

  $("#operations .checkbox-click").click(function () {
    var checkbox = $(this).parents("tr").find("input[type=checkbox]");
    checkbox.attr("checked", !checkbox.attr("checked"));
  });

  $("#toggle-all").click(function () {
    $("#operations tbody input[type=checkbox]").attr("checked", $(this).attr("checked"));
  });
});
