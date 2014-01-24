<script>
    var base = '<?php echo $_SERVER['REQUEST_URI'];?>';
</script>
<?php if($have_posts):?>

<section id="filter_form">
    <input type="text" id="filter_by" placeholder="Filter through posts by title" autocomplete="off" >
    <input type="button" value="Filter" class="button" id="filter" title="Apply the filter to your posts">

<?php if(isset($_GET['filter_by']) && !empty($_GET['filter_by'])): ?>
    <br>
    <br>
    <a href="#" class="standard" id="remove_filter">Remove filter: <?php echo trim($_GET['filter_by']);?></a>
<?php endif; ?>

</section>

<?php endif;?>

<section id="posts">
    <?php if(!empty($posts)): foreach($posts as $post): ?>
    <article>
        <h2>
            <a href="?page=edit_post&post=<?php echo $post['slug'];?>" title="Edit <?php echo $post['title'];?>">
                <?php echo $post['title'];?>
            </a>
        </h2>

        <ul>
            <li>
                 <p>
            <time datetime="<?php echo date('d-m-Y', $post['date']);?>"><?php echo date('jS \of F, Y', $post['date']);?>.</time>
                </p>
            </li>

            <li>
                <?php echo ( Post::is_static_post($post['slug']) ) ? '<p title="This is a static post."><strong>Static</strong></p>': '<p title="This is a dynamic post."><strong>Dynamic</strong></p>';?>
            </li>

            <li>
                <p>
                    <a target="_blank" href="<?php echo Link_Parser::generate_link('POST', array('slug' => $post['slug']));?>">View Post</a>
                </p>
            </li>
        </ul>
       
    </article>
    <?php endforeach; else:?>
        <h1 class="standard"><?php echo $message;?></h1>
    <?php endif; ?>
</section>

<?php if( $have_posts && ($show_next_page || $show_prev_page) ): if($show_next_page && $current_page === 1):?>
<div id="paginator">
    <a href="<?php echo Link_Parser::current_url('pagenum', $current_page + 1);?>">
        <p class="button">Next page &raquo;</p>
    </a>
</div>
<?php elseif($show_next_page && $current_page > 1): ?>
<div id="paginator">
    <a href="<?php echo Link_Parser::current_url('pagenum', $current_page - 1);?>">
        <p class="button">&laquo; Previous page</p>
    </a>
    <br>
    <a href="<?php echo Link_Parser::current_url('pagenum', $current_page + 1); ?>">
        <p class="button">Next page &raquo;</p>
    </a>
</div>
<?php elseif( !$show_next_page && $show_prev_page ): ?>
<div id="paginator">
    <a href="<?php echo Link_Parser::current_url('pagenum', $current_page - 1);?>">
        <p class="button">&laquo; Previous page</p>
    </a>
</div>
<?php endif; endif;  ?>
<script src="../content/includes/admin/js/view_posts.js"></script>