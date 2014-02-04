<?php

$messages = array();

// Set to true if the slug provided is a real post
$is_post = ( isset($_GET['post']) && Post::is_slug($_GET['post']) ) ? true: false;

if( $is_post ) {
    $post_type = ( Post::is_static_post($_GET['post']) ) ? 'static': 'db';
}

// If the form has been redirected due to a slug change, show the update successful message for continuity.
if( isset($_SESSION['update_rdirect']) ) {
    $messages[] = "<em>$title</em> has been saved.";

    // Now unset the key so that the message doesn't reappear unless slug is reupdated.
    unset($_SESSION['update_rdirect']);
}

if( isset($_POST['p_slug']) ) {

    $mode = (isset($_POST['static_post'])) ? 'static': 'db';

    $action = 'insert';

    $post_title = trim( strip_tags( $_POST['p_title'] ) );

    // Avoid trimming the contents of the textarea incase of any tabs intended for code blocks
    $post_content = strip_tags( $_POST['p_content'] );

    $post_description = trim( strip_tags( $_POST['p_description'] ) );

    $post_slug = trim( strip_tags( $_POST['p_slug'] ) );

    $post_categories = trim( strip_tags( $_POST['p_categories'] ) );

    if( $is_post ) {

        // $_GET['post'] holds the slug in the URL
        $get = Post::get_post_by_slug( trim( $_GET['post'] ) );

        $mode = ( $get[0]['type'] === 'static' ) ? 'static': 'db';

        $action = 'update';
    }

    if( isset( $_POST['convert_to_static_post'] ) ) {
        $action = 'convert_to_static_post';
        $mode = 'static';
    }

    else if( isset( $_POST['delete_post'] ) ) {
        $action = 'delete_post';
    }

    switch( $action ) {

        case 'insert':
            $insert = Admin::handle_post($mode, 'insert', array(
                'title' => $post_title,
                'post' => $post_content,
                'description' => $post_description,
                'author' => User::get_info('username'),
                'date' => date('Y-m-d G:i:s'),
                'slug' => $post_slug,
                'categories' => $post_categories
            ));

            if( !is_array( $insert ) ) {
                $messages[] = sprintf('<em>%s</em> has been successfully published! <a href="%s">View it now!</a>', $post_title, Link_Parser::generate_link('post', array('slug' => $post_slug)) );
            }

            else {
                $messages[] = array_merge($insert, $messages);
            }
        break;

        case 'convert_to_static_post':

            // The post will be removed from the database via its slug, so retrieve its slug from what is already stored as the slug passed from the form may be different.
            Query::run('DELETE FROM posts WHERE slug = :slug', array(':slug' => $_GET['post']));

            // Now convert the post to a static post.
            $convert = Admin::handle_post('static', 'insert', array(
                'id' => $id,
                'title' => $post_title,
                'post' => $post_content,
                'description' => $post_description,
                'author' => User::get_info('username'),
                'date' => date('Y-m-d G:i:s', $date),
                'slug' => $post_slug,
                'categories' => $post_categories
            ));

            if( !is_array($convert) ) {
                $messages[] = "<em>$post_title</em> has been converted to a static post!";

                // Change the type of the post to static now.
                $post_type = 'static';
            }

            else {
                $messages[] = array_merge($convert, $messages);
            }
        break;

        case 'update':

            // Send an update request
            $update = Admin::handle_post($mode, 'update', array(
                'id' => $id,
                'title' => $post_title,
                'post' => $post_content,
                'description' => $post_description,
                'author' => User::get_info('username'),
                'slug' => $post_slug,
                'categories' => $post_categories
            ));

            if( is_array($update) ) {
                $messages[] = array_merge($update, $messages);
            }

            else {
                $messages[] = "<em>$post_title</em> has been saved.";

                // If the slug is changed, once the editor loads again, an error message will be displayed, thus only redirect back to the editor if the slug is different from its previous form.
                if( $get[0]['slug'] !== $post_slug ) {

                    $_SESSION['update_rdirect'] = true;
                    header("Location: ?page=edit_post&post=$post_slug");
                    exit;
                }
            }
        break;

        case 'delete_post':

            $delete = Admin::handle_post($mode, 'delete', array(
                'id' => $id,
                'title' => $post_title,
                'post' => $post_content,
                'description' => $post_description,
                'author' => User::get_info('username'),
                'slug' => $post_slug,
                'categories' => $post_categories
            ));

            if( !is_array($delete) ) {
                $messages[] = "<em>$post_title</em> has been deleted."; 

                // Empty input variables to return a fresh new form
                $post_title = '';
                $post_content = '';

                $post_categories = '';
                $post_slug = '';

                $post_description = '';

                $post_type = '';
            }

            else {
                $messages[] = array_merge($delete, $messages);
            }
        break;
    }

    // Set the values of the text field's to whatever was submitted.
    if( isset($post_title, $post_content, $post_categories, $post_slug, $post_description, $mode) ) {
        $title = $post_title;
        $post = $post_content;

        $categories = $post_categories;
        $slug = $post_slug;
        $description = $post_description;

        $post_type = $mode;
    }

}

?>
<script>
    var page_type = '<?php echo $page_type;?>',
        post_type = '<?php echo isset($post_type) ? $post_type: '';?>',
        date = <?php if(!empty($date)): echo $date; else: ?>''<?php endif;?>,
        categories = '<?php echo isset($categories) ? $categories: '';?>',
        author = '<?php echo isset($author) ? $author: User::get_info('username');?>';
</script>
<div id="modal_preview">

    <h3>Preview of 
        <em>
            <span id="postname"></span>
        </em> &mdash; 

        <a href="#" class="standard" id="close_modal" title="Close the Preview">Close</a>
    </h3>

    <iframe src="about:blank" seamless></iframe>
</div>
<?php if( !empty($messages) ):?>
<div id="messages">
    <ul>
    <?php foreach($messages as $message): ?>

        <?php if( !is_array($message) ): ?>
        <li><?php echo $message;?></li>

    <?php else: foreach( $message as $_message ): ?>
        <li><?php echo $_message;?></li>
        <?php endforeach;?>

    <?php endif; endforeach;?>
    </ul>
</div>
<?php endif; ?>
<form method="POST" autocomplete="off">

    <input type="text" name="p_title" <?php echo ( isset($title) ) ? "value='$title'": '';?> placeholder="Your post's title." id="p_title" required>

    <textarea name="p_content" id="p_content" required contenteditable="true" placeholder="Your post's content. Markdown is supported." rows="20"><?php echo ( isset($post) ) ? $post: '';?></textarea>

    <div id="toolbox">
        <div class="container">
            <h2><?php echo ( $page_type === 'new' ) ? 'Add': 'Edit';?> Metadata &amp; <?php echo ( $page_type === 'new' ) ? 'Publish': 'Save';?> Your Post</h2>
            <p class="standard">Extend and save your post.</p>
            <br>

            <label for="categories" class="standard">Categories:</label><br>
            <input type="text" name="p_categories" id="categories" <?php echo ( isset($categories) ) ? "value='$categories'": '';?> placeholder="Separate by commas." required>

            <div id="catlist">
                <p>Your <abbr title="These are your categories that you've defined in settings.php">categories</abbr>: </p>
                <ul>

                <?php if( !is_null(Post::get_categories()) ): foreach(Post::get_categories() as $category): ?>
                    <li class="category" title="Click to add '<?php echo $category;?>'">
                        <a href="#"><?php echo $category;?></a>
                    </li>
                <?php endforeach; else: echo '<li class="category">You have not defined any categories!</li>'; endif;?>
                </ul>
            </div>
            <br>

            <label for="slug" class="standard">Slug:</label><br>

            <input type="text" name="p_slug" id="slug" maxlength="50" <?php echo ( isset($slug) ) ? "value='$slug'": '';?> placeholder="Separate-space-by-hyphen" required>
            <br>
            <br>

            <label for="p_description" class="standard">Description/Subtitle:</label><br>
            <input type="text" name="p_description" id="description" <?php echo (isset($description)) ? "value='$description'":'';?>>
            
            <br>
            <input type="submit" id="save" name="save" class="button">
        <?php if(Post::can_static_post()):?>
            <input type="submit" id="static" value="Static Post" class="button alternative">
        <?php endif; ?>
            <input type="button" id="preview" value="Preview Post" class="button alternative" title="View a preview of this post.">

        <?php if($is_post): ?>
            <input type="submit" value="Delete Post" id="delete" class="button emphasis">
        <?php endif; ?>
        </div>
    </div>
</form>
<script src="../content/includes/admin/js/add_posts.js"></script>
