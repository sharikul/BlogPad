# The URL struct
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

## Mammoth questioning
Noticed this line in the example URL struct?
```
{- PATTERN: category/%word%/%num%?/?, TEMPLATE: CATEGORY -}
```

It starts off fine, but then everything seems to get question marked out. Essentially, this enables visitors to visit a link like `category/category/` and view the content of the `CATEGORY` template delegated. Additionally, since the content of a category template will be affected by pagination, it enables visitors to view a wide range of posts spanning over pages, so visitors can visit a link like `category/category/2` and the posts shown will be altered for pagination.

The reason for question marking out the end characters is so that URL's for categories can still be generated without requiring a page number to be specified. So in events when a visitor should be directed to the categories template (_where they are able to view posts filtered out by a specific category_), a link like `http://base/category/category/` will be generated. In the event when a visitor should switch pages of the same link through a page number, a link such a `http://base/category/category/2` will be generated.

Because BlogPad generates link logically, you will not need to handle the generation of links of your own.

## Slug for posts, word for everything else
Because BlogPad enforces pretty URL's, it expects links that delegate the `POST` template to use the `%slug%` content tag, as posts should be accessed through slugs. However, for other templates, it expects `%word%` to be used, with `%num%` optionally thrown in.

Now, the paragraph above may sound confusing, so here's a table hoping to clear up confusions:

| Content tag | Usage | Correct? | Why? |
| ----------- | ----- | -------- | ---- |
| `%slug%` | `{- PATTERN: p/%slug%/, TEMPLATE: POST -}` | Yes | An individual post must be accessed through its slug. |
| `%word%` | `{- PATTERN: p/%word%/, TEMPLATE: POST -}` | No | Though it _can_ possibly work, BlogPad won't be able to generate a link to a post successfully as it expects to replace the `%slug%` content tag with the slug of a post. Additionally, since spaces in slugs are to be separated by hyphens, the `%word%` content tag doesn't take that into consideration. |
| `%word%` | `{- PATTERN: category/%word%/, TEMPLATE: CATEGORY -}` | Yes | Categories shouldn't really contain symbols, and therefore BlogPad expects categories to be words, with the occasional space if required. |
| `%num%` | `{- PATTERN: category/%word%/%num%?/?, TEMPLATE: CATEGORY -}` | Yes | `%num%` is being optionalised through question marking and therefore BlogPad knows that the pattern **_can_** contain a number at the end, but it's not always going to be required. |
| `%num%` | `{- PATTERN: post/%num%/, TEMPLATE: POST -}` | No | Posts cannot be accessed through a number, i.e. their ID. |
| `%num%` | `{- PATTERN: post/%slug%/%num%/, TEMPLATE: POST -}` | No | The use of the `%num%` content tag has no purpose here. Additionally, since the content tag was optionalised, BlogPad won't be able to generate links successfully. |

## Word before numbers, or just slug
BlogPad uses baked in prewritten query strings that are used to power the application. Here is the implementations, in PHP:

```php
$params = array(
  'CATEGORY' => 'word=$1&pagenum=$2',
  'POST' => '_post=$1',
  'HOMEPAGE' => 'pagenum=$1',
  'PROFILE' => 'username=$1&pagenum=$2',
  'SEARCH' => 'query=$1&pagenum=$2'
);
``` 

As you can see, the query string of templates which can take page numbers make `pagenum` the second parameter passed. This means that in your URL's, `%word%`'s must come **before** `%num%`, if any. 

```
{- PATTERN: category/%word%/%num%?/?, TEMPLATE: CATEGORY -}
```

Is correct.

```
{- PATTERN: category-is-%word%-?a?n?d?-?-?p?a?g?e?-?i?s?-?%num%?/?, TEMPLATE: CATEGORY -} // please don't create links like this :)
```

Is also correct.

```
{- PATTERN: category/%num%/%word%, TEMPLATE: CATEGORY -}
```

Is not correct.