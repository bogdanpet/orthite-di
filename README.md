# orthite-di
Simple dependency injection container

## Instalation

```
composer require bogdanpet/orthite-di
```

## Usage

### Automatic resolving of classes
For example let's say we have model User, which depends on Request and Database objects, and the Database class depends on PDO.

```php
<?php

class User {

    public function __construct(Request $request, Database $db) {
        .
        .
        .
    }
}
```

```php
<?php

class Database {

    public function __construct(\PDO $pdo) {
        .
        .
        .
    }
}
```

```php
<?php

class Request {
        .
        .
        .
}
```

Without DI creating $user object will look like this.

```php
$request = new Request();
$pdo = new PDO($dsn, $username, $passwd);
$db = new Database($pdo);
$user = new User($request, $db);
```

Now imagine if there were more dependendcies with more dependencies. This is where auto resolving comes to aid. All you need is to call Container's get() method. If the object is not in the definitions container will try to resolve it using [type hinting](http://php.net/manual/en/functions.arguments.php#functions.arguments.type-declaration).

```php
$container = Orthite\DI\Container::getInstance();

$user = $container->get(User::class);
```

Container will recursively try to resolve all the classes. Problem with this one is that PDO depends on dsn, user and password input which cannot be resolved. So some user defined parameters can still be passed.
Pass the array of parameters and Container will know where to put them. Keep in mind that array keys must match the parameter names.

```php
$user = $container->get(User::class, [
    'dsn' => $dsn,
    'username' => $username,
    'passwd' => $passwd
]);
```
or
```php
$user = $container->get(User::class, compact('dsn', 'username', 'passwd');
```
Assuming that you have variables $dsn, $username and $passwd defined.

Package also provide container() helper function if you prefer not to instantiate Container singleton. It will be done automatically inside the function.

```php
// No previous instantiation of container is required.
$user = container(User::class, compact('dsn', 'username', 'passwd');
```

### Invoking a method from a class
Same way you resolve classes, methods from classes can also be resolved. For example, let's say class User has a method show which displays user account details, and it depends on user's id and Response class.

```php
<?php

class User {

    public function __construct(Request $request, Database $db) {
        .
        .
        .
    }
    
    public function show(Response $response, $id) {
        .
        .
        .
        return $response(...$something);
    }
}
```

Container's method call() can be used to invoke the method and container will take care of resolving both class and method dependencies.

```php
$container = Orthite\DI\Container::getInstance();
$response = $container->call(User::class, 'show', ['id' => 1]);
echo $response; // or whatever
```
or with helper function cintainer_call()
```php
$response = container_call(User::class, 'show', ['id' => 1]);
echo $response; // or whatever
```
