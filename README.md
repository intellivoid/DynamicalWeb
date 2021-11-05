# DynamicalWeb
Written by Zi Xing Narrakas (Intellivoid Developer) & Contributed by Kasper Medvedkov 
(Intellivoid System Administrator) 

DynamicalWeb is a Web Application Framework written in PHP 7.+  for Apache & Nginx Web 
Servers, this framework is designed for creating and hosting Web Applications. As of
version 3.* DynamicalWeb is designed to work with PPM, if you are looking for the legacy
version without PPM support (only partial) see the
[`legacy`](https://github.com/intellivoid/DynamicalWeb/tree/legacy) branch, the legacy
version and the PPM version are not compatible at all, while DynamicalWeb is designed
with the same idea and same structure, the codebase and functionality is entirely
different and will not work with legacy versions. 

The goal of this new version of DynamicalWeb is to simply pack the distribution of a web
application into a redistributable binary file which can allow for much easier deployments
by simply using a bootstrap script to load in your web application, for example;

```php
<?php
    require('ppm'); // Require PPM Runtime
    import('com.example.web_application'); // Import the web application, that's all!
```


## File Types

Due to how PPM is designed, anything with a `.php` extension will be compiled, this
can cause issues when importing the web application as a package since most files
in a DynamicalWeb project are not classes but simple procedural scripts. So PPM cannot
build a autoloader for these types of files and instead imports them right away which
causes unwanted code to execute. A way around this issue is to avoid compiling these
components, so a `.dyn` extension is used in replacement of a `.php` extension. And for
compiled assets such as `.css` and `.js` it will be `.css.dyn` and `.js.dyn`

This causes the final binary output to be more bloated due to .dyn files not being
compiled, but in the future this could be fixed with custom compiler extensions. But right
now this is not considered to be a big issue bot performance or disk size.


## Web Application Structure

Below is a tree view of an example


```
web_app
|-- assets
|   |-- css
|   |   `-- file.txt
|   `-- images
|       `-- me-weeb-shit-nya-3-39415496.png
|-- configuration.json
|-- localization
|   |-- en.json
|   `-- zh.json
|-- markdown
|   `-- lorem.md.dyn
|-- package.json
|-- pages
|   |-- debug
|   |   `-- contents.dyn
|   `-- index
|       |-- contents.dyn
|       `-- sections
|           `-- header.dyn
`-- sections
    `-- copyright.dyn
``