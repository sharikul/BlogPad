document.body.id = 'postpage'

if $('#filter_form')[0]?
    $('#filter').click ->
        filter_by = $('#filter_by').val().trim()

        if filter_by isnt ''
            if not window.location.href.match /filter_by\=[\w%]+/
                window.location = "#{base}&filter_by=#{filter_by}"
                return
            else
                window.location = window.location.href.replace /filter_by\=[\w%]+/, "filter_by=#{filter_by}"
                return

if $('#remove_filter')[0]?
    $('#remove_filter').click ->
        window.location = window.location.href.replace /&filter_by\=[\w%]+/, ''