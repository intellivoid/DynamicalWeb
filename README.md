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


## Runtime Definitions

DynamicalWeb will create various definitions once initialized, here is a table of
definitions your Application can access during runtime.

| Definition Name                    | Description                                                                                                                                           |
|------------------------------------|-------------------------------------------------------------------------------------------------------------------------------------------------------|
| `DYNAMICAL_INITIALIZED`            | Indicates that DynamicalWeb has been initialized, this will prevent DynamicalWeb from initializing again and allow static functions to work correctly |
| `DYNAMICAL_FRAMEWORK_VERSION`      | The version of the DynamicalWeb Framework build                                                                                                       |
| `DYNAMICAL_FRAMEWORK_AUTHOR`       | The author of the DynamicalWeb Framework build                                                                                                        |
| `DYNAMICAL_FRAMEWORK_ORGANIZATION` | The organization that manages the DynamicalWeb Framework build                                                                                        |
| `DYNAMICAL_APP_RESOURCES_PATH`     | The path for the resources directory that your Web Application is housed in                                                                           |
| `DYNAMICAL_APP_CONFIGURATION_PATH` | The path for the main configuration file that your Web Application is defined in                                                                      |
| `DYNAMICAL_APP_NAME`               | The name of your initialized web application                                                                                                          |
| `DYNAMICAL_CLIENT_IP_ADDRESS`      | The IP Address of the client that's making the request                                                                                                |
| `DYNAMICAL_CLIENT_USER_AGENT`      | The user agent of the client that's making the request                                                                                                |
| `DYNAMICAL_CLIENT_OS_FAMILY`       | The operating system family of the client that's making the request                                                                                   |
| `DYNAMICAL_CLIENT_OS_VERSION`      | The operating system version of the client that's making the request                                                                                  |
| `DYNAMICAL_CLIENT_DEVICE_FAMILY`   | The device family of the client that's making the request                                                                                             |
| `DYNAMICAL_CLIENT_DEVICE_BRAND`    | The brand of the client's device that's making the request                                                                                            |
| `DYNAMICAL_CLIENT_DEVICE_MODEL`    | The model of the client's device that's making the request                                                                                            |
| `DYNAMICAL_CLIENT_FAMILY`          | The client's browser family that's making the request                                                                                                 |
| `DYNAMICAL_CLIENT_VERSION`         | The client's browser version that's making the request                                                                                                |