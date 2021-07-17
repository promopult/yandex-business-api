# PHP-библиотека API Яндекс.Бизнес

Клиентская библиотека для API сервиса [Яндекс.Бизнес](https://yandex.ru/support/business-priority/index.html)

Ссылка на [документацию API](https://yandex.ru/dev/business-api/doc/ref/index.html)

### Установка 

```bash
$ composer require promopult/yandex-business-api
```
или 
```
"require": {
  // ...
  "promopult/yandex-business-api": "*"
  // ...
}
```

### Примеры
Смотрите папку [examples](/examples).

```php
$client = new \Promopult\YandexBusinessApi\Client(
    new \Promopult\YandexBusinessApi\Config(getenv('__ACCESS_TOKEN__')),
    new \GuzzleHttp\Client() // PSR-18 HTTP-client
);

$response = $client->pingping();

var_dump($response);

// array(1) {
//   ["data"]=>
//   string(8) "pongpong"
// }

```
