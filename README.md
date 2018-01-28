# php-di-container

A PHP Dependency injection container based on JSON configuration.

## Sample

Create your container json files:

- It it possible to pass multiple json files. For instance, you can create a separated file containing only the "parameters" section.
- The files must have the section "parameters" or "services" as is the example above. 
- In the example above we have one file with parameters and other with services. 

#### parameters.json
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
  ]
}
```
  
#### services.json
```javascript
{  
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
    },
    {
      "id" : "sample.inheritance.two",
      "class" : "GSoares\\Test\\Sample\\InheritanceTwo",
      "parent" : "sample.abstract",
      "unique" : true
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
$container = $builder->build(['parameters.json', 'services.json']);
$container->get('environment'); // prod
$container->get('valid-ips'); // array(...)
$container->get('database'); // \stdClass(...)
$container->get('sample.one'); // class GSoares\Test\DiContainer\Sample\\One

# Inheritance
$container->get('sample.inheritance.one')->getOne(); // class GSoares\Test\DiContainer\Sample\\One
$container->get('sample.inheritance.one')->getTwo(); // class GSoares\Test\DiContainer\Sample\\Two
$container->get('sample.inheritance.one')->getThree(); // class GSoares\Test\DiContainer\Sample\\Three

# Unique services have instances created every time "container::get" is called:
$inheritanceOne = $container->get('sample.inheritance.one');
$inheritanceTwo = $container->get('sample.inheritance.two');

$inheritanceOne->setChangeable('test');
$inheritanceTwo->setChangeable('test');

$container->get('sample.inheritance.one')->getChangeable(); //test
$container->get('sample.inheritance.two')->getChangeable(); //null
```

- For production you can enable cache and compile it before build.
- It is recommended compile locally before sending to production.
- Always remove "$containerCachePath/ContainerCache.php" before compile. Compile will only work if the cache file was removed.

```php
$container = $builder->enableCache() //will use the cache file only...
    ->enableCompile() //will test if all services are mapped correctly...
    ->build(['container.json']);
```
