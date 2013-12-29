## What is BlogPad?
BlogPad is a simple blogging toolkit and is powered by a base that is a hybrid between a flat-file system and database system. 

## Is BlogPad a CMS?
No. I hate the idea of a fully fledged CMS with blogging software. BlogPad is merely a blogging toolkit. That's it.

## What is BlogPad written in?
BlogPad is written in PHP.

## Is BlogPad similar to WordPress?
That depends on how you look at it. BlogPad requires you to produce themes via its own templating engine, called _BlogPad_, which does not need you to provide loops of any kind.

## How would BlogPad manage preferences?
Any preferences would need to be set mainly in the files that come bundled with BlogPad. The database is only used to store posts written via the web based editor.

## How can I develop themes for BlogPad?
BlogPad requires themes to be made up to .bp files, and defined in a _struct_. A _struct_ is nothing more than a definition file that explains the structure of files to the BlogPad Parser.

## Can I see the difference between writing WordPress and BlogPad code?
Of course.

Here is how you'd fetch posts in a typical WordPress theme:
```php
<?php while( have_posts() ): the_post(); ?>
  <article>
    <h1><?php the_title();?></h1>
    <p><?php the_content();?></p>
  </article>
<?php endwhile; ?>
```

And here's how you'd fetch posts in a BlogPad theme:
```
{- BEGIN POSTS -}
  <article>
    <h1>{- title -}</h1>
    <p>{- post -}</p>
  </article>
{- END POSTS -}
```

'POSTS' denotes a plural set of posts so it would work the same way as the WordPress while loop to fetch a range of posts. BlogPad has another structure with 'POST' which would only be used on pages that need to fetch one post, such as the posts page.

# More documentation coming soon!
