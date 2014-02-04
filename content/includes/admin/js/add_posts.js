(function() {
  var all_fields_complete, categories, description, modal_preview, page_type, post_content, post_type, preview, save, slug, title, _catlist, _static;

  slug = $('#slug');

  categories = $('#categories');

  description = $('#description');

  _catlist = $('.category');

  post_content = $('#p_content');

  preview = $('#preview');

  modal_preview = $('#modal_preview');

  title = $('#p_title');

  _static = $('#static');

  save = $('#save');

  page_type = window.page_type.trim();

  post_type = window.post_type.trim();

  all_fields_complete = function() {
    return title.val().trim() !== '' && post_content.val() !== '' && slug.val().trim() !== '' && categories.val().trim() !== '';
  };

  if (_static[0] !== void 0) {
    if (post_type === 'static') {
      _static.hide();
    } else if (page_type === 'new') {
      _static.val('Save as Static Post').attr('title', 'Save this post as a static post.');
    } else if (post_type !== 'static') {
      _static.val('Convert to Static Post').attr('title', 'Convert this post to a static post. After conversion,you will find this post in your static posts directory. This post will be removed from the database.');
    }
  }

  if (page_type === 'edit') {
    save.val('Save Post').attr('title', 'Save this Post.');
  } else {
    save.val('Publish Post').attr('title', 'Publish this Post to the database.');
  }

  modal_preview.hide();

  document.title = page_type === 'edit' ? "Editing " + (title.val().trim()) : 'Add a Post';

  $(document).delegate('#p_content', 'keydown', function(e) {
    var end, keyCode, start;
    keyCode = e.keyCode || e.which;
    if (keyCode === 9) {
      e.preventDefault();
      start = $(this).get(0).selectionStart;
      end = $(this).get(0).selectionEnd;
      $(this).val("" + ($(this).val().substring(0, start)) + "\t" + ($(this).val().substring(end)));
      return $(this).get(0).selectionStart = $(this).get(0).selectionEnd = start + 1;
    }
  });

  title.keyup(function() {
    var _title;
    _title = $(this).val().trim();
    if (page_type === 'edit') {
      if (_title !== '') {
        document.title = "Editing " + _title;
      } else {
        document.title = 'Edit a Post';
      }
    } else {
      if (_title !== '') {
        document.title = "Adding " + _title;
      } else {
        document.title = 'Add a Post';
      }
    }
    _title = title !== '' ? _title.split(/\s+/).join(' ').replace(/[\W\s]+/g, ' ').split(/\s+/g).join('-').toLowerCase().trim() : void 0;
    return slug.val(_title.substr(0, 50));
  });

  slug.keyup(function() {
    var _value;
    _value = $(this).val().trim();
    console.log(_value);
    if (_value.match(/\s+/g)) {
      return $(this).val(_value.replace(/\s/, '-'));
    }
  });

  if (page_type !== '' || page_type !== 'static') {
    _static.click(function() {
      var input;
      input = $('<input>').attr({
        type: 'hidden',
        name: post_type === 'db' ? 'convert_to_static_post' : 'static_post'
      });
      if (post_type === 'db' && page_type === 'edit') {
        if (!confirm('Are you sure that you want to convert this post to a static post? It will be deleted from the database (forever).')) {
          return false;
        }
      }
      return $('form').eq(0).append(input);
    });
  }

  _catlist.each(function(index, value) {
    return $(value).click(function() {
      if (categories[0].value.trim() !== '') {
        categories[0].value += ", " + (this.textContent.trim());
        window.categories += ", " + (this.textContent.trim());
      } else {
        categories[0].value += this.textContent.trim();
        window.categories = this.textContent.trim();
      }
      return false;
    });
  });

  categories.keyup(function() {
    return window.categories = $(this).val();
  });

  preview.click(function() {
    if (all_fields_complete()) {
      $('#postname').text(title.val().trim());
      return $.get('../content/includes/admin/preview.php', {
        title: title.val().trim(),
        content: post_content.val(),
        description: description.val().trim(),
        date: window.date,
        slug: slug.val().trim(),
        categories: window.categories,
        author: window.author
      }, function(preview) {
        var iframe;
        iframe = $('iframe')[0].contentDocument;
        iframe.open();
        iframe.write(preview);
        iframe.close();
        modal_preview.show('slow');
        return window.location = '#modal_preview';
      });
    }
  });

  $('#close_modal').click(function() {
    return modal_preview.hide('slow');
  });

  if (page_type === 'edit') {
    $('#delete').click(function() {
      if (confirm('Are you sure that you want to delete this post?')) {
        return $('form').eq(0).append($('<input>').attr({
          type: 'hidden',
          name: 'delete_post'
        }));
      } else {
        return false;
      }
    });
  }

}).call(this);
