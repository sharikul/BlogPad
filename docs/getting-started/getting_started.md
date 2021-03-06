# Getting Started
**You should follow the instructions or tips provided in this help file after following the instructions of system_requirements.md.**

## settings.php
_settings.php_ is the file which is home to the wide variety of settings that affect your BlogPad blog. 

Here, you'll gain an understanding of the keys found in this file and their significance to your blog:

* `base` - This key stores the full path to the directory that BlogPad is presently in.

* `using` - This key stores the name of the current theme that should be applied to your blog. The name of a theme is the name of the directory that is placed in within `contents/themes`. So if you're using the `default` theme, ensure that a directory titled `default` is present in `content/themes`.

* `database` - This is an optional key and you can remove it should you not need it. It should store an array containing four sub-keys, which are `host`, `username`, `password`, and `database`. Keys are self explanatory.

* `auto_link` - This key should store a boolean denoting your intention on whether links should be automatically be added in areas that are appropriate, e.g. to the title of blog posts.

* `categories` - This key should store an array containing sub-arrays that are presented in the `category => description` format. These categories can then be used to link to posts.

* `static_posts_dir` - Likewise to `database`, this is an optional key and it should store the path to the directory that should contains static posts. 

* `no_post_message` - This key should store a message that will be displayed whenever posts cannot be shown. If you try to visit your blog's homepage when you haven't added any posts, you'll see this message. **You can also insert HTML tags to customize the appearance of this message**.

* `post_sort_type` - This key should only hold the values `DESC` or `ASC` which stand for _descending_ and _ascending_, respectively, and these signify the order in which posts should appear in. When set to `DESC`, posts will begin to show starting from the most recently added post. 

* `titles` - This key should store an array that describes the page title of a template. The format that you should follow is: `TEMPLATE => 'Title'`. There are several placeholders you can use within the title string which are wrapped around percentage symbols. The placeholders are as follows:

  * `%blogname%`: The name of your blog will replace this placeholder.
  * `%blogdescription%`: The description of your blog (defined in settings.php) will replace this placeholder.
  * `%posttitle`: The title of the current blog post will replace this placeholder.
  * `%category`: **You should only use this placeholder in your CATEGORY template**. The category being accessed will replace this placeholder.
  * `%searchquery%`: The text of the search query will replace this placeholder.
  * `%username%`: **You should only use this placeholder in your PROFILE template**. The username whose profile is being accessed will replace this placeholder.

* `accounts` - This key should hold a sub-array containing the accounts with their respective credentials. This is the format that you should follow: `username => array('firstname' => 'firstname', 'lastname' => 'lastname', 'username' => 'username', 'password' => 'password')` **Please make use of `crypt_gen.php` to generate a crypted password and this crypted password should be the value of the password key in an account array**.

## Routing
Each theme has the option of changing the presentation of links. In order for this to work, through a _.htaccess_ file, you must direct all requests to `index.php`. If BlogPad is stored in a directory on your web-server, this is what a `.htaccess` file should look for you:

```
RewriteEngine On
RewriteBase /name-of-directory/

RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule . /name-of-directory/index.php
```

Specifying the directory name as the rewrite base ensures that other content on your web-server aren't affected by BlogPad.

### Slugging it out
The reason why BlogPad features routing even though it's not a CMS is because BlogPad enforces the pretty presentation of URL's, built up of slugs and full words - not query strings! 

## Logging in
After you've created an account (_or accounts_), you can now login to the supplied admin dashboard by pointing your web browser towards `/admin/login.php`. Once logged in, you'll be directed to the web editor, which looks like this:

![Add Post](http://i.imgur.com/G8P2zoS.png)

Since the aim of BlogPad is to be a _**very**_ minimalistic application, the editor has also been designed to replicate that. You also have the ability to format your post's body using Markdown. 
