# The file struct
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

# Pointers
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
