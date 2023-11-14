# FilePathRouter
The Blank Framework FilePathRouter is an extremely simple router library for routing requests. Essentially all it does
is take a path like /blog/posts and routes it to a directory like /blog/posts/index.php.

To use it you need to simply instantiate the class and pass the **full** path into the constructor to the directory
where your routes will be. An example as from the this project might be assuming your index.php is where the README is

```php
$router = new FilePathRouter(__DIR__ . '/routes');
```

This will map your routes directory to the directory `/routes`. That is it really! After that you simply need to find
the route by calling the following code and providing the requested URL path like this:

```php
$router->routeRequest('/blog');
```
Paths are then done through folders. Home is routed to the `index.php` in the `/routes` directory. To create the path 
`/blog` create a directory `blog` inside `/routes` and inside the `blog` directory add an `index.php` file. This file
will be executed whenever the path `/blog` is requested.

##  Exceptions
If the routes path you provided in the constructor does not exist, the router will throw a `RoutesPathNotFoundException`.
If the requested route could not be found, the router will throw a `RouteNotFoundException`.
