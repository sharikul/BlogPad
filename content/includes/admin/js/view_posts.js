(function() {
  document.body.id = 'postpage';

  if ($('#filter_form')[0] != null) {
    $('#filter').click(function() {
      var filter_by;
      filter_by = $('#filter_by').val().trim();
      if (filter_by !== '') {
        if (!window.location.href.match(/filter_by\=[\w]+/)) {
          window.location = "" + base + "&filter_by=" + filter_by;
        } else {
          window.location = window.location.href.replace(/filter_by\=[\w]+/, "filter_by=" + filter_by);
        }
      }
    });
  }

  if ($('#remove_filter')[0] != null) {
    $('#remove_filter').click(function() {
      return window.location = window.location.href.replace(/&filter_by\=[\w]+/, '');
    });
  }

}).call(this);
