# php-di-container

A PHP Dependency injection container based on JSON configuration.

## Sample

Create container.json file

- It it possible to pass multiple json files. For instance, you can create a separated file containing only the "parameters" section.
- The files must have the section "parameters" or "services" and the example above.

```javascript
{
  "parameters" : [
    {
      "environment" : "prod"
    },
    {
      "database": {
        "username": "user",
        "password": "secret",
        "host": "localhost",
        "port": "3306"
      }
    },
    {
      "valid-ips" : [
        "127.0.0.1",
        "127.0.0.2",
        "127.0.0.3"
      ]
    }
  ],
  "services" : [
    {
      "id" : "sample.one",
      "class" : "GSoares\\Test\\DiContainer\\Sample\\One"
    },
    {
      "id" : "sample.two",
      "class" : "GSoares\\Test\\DiContainer\\Sample\\Two",
      "arguments" : [
        "%sample.one%",
        "%database%"
      ]
    },
    {
      "id" : "sample.three",
      "class" : "GSoares\\Test\\DiContainer\\Sample\\Three"
    }
  ]
}
```

Create the container builder: 

```php
$containerCachePath = '/tmp/cache';
$builder = new JsonBuilder($containerCachePath, new JsonValidator(), new JsonDecoder());
```

Create the container: 

- It is possible to get services instances, simple and complex parameters.

```php
$container = $builder->build(['container.json']);
$container->get('environment'); // prod
$container->get('valid-ips'); // array(...)
$container->get('database'); // \stdClass(...)
$container->get('sample.one'); // class GSoares\Test\DiContainer\Sample\\One
```

For local development you can disable container cache before building it 

```php
$container = $builder->disableCache()
    ->build(['container.json']);
```

- Compiling the container will validate services calls. It is recommended to do that before sending container to production.
- Always remove the "ContainerCache.php" file inside the cache directory before deploy your application

```php
$container = $builder->compile(['container.json']);
```