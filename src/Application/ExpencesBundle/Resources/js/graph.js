if (typeof(flotData) != "undefined") {
  var _placeholderId = "flot_graph";
  var _tooltipId     = "flot_tooltip";
  $.plot(
      $("#" + _placeholderId), 
      [{data: flotData, lines: { show: true }, points: { show: true }}],
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
          var contents = "<strong>" + date.getFullYear() + "-" + (date.getMonth() + 1) + "</strong><br />" + y;
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
