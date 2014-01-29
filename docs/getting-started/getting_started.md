# Getting Started
**You should follow the instructions or tips provided in this help file after following the instructions of _system_requirements.md_.**

_settings.php_ is the file which is home to the wide variety of settings that impacts your BlogPad blog. 

Here, you'll gain an understanding of the keys found in this file and their significance to your blog:

* `base` - This key stores the full path to the directory that BlogPad is presently in.

* `using` - This key stores the name of the current theme that should be applied to your blog. The name of a theme is the name of the directory that is placed in within `contents/themes`. So if you're using the `default` theme, ensure that a directory titled `default` is present in `content/themes`.

* `database` - This is an optional key and you can remove it should you not need it. It should store an array containing four sub-keys, which are `host`, `username`, `password`, and `database`. Keys are self explanatory.

* `auto_link` - This key should store a boolean denoting your intention on whether links should be automatically be added in areas that are appropriate, e.g. to the title of blog posts.

* `categories` - This key should store an array containing sub-arrays that are presented in the `category => description` format. These categories can then be used to link to posts.

* `static_posts_dir` - Likewise to `database`, this is an optional key and it should store the path to the directory which should hold static posts. 

* `no_post_message` - This key should store a message that will be displayed whenever posts cannot be shown. If you try to visit your blog's homepage when you haven't added any posts, you'll see this message. **You can also insert HTML tags to customize the appearance of this message**.

* `post_sort_type` - This key should only hold the values `DESC` or `ASC` which stand for _descending_ and _ascending_, respectively, and these signify the order at which posts should appear in. When set to `DESC`, posts will begin to show starting from the most recently added post. 

* `accounts` - This key should hold a sub-array containing the accounts with their respective credentials. This is the format that you should follow: `username => array('firstname' => 'firstname', 'lastname' => 'lastname', 'username' => 'username', 'password' => 'password')`. **Please make use of `crypt_gen.php` to generate a crypted password and this crypted password should be the value of the password key in an account array**.