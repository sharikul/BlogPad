<title><?php echo isset($title) ? $title: 'BlogPad Admin'; ?></title>
</head>
<body>
    <header>
        <ul>
            <li>
                <h4>Logged in as <strong>Sharikul Islam</strong></h4>
            </li>
        </ul>
    </header>
    <div id="actions">
    <ul>
        <a href="?page=view_posts" title="View your blog's static and dynamic posts.">
            <li>
                <h3>View Posts</h3>
                <span class="icon-file"></span>
            </li>
        </a>
    
        <a href="?page=add_posts" title="Add a new Blog Post.">
            <li>
                <h3>Add a Post</h3>
                <span class="icon-pen"></span>
            </li>
        </a>
    </ul>
</div>