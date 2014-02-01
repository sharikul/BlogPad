slug = $ '#slug'
categories = $ '#categories' 
description = $ '#description' 

_catlist = $ '.category' 

# Avoid trimming the contents of the textarea later on as they may be tabs which would need to be rendered into code blocks
post_content = $ '#p_content' 

preview = $ '#preview' 
modal_preview = $ '#modal_preview' 

title = $ '#p_title' 
_static = $ '#static' 


save = $ '#save' 

page_type = window.page_type.trim()
post_type = window.post_type.trim()

all_fields_complete = ->
    title.val().trim() isnt '' and post_content.val() isnt '' and slug.val().trim() isnt '' and categories.val().trim() isnt ''

if _static[0] isnt undefined
    if post_type is 'static'
        _static.hide()

    else if page_type is 'new'
        _static.val('Save as Static Post').attr 'title', 'Save this post as a static post.'

    else if post_type isnt 'static'
        _static.val('Convert to Static Post').attr 'title', 'Convert this post to a static post. After conversion,you will find this post in your static posts directory. This post will be removed from the database.'

if page_type is 'edit'
    save.val('Save Post').attr 'title', 'Save this Post.'

else
    save.val('Publish Post').attr 'title', 'Publish this Post to the database.'

modal_preview.hide()

document.title = if page_type is 'edit' then "Editing #{title.val().trim()}" else 'Add a Post'

# Enables the use of the tab key in the textarea. Copied from http://stackoverflow.com/a/6637396 but modified for CoffeeScript.
$(document).delegate '#p_content', 'keydown', (e) ->
    keyCode = e.keyCode or e.which

    if keyCode is 9
        e.preventDefault()
        start = $(this).get(0).selectionStart
        end = $(this).get(0).selectionEnd

        # set textarea value to: text before caret + tab + text after caret
        $(this).val "#{$(this).val().substring(0, start)}\t#{$(this).val().substring(end)}"

        # put caret at right position again
        $(this).get(0).selectionStart = $(this).get(0).selectionEnd = start + 1

title.keyup ->
    _title = $(this).val().trim()

    if page_type is 'edit'
        if _title isnt ''
            document.title = "Editing #{_title}"
        else
            document.title = 'Edit a Post'
    else
        if _title isnt ''
            document.title = "Adding #{_title}"
        else
            document.title = 'Add a Post'

    # The slug field is likely to be empty at this point, so convert the title to a slug form with hyphens
    _title = if title isnt '' then _title.split(/\s+/).join(' ').replace(/[\W\s]+/g, ' ').split(/\s+/g).join('-').toLowerCase().trim()

    # Max length of a post slug is 50
    slug.val _title.substr 0, 50

slug.keyup ->
    _value = $(this).val().trim()
    console.log _value
    if _value.match /\s+/g
        $(this).val(_value.replace /\s/, '-')

if page_type isnt '' or page_type isnt 'static'
    _static.click ->
        input = $('<input>').attr
            type: 'hidden'
            name: if post_type is 'db' then 'convert_to_static_post' else 'static_post'


        if post_type is 'db' and page_type is 'edit'

            # Show a confirmation message when the user wants to convert a DB post to a static one. Submit the request on 'ok'
            return no if not confirm 'Are you sure that you want to convert this post to a static post? It will be deleted from the database (forever).'

        $('form').eq(0).append input

_catlist.each (index, value) ->
    $(value).click ->
        
        # Prepend a category with a comma if the category field is currently filled with data
        if categories[0].value.trim() isnt ''
            categories[0].value += ", #{@textContent.trim()}"

            # Update the window.categories variable so that categories can be viewed on preview
            window.categories += ", #{@textContent.trim()}"
        else
            categories[0].value += @textContent.trim()

            window.categories = @textContent.trim()
        no

categories.keyup ->
    window.categories += $(this).val()

preview.click ->

    # Display post title on the title section of the preview modal - if all fields are complete
    if all_fields_complete()
        $('#postname').text title.val().trim()

        $.get '../content/includes/admin/preview.php',
            title: title.val().trim()
            content: post_content.val()
            description: description.val().trim()
            date: window.date
            slug: slug.val().trim()
            categories: window.categories
            author: window.author
            
            (preview) ->
                iframe = $('iframe')[0].contentDocument
                iframe.open()
                iframe.write preview 
                iframe.close()

                modal_preview.show 'slow'
                window.location = '#modal_preview'

$('#close_modal').click ->
    modal_preview.hide 'slow'

if page_type is 'edit'
    $('#delete').click ->
        if confirm 'Are you sure that you want to delete this post?'
            $('form').eq(0).append $('<input>').attr
                type: 'hidden'
                name: 'delete_post'

        else
            no
