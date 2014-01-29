<title><?php echo isset($title) ? $title: 'BlogPad Admin'; ?></title>
</head>
<body>
    <header>
        <ul>
            <li>
                <h4>Logged in as 
                    <strong>
                    <?php echo User::get_info('fullname');?> (<?php echo User::get_info('username');?>)
                    </strong>.

                    <a href="<?php echo BlogPad::get_blog_homepage();?>/admin/logout.php" style="color: azure;">Logout?</a>
                </h4>
            </li>
        </ul>
    </header>
    <div id="actions">
    <ul>
        <a href="?page=view_posts" title="View your blog's static and dynamic posts">
            <li>
                <h3>View Posts</h3>
                <span class="icon-note"></span>
            </li>
        </a>
    
        <a href="?page=add_posts" title="Add a new Blog Post">
            <li>
                <h3>Add a Post</h3>
                <span class="icon-pen"></span>
            </li>
        </a>

        <a href="<?php echo BlogPad::get_blog_homepage();?>" title="Visit your blog">
            <li>
                <h3>Visit Blog</h3>
                <span class="icon-shop"></span>
            </li>
        </a>
    </ul>
</div>