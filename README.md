# php-di-container

A PHP Dependency injection container based on JSON configuration.

## Sample

Create container.json file

```
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

Create container

```
$container = (new JsonBuilder(['container.json'])
    ->build()
    ->getContainer();
    
$container->get('environment'); // prod
$container->get('valid-ips'); // array(...)
$container->get('database'); // \stdClass(...)
$container->get('sample.one'); // class GSoares\Test\DiContainer\Sample\\Two
```