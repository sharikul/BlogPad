# System Requirements
BlogPad was written on PHP 5.3 and therefore compatibility with this version and higher is guaranteed. However, the source code doesn't make use of features that have been newly introduced from 5.3 and therefore BlogPad _should_ be compatible with any version of PHP starting from 5.1.

## Database
Since BlogPad is a toolkit supporting the creation of static (file based) and dynamic (database-based) posts, you are not required to supply database credentials if you don't intend to store posts into the database. However, if you do, do make sure that the database you plan to use for your BlogPad blog is a MySQL database.

Additionally, using a MySQL GUI provided by your web-server (_phpMyAdmin_ is a great example), import the contents of the provided _posts.sql_ file. This should create a table called `posts` and this will be used by BlogPad to store posts. 

## Permissions
If you intend to also (or on its own) store posts statically, you must ensure that your web-server account has permission to be able to modify files (that is to be able to create, edit, and delete files). 