# FilePathRouter

FilePathRouter is a simple routing package that maps a path such as `/blog/posts` to the directory `/blog/posts`. If
that directory exists, it executes the `index.php` file inside.

## Usage

To start, instantiate the `FilePathRouter` class and pass the absolute path to the directory where your routes will be
located. If it is in the root of your project directory is could be like the code snippet below:

```php
$router = new FilePathRouter(__DIR__ . '/routes');
```

To route your requests, get the requested path for example /blog and pass it into the function routeRequest as shown below:

```php
$router->routeRequest('/blog');
```

## Directory Structure and Routing

FilePathRouter's routing mechanism is dependent on your project's directory structure. The home route (/) is mapped to
the index.php file in the /routes directory. To create a new path, such as /blog, simply create a directory named blog
within /routes. Place an index.php file in this new directory, and this file will be executed whenever the /blog path is
requested.

## Exceptions

If the routes path you provided in the constructor does not exist, the router will throw a `RoutesPathNotFoundException`.
If the requested route could not be found, the router will throw a `RouteNotFoundException`.

## Getting Started

To integrate FilePathRouter into your project, include this library in your codebase and follow the usage guidelines to
set up your routing. Ensure your directory structure is congruent with your routing paths for optimal functionality.