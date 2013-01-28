$(function(){
  //https://github.com/twitter/bootstrap/pull/581#issuecomment-4966967
  // Function to activate the tab
  function activateTab() {
    var activeTab = $('[href=' + window.location.hash.replace('/', '') + ']');
    activeTab && activeTab.tab('show');
  }

  // Trigger when the page loads
  activateTab();

  // Trigger when the hash changes (forward / back)
  $(window).hashchange(function(e) {
    activateTab();
  });

  // Change hash when a tab changes
  $('a[data-toggle="tab"], a[data-toggle="pill"]').on('shown', function () {
    window.location.hash = '/' + $(this).attr('href').replace('#', '');
  });

  // add parser through the tablesorter addParser method
  // looks for the data-days attribute in the <span> in the <td>
  $.tablesorter.addParser({
    // set a unique id
    id: 'data-date',
    is: function(s) {
      // return false so this parser is not auto detected
      return false;
    },
    format: function(s, table, cell, cellIndex) {
      d = $(cell).data('date');
      dt = new Date(d.substr(6,4)+'-'+d.substr(3,2)+'-'+d.substr(0,2));
      console.log(dt);
      return dt.getTime();

    },
    // set type, either numeric or text
    type: 'numeric'
  });

  // add parser through the tablesorter addParser method
  // looks for the data-days attribute in the <span> in the <td>
  $.tablesorter.addParser({
    // set a unique id
    id: 'span-data-days',
    is: function(s) {
      // return false so this parser is not auto detected
      return false;
    },
    format: function(s, table, cell, cellIndex) {
      // format your data for normalization

      return $(cell).find("span").data('days');

    },
    // set type, either numeric or text
    type: 'numeric'
  });


  $("#tournament-tables table").tablesorter({
    dateFormat : "ddmmyyyy", // set the default date format

    headers: {
      1: { sorter: "data-date" },
      2: { sorter: "span-data-days" }
    }

  });
});

/**
 * 0-pad a Js getDate or getMonth value
 * @param  {[type]} num [description]
 * @return {[type]}     [description]
 */
function padJsDate(num) {
  return ('0' + num).slice(-2);
}