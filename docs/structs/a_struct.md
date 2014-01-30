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
With the exception of the post struct, when making use of the other two structs (_file_ and _URL_), you'll need to outline a struct in a way that's expressive. For instance, when making use of the file struct in `struct.bpd`, you'll need to define pointers to files in a block beginning and ending with `{- BEGIN FILES -}` and `{- END FILES -}`

## Meet the Structs
BlogPad uses 3 structs - the **_file struct_**, the **_URL struct_** and the **_post struct_**. More times than ever, you are more likely to make use of the post struct. If you're developing a theme, then you'll also make use of the file and URL struct's. 

### The file struct
The file struct is the struct that provides pointers to BlogPad for a certain theme. This ensures that theme developers aren't locked in naming conventions. However, for any theme, the file struct must be located in a file called `struct.bpd`. Here's an example struct:

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
