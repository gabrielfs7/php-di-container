# php-di-container

A PHP Dependency injection container based on JSON configuration.

## Sample

Create container.json file

- It it possible to pass multiple json files. For instance, you can create a separated file containing only the "parameters" section.
- The files must have the section "parameters" or "services" as is the example above.

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
      "class" : "GSoares\\Test\\DiContainer\\Sample\\Three",
      "arguments" : [
        "%sample.one%"
      ],
      "call" : [
        {
          "method" : "setTwo",
          "arguments" : [
            "%sample.two%"
          ]
        }
      ]
    },
    {
      "id" : "sample.abstract",
      "class" : "GSoares\\Test\\Sample\\Abstract",
      "abstract" : true,
      "arguments" : [
        "%sample.one%",
        "%sample.two%"
      ],
      "call" : [
        {
          "method" : "setThree",
          "arguments" : [
            "%sample.three%"
          ]
        }
      ]
    },
    {
      "id" : "sample.inheritance.one",
      "class" : "GSoares\\Test\\Sample\\InheritanceOne",
      "parent" : "sample.abstract"
    }
  ]
}
```

Create the container builder: 

```php
$containerCachePath = '/tmp/cache';
$builder = new JsonBuilder($containerCachePath);
```

Create the container: 

- It is possible to get services instances, simple and complex parameters.

```php
$container = $builder->build(['container.json']);
$container->get('environment'); // prod
$container->get('valid-ips'); // array(...)
$container->get('database'); // \stdClass(...)
$container->get('sample.one'); // class GSoares\Test\DiContainer\Sample\\One

# Inheritance
$container->get('sample.inheritance.one')->getOne(); // class GSoares\Test\DiContainer\Sample\\One
$container->get('sample.inheritance.one')->getTwo(); // class GSoares\Test\DiContainer\Sample\\Two
$container->get('sample.inheritance.one')->getThree(); // class GSoares\Test\DiContainer\Sample\\Three
```

- For production you can enable cache and compile it before build.
- It is recommended compile locally before sending to production.
- Always remove "$containerCachePath/ContainerCache.php" before compile. Compile will only work if the cache file was removed.

```php
$container = $builder->enableCache() //will use the cache file only...
    ->enableCompile() //will test if all services are mapped correctly...
    ->build(['container.json']);
```
