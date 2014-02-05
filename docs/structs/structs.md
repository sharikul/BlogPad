# Structs
A 'struct' is a BlogPad terminology meaning _structure_. A struct organises sections of your blog based on its wider functionality. 

## The different extensions
In a theme, different files use unique extensions. In this table, you can see the different extensions, when they should be used and some further information.

| Extension | When to be used | Further information |
| --------- | --------------- | ------------------- |
| `.bpd`    | When indicating struct files. such as the URL and file structs. | Stands for _BlogPad Definitions_.         |
| `.bp`     | When indicating a template file, such as the homepage. | Stands for _BlogPad_. |
| `.bpp`    | To indicate a BlogPad **static post**. | Stands for _BlogPad Post_. |

## Everything begins and ends
With the exception of the post struct, when making use of the other two structs (_file_ and _URL_), you'll need to outline a struct in a way that's expressive. For instance, when making use of the file struct in `struct.bpd`, you'll need to define pointers to files in a block beginning and ending with `{- BEGIN FILES -}` and `{- END FILES -}`.

The table below lists the different block opening/closing tags with their structs.

| Opening tag | Closing tag | Struct | Struct extension |
| ----------- | ----------- | ------ | ---------------- |
| `{- BEGIN FILES -}` | `{- END FILES -}` | File struct | `.bpd` |
| `{- BEGIN URLS -}` | `{- END URLS -}` | URL struct | `.bpd` |
| `{- BEGIN POSTS -}` | `{- END POSTS -}` | Post struct (to display multiple posts, e.g. on the homepage) | `.bp` |
| `{- BEGIN POST -}` | `{- END POST -}` | Post struct (to display a single post, e.g. on the post page) | `.bp` |

## Meet the Structs
BlogPad uses 3 structs - the **_file struct_**, the **_URL struct_** and the **_post struct_**. More times than ever, you are more likely to make use of the post struct. If you're developing a theme, then you'll also make use of the file and URL struct's. 

### The file struct
The file struct is the struct that provides pointers to BlogPad for a theme. This ensures that theme developers aren't locked in naming conventions. However, for any theme, the file struct must be located in a file called `struct.bpd`. Here's an example struct:

```
{- BEGIN FILES -}
  {- HOMEPAGE: index -}
  {- STYLESHEET: css/style -}
  {- POST: posts/post -}
  {- CATEGORY: posts/category -}
  {- PROFILE: user_profile -}
  {- URL_STRUCT: url_struct -}
{- END FILES -}
```

With the exception of `STYLESHEET` and `URL_STRUCT`, which will have the `.css` and `.bpd` extensions attached to them respectively, every other pointer must link to a file with a `.bp` file extension.

Just incase you're wondering, here is the file structure for the demo theme whose file struct was shown above:

```
- theme
  - css/
    - style.css
  - index.bp
  - posts/
    - category.bp
    - post.bp
  - struct.bpd
  - url_struct.bpd
  - user_profile.bp
```

#### Pointers
A BlogPad theme is powered by pointers. A pointer is a description of a template. Most of the available pointers that you can define in a file struct have been shown in the example above.

There are a few required pointers that you must define in a file struct for themes. These are:

| Pointer | Purpose |
| ------- | ------- |
| `HOMEPAGE` | Points to the homepage template of a theme. The content of this template will be the first thing visitors to a visitors to your blog will see. |
| `STYLESHEET` | Points to the main stylesheet of a theme. This enables you (_as a theme developer_) to use the `{- VAR stylesheet -}` tag in your theme which points to the theme stylesheet. Even if you won't be styling a theme with a stylesheet, it must still be defined. |
| `POST` | Points to the template to display individual posts and their contents. |
| `CATEGORY` | Points to the template that will be used to display posts organised by a certain category. |
| `URL_STRUCT` | Points to the URL struct of a theme. The linked struct must have the `.bpd` file extension. |

There are a few more pointers that you can define in a file struct but aren't absolutely required. These are:

| Pointer | Purpose |
| ------- | ------- |
| `PROFILE` | Links to the template that will be used to display a profile of an individual author along with their posts. |
| `ERROR` | Links to the error template of a theme which will be brought in, in the event when a usual template cannot be used. |
| `SEARCH` | Links to the template that will be used to display search results. |

### The URL struct
The URL struct is the struct that defines the appearance of URL's associated with a blog. Linking to the file struct example above, let's say that this example URL struct is within a file called `url_struct.bpd`.

```
{- URL STRUCT -}

  {- BEGIN URLS -}
    {- PATTERN: page/%num%/, TEMPLATE: HOMEPAGE -}
    {- PATTERN: post/%slug%/, TEMPLATE: POST -}
    {- PATTERN: category/%word%/%num%?/?, TEMPLATE: CATEGORY -}
    {- PATTERN: user/%word%/, TEMPLATE: PROFILE -}
  {- END URLS -}
```

As with other structs, a URL struct must have a beginning and an ending, in this case the beginning and ending of URL's. Within this block, you must also provide more tags with two special pointers: `PATTERN` and `TEMPLATE`. 

`PATTERN` must link to the representation of a URL, and `TEMPLATE` must link to a pointer defined in `struct.bpd`. When a URL matches a pattern defined here, the associate template will be delegated the responsibility of displaying data based on that URL. 

In patterns, you can make use of _content tags_ (_those things that start and end with a percentage symbol_). Currently, there are three content tags that you can use. Each content tag is converted into a regular expression at runtime. In patterns, you must make use of these tags to ensure that URL's can be properly created.

| Tag | Regular Expression |
| --- | ------------------ |
| `%word%` | `[A-z%0-9\_\s]+` |
| `%slug%` | `[\w\-]+` |
| `%num%` | `[0-9]+` |
