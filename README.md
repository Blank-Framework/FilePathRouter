# FilePathRouter

The Blank Framework FilePathRouter is an efficient and straightforward router library designed for routing HTTP requests
in PHP applications. It adeptly maps a requested path, such as `/blog/posts`, to its corresponding directory and file,
e.g., `/blog/posts/index.php`.

## Usage

To utilise this library, instantiate the `FilePathRouter` class and pass the **absolute** path to the directory that
contains your routes. Here's an example that assumes your `index.php` is located in the same directory as this README:

```php
$router = new FilePathRouter(__DIR__ . '/routes');
```

## Directory Structure and Routing

FilePathRouter's routing mechanism is dependent on your project's directory structure. The home route (/) is mapped to
the index.php file in the /routes directory. To create a new path, such as /blog, simply create a directory named blog
within /routes. Place an index.php file in this new directory, and this file will be executed whenever the /blog path is
requested.

## Exceptions

FilePathRouter may throw two types of exceptions:

- RoutesPathNotFoundException: Thrown if the routes directory specified in the constructor does not exist.
- RouteNotFoundException: Thrown if the requested route does not exist.

These exceptions are instrumental in diagnosing issues related to nonexistent route paths or directories.

## Getting Started

To integrate FilePathRouter into your project, include this library in your codebase and follow the usage guidelines to
set up your routing. Ensure your directory structure is congruent with your routing paths for optimal functionality.