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

Each struct is explained in-depth in its own help file within this directory.