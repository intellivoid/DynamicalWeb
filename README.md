# DynamicalWeb
Written by Zi Xing Narrakas (Intellivoid Developer)

DynamicalWeb is a Web Application Handler written in PHP 7.+
for Apache Web Servers, this Library is designed for
creating and hosting Web Applications

## How it works
DynamicalWeb is a "Framework" written in PHP for PHP, it auto-defines
parts of your web application making it easy to create pages which
displays dynamic information without having to create complicated
source codes which repeats a lot of the functionality which has already
been established.

In the source code (`/src` directory) you will see two directories
and two files.

`/src/assets` are public assets which are retrievable via HTTP Requests.
This directory should not contain any php files or any hidden source
files. This directory typically contains js, css and images.

`/src/resources` are internal assets which are not retrievable via
HTTP Requests but rather internal functions. This documentation
will go into details what each file & directory does.

`/src/.htaccess` is a configuration file for apache web server, this
basically routes all "404" requests to `/src/index.php` so it 
can properly display the pages you have in your web application.
For example visiting will `http://localhost/example_page` route
the request internally to index.php, therefore the actual resulting
url becomes `http://localhost/index.php?c_view_point=example_page`
but the user will never see this nor know this. One of the ideas
behind DynamicalWeb is that it hides the fact that your web
application is written in PHP, it will not expose any .php file
extensions in the url or request headers.

## Resources Directory

`/src/resources/DynamicalWeb` is the core library for DynamicalWeb,
which auto-defines some variables, loads scripts, routes pages, etc.
It's not meant to be altered. Just know that this is the library
that drives this whole framework.

`/src/resources/languages` are language files if you plan to add
multi-languages to your web application, this directory only contains
.json files that are named by ISO 639-1 codes for the language
(eg; en, cn, kr, es, ...) to switch between languages you simply
make a `GET` request to any page within this web application
eg; `http://localhost/index` with `set_language` as one of the
parameters followed by a value which is the file name of the language
file. For example, to change to chinese your request would be
`http://localhost/index?set_language=zh`, this will attempt to load
`/src/resources/languages/zh.json`, if it fails it will attempt to load
the primary language. There is no response code or errors if the
language file doesn't exist or it cannot be loaded. The web application
will try to correct itself if it cannot find a particular resource.

`/src/reesources/libraries` Is a directory that would contain all the
PHP Libraries that your web application requires to function correctly.
Each library is configured manually before it can be used properly.

`/src/resources/markdown` Is a directory that contains markdown files
that you can import in pages which will convert into compatible 
HTML code once imported. This is designed to work with multi-languages
so each markdown file contains it's own directory that contains the
actual markdown file which like the language files in the language
directory is named after ISO 639-1 codes for the language. If
no particular resource for that language is available it will
load the markdown file that represents the primary language. For example
the markdown file called `docs` would have it's own directory in
`/src/resources/markdown/docs` which contains `en.md`, `es.md` and
`zh.md`, if the current language is set to `zh` it will
attempt to load `/src/resources/markdown/docs/zh.md` if it cannot
be found it will attempt to load the file that represents the primary
language for your web application `en` which would be
`/src/resources/markdown/docs/en.md` an exception will be thrown if no 
such directory or file exists.

`/src/resources/pages` Contains all the pages that are available in
your web application which are represented in directories for example
`http://localhost/index` would load
`/src/resources/pages/index/contents.php`, print out the HTML code
that you have in that file and also execute any php code that is
in that file.