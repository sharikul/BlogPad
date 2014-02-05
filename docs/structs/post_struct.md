# The post struct
The post struct is the struct that you're more often going to find yourself using when developing themes. A post struct can look like this:

```
{- BEGIN POSTS -}
    <h1>{- title -}</h1>
    <h3>{- description -}</h3>

    <div id="body">
        {- post -}
    </div>

    <p>Posted in
        <ul>
            <li class="categories">{- categories -}</li>
        </ul>
{- END POSTS -}
```

Or this:

```
{- BEGIN POST -}
    <h1>{- title -}</h1>
    <h3>{- description -}</h3>

    <div id="body">
        {- post -}
    </div>

    <p>Posted in
        <ul>
            <li class="categories">{- categories -}</li>
        </ul>
{- END POST -}
```

They both work effectively in the same manner. The key to remembering which format to use and when is by understanding the purpose of each of the formats.

## `{- BEGIN POSTS -}`
`POSTS` is a plural term, and therefore denotes a wide number of something, posts in BlogPad's case. Therefore you should use this format in templates that are intended to display any number of blog posts, such as the `HOMEPAGE` and `CATEGORY` templates.

## `{- BEGIN POST -}`
`POST` is a singular term, and therefore denotes a single number of something, posts in BlogPad's case. Thus, you should use this format in the `POST` template. 

## Don't forget to end your formats
Upon reading `a_struct.md`, you'll understand that in BlogPad, everything has a beginning and an ending. The same principle applies to the post struct. Without wrapping a post block in `{- BEGIN POST[S] -}` and `{- END POST[S] -}`, BlogPad won't parse your templates, thus leaving blogs in ruins.