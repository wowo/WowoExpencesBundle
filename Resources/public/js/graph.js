if (typeof(flotData) != "undefined") {
  var _placeholderId = "flot_graph";
  var _tooltipId     = "flot_tooltip";
  $.plot(
      $("#" + _placeholderId), 
      flotData,
      {
        xaxis: {mode: "time"},
        grid: {clickable: true, hoverable: true}
      }
  );
  var previousPoint = null;
  $("#" + _placeholderId).bind("plothover", function (event, pos, item) {
      if (item) {
        if (previousPoint != item.datapoint) {
          previousPoint = item.datapoint;
          $("#" + _tooltipId).remove();
          var y = item.datapoint[1].toFixed(2);
          var date = new Date(item.datapoint[0]);
          if (item.series.label != "") {
            var contents = "<strong>" + item.series.label + "</strong><br />";
          } else {
            var contents = "";
          }
          contents += "<em>" + date.getFullYear() + "-" + (date.getMonth() + 1) + "</em><br />" + y;
          $('<div id="'+ _tooltipId +'">' + contents + '</div>').css( {
              top: item.pageY + 10,
              left: item.pageX + 10,
          }).appendTo("body");
        }
      } else {
        $("#" + _tooltipId).remove();
        previousPoint = null;            
      }
  });
}
