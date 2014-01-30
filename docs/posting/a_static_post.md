# Static Posts
A BlogPad static post is a file that stores a post's content. **That's it**. Static posts should have the extension **.bpp** and be stored in the directory specified as the static posts directory. 

This is the format of a static post:

```
-- BEGIN METADATA
Title: Post title

Date: Year-Month-Date Hour:Minute:Second

Author: username

Categories: A, comma, separated, list, of, values, if, required

Description: A description
-- END METADATA

The body of a post.
```

When you learn more about structs (_this is a post struct_), you'll find out that everything in BlogPad has a beginning and an ending.

You may have noticed that there is no way of providing a slug to your post. Since a static post is a file, the filename should contain the slug, separated by hyphens if required, like so: `my-post.bpp` or `post.bpp`. 

The web editor also provides you with the ability to create and edit static posts and it will handle this templating for you.
