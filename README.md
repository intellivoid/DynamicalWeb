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